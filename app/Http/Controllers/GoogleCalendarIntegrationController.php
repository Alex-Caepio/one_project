<?php

namespace App\Http\Controllers;

use App\Helpers\GoogleCalendarHelper;
use App\Http\Requests\CalendarSettings\AuthRequest;
use App\Http\Requests\CalendarSettings\EventListRequest;
use App\Http\Requests\CalendarSettings\SettingsRequest;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\GoogleCalendar;
use App\Models\Service;
use App\Models\UserUnavailabilities;
use App\Transformers\CalendarEventTransformer;
use App\Transformers\GoogleCalendarTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarIntegrationController extends Controller
{
    public function updateSettings(SettingsRequest $request)
    {
        $user = Auth::user();
        $calendar = !$user->calendar ? GoogleCalendar::createDefaultModel($user) : $user->calendar;
        $calendar->timezone_id = $request->get('timezone_id');
        $calendar->save();

        if ($request->filled('unavailabilities')) {
            UserUnavailabilities::where('practitioner_id', $user->id)->delete();
            $now = Carbon::now()->toDateTimeString();
            $dataToInsert = collect($request->get('unavailabilities'))->map(function ($value) use ($now, $user) {
                return [
                    'practitioner_id' => $user->id,
                    'start_date' => Carbon::parse($value['start_date'])->toDateTimeString(),
                    'end_date' => Carbon::parse($value['end_date'])->toDateTimeString(),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            });
            UserUnavailabilities::insert($dataToInsert->toArray());
        } else {
            UserUnavailabilities::where('practitioner_id', $user->id)->delete();
        }

        if ($calendar->calendar_id) {
            try {
                $gcHelper = new GoogleCalendarHelper($calendar);
                $gcHelper->updateTimezone();
            } catch (Exception $e) {
                Log::channel('google_authorisation_failed')
                    ->error('Unable to update timezone in calendar:', [
                        'calendar_id' => $calendar->calendar_id,
                        'user_id' => $user->id,
                        'message' => $e->getMessage()
                    ]);
            }
        }
        $calendar->load('unavailabilities');

        return response(
            fractal($calendar, new GoogleCalendarTransformer())->parseIncludes(['unavailabilities'])->toArray()
        );
    }

    public function getSettings(Request $request)
    {
        $user = Auth::user();
        $user->load('calendar');

        return response(
            fractal($user->calendar, new GoogleCalendarTransformer())
                ->parseIncludes($request->getIncludes())->toArray()
        );
    }

    public function auth(AuthRequest $request)
    {
        $user = Auth::user();
        $calendar = !$user->calendar ? GoogleCalendar::createDefaultModel($user) : $user->calendar;

        $gcHelper = new GoogleCalendarHelper(null);
        $tokenData = $gcHelper->getTokenByAuthCode($request->get('code'));

        $gcHelper->setUserCalendar($calendar);

        //Log::info('AUTH TOKEN DATA: ');
        //Log::info($tokenData);
        if ($gcHelper->storeNewUserTokens($tokenData)) {
            return response('', 204);
        }
        abort(500, 'Google authorization failed');
    }

    public function getEventList(EventListRequest $request)
    {
        $bookings = Booking::query()
            ->with([
                'schedule:service_id,id,location_displayed,title',
                'user:id,first_name,last_name'
            ])
            ->where('practitioner_id', '=', Auth::id())
            ->whereIn('status', Booking::getActualStatuses())
            ->where(function (Builder $builder) use ($request) {
                return $builder
                    ->whereBetween('datetime_from', [$request->first_date_point, $request->last_date_point])
                    ->orWhereBetween('datetime_to', [$request->first_date_point, $request->last_date_point]);
            })
            ->whereHas('schedule', function (Builder $builder) {
                return $builder
                    ->whereHas('service', function (Builder $builder) {
                        return $builder
                            ->where('service_type_id', Service::TYPE_APPOINTMENT)
                            ->select('id');
                    })
                    ->select('id');
            })
            ->get();

        return response(
            fractal($bookings, new CalendarEventTransformer())
                ->parseIncludes($request->getIncludes())->toArray()
        );
    }
}
