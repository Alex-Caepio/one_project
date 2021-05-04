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
        $calendar = !$user->calendar ? new GoogleCalendar(['user_id' => $user->id]) : $user->calendar;
        $calendar->timezone_id = $request->get('timezone_id');
        $calendar->save();
        if ($request->filled('unavailabilities')) {
            UserUnavailabilities::where('practitioner_id', $user->id)->delete();
            $requestData = $request->get('unavailabilities');
            $additionalParams = ['practitioner_id' => $user->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
            $dataToInsert = array_map(function($value) use($additionalParams) {
                $value = array_merge($value, $additionalParams);
                return $value;

            }, $requestData);
            UserUnavailabilities::insert($dataToInsert);
        } else {
            UserUnavailabilities::where('practitioner_id', $user->id)->delete();
        }
        if ($calendar->calendar_id) {
            $gcHelper = new GoogleCalendarHelper($calendar);
            $gcHelper->updateTimezone();
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
        $calendar = !$user->calendar ? new GoogleCalendar(['user_id' => $user->id]) : $user->calendar;

        $gcHelper = new GoogleCalendarHelper();
        $tokenData = $gcHelper->getTokenByAuthCode($request->get('code'));

        $gcHelper->setUserCalendar($calendar);

        Log::info('AUTH TOKEN DATA: ');
        Log::info($tokenData);
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
