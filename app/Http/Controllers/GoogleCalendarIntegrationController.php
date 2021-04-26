<?php


namespace App\Http\Controllers;

use App\Helpers\GoogleCalendarHelper;
use App\Http\Requests\CalendarSettings\AuthRequest;
use App\Http\Requests\CalendarSettings\EventListRequest;
use App\Http\Requests\CalendarSettings\SettingsRequest;
use App\Http\Requests\Request;
use App\Models\GoogleCalendar;
use App\Transformers\GoogleCalendarTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class GoogleCalendarIntegrationController extends Controller {

    public function updateSettings(SettingsRequest $request) {
        $user = Auth::user();
        $calendar = !$user->calendar ? new GoogleCalendar(['user_id' => $user->id]) : $user->calendar;
        $calendar->timezone_id = $request->get('timezone_id');
        // add availabilities save
        $calendar->save();
        return response(fractal($calendar, new GoogleCalendarTransformer())->toArray());
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
        $gcHelper = new GoogleCalendarHelper($calendar);
        if ($gcHelper->updateUserTokens($gcHelper->getTokenByAuthCode($request->get('code')))) {
            return response('', 204);
        }
        abort(500, 'Google authorization failed');
    }


    public function getEventList(EventListRequest $request) {
        $gcHelper = new GoogleCalendarHelper(Auth::user()->calendar);
        return response()->json($gcHelper->getEventList());
    }

    public function addEvent(Request $request) {

    }


}
