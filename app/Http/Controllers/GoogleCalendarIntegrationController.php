<?php


namespace App\Http\Controllers;

use App\Helpers\GoogleCalendarHelper;
use App\Http\Requests\CalendarSettings\AuthRequest;
use App\Http\Requests\CalendarSettings\EventListRequest;
use App\Http\Requests\CalendarSettings\SettingsRequest;
use App\Http\Requests\Request;
use App\Models\GoogleCalendar;
use App\Models\UserUnavailabilities;
use App\Transformers\GoogleCalendarEventTransformer;
use App\Transformers\GoogleCalendarTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class GoogleCalendarIntegrationController extends Controller {

    public function updateSettings(SettingsRequest $request) {
        $user = Auth::user();
        $calendar = !$user->calendar ? GoogleCalendar::createDefaultModel($user) : $user->calendar;
        $calendar->timezone_id = $request->get('timezone_id');
        $calendar->save();
        if ($request->filled('unavailabilities')) {
            UserUnavailabilities::where('practitioner_id', $user->id)->delete();
            $requestData = $request->get('unavailabilities');
            $additionalParams = ['practitioner_id' => $user->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
            $dataToInsert = array_map(static function($value) use($additionalParams) {
                return array_merge($value, $additionalParams);
            }, $requestData);
            UserUnavailabilities::insert($dataToInsert);
        } else {
            UserUnavailabilities::where('practitioner_id', $user->id)->delete();
        }
        if ($calendar->calendar_id) {
            try {
                $gcHelper = new GoogleCalendarHelper($calendar);
                $gcHelper->updateTimezone();
            } catch (\Exception $e) {
                Log::channel('google_authorisation_failed')->info('Unable to update timezone in calendar:', [
                    'calendar_id' => $calendar->calendar_id,
                    'user_id'     => $user->id,
                    'message'     => $e->getMessage()
                ]);
            }
        }
        $calendar->load('unavailabilities');
        return response(fractal($calendar, new GoogleCalendarTransformer())->parseIncludes(['unavailabilities'])->toArray());
    }

    public function getSettings(Request $request) {
        $user = Auth::user();
        $user->load('calendar');
        return response(fractal($user->calendar, new GoogleCalendarTransformer())
                            ->parseIncludes($request->getIncludes())->toArray());
    }

    public function auth(AuthRequest $request) {
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

    public function getEventList(EventListRequest $request) {
        $gcHelper = new GoogleCalendarHelper(Auth::user()->calendar);
        return response(fractal($gcHelper->getEventList(), new GoogleCalendarEventTransformer())
                            ->parseIncludes($request->getIncludes())->toArray());
    }

}
