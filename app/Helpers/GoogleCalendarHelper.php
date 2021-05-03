<?php


namespace App\Helpers;


use App\Events\AppointmentBooked;
use App\Models\GoogleCalendar;
use Carbon\Carbon;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class GoogleCalendarHelper {

    private \Google_Client $_client;
    private ?\Google_Service_Calendar $_service = null;
    private ?GoogleCalendar $_calendar = null;

    public function __construct(?GoogleCalendar $calendar) {
        $this->_client = new \Google_Client();
        $this->_client->setApplicationName(config('app.platform_name'));
        $this->_client->setAuthConfig(config('google-calendar.auth_profiles.service_account.credentials_json'));
        $this->_client->setAccessType("offline");
        $this->_client->setIncludeGrantedScopes(true);
        $this->_client->setApprovalPrompt('force');
        $this->_client->addScope(\Google_Service_Calendar::CALENDAR);
        $this->_client->setRedirectUri(config('google-calendar.calendar_redirect_uri'));
        if ($calendar instanceof GoogleCalendar) {
            $this->setUserCalendar($calendar);
        }
        $this->setAccessToken();
    }

    public function setUserCalendar(GoogleCalendar $calendar): void {
        $this->_calendar = $calendar;
    }

    public function setAccessToken(): void {
        if (!$this->_calendar->access_token) {
            return;
        }
        $this->_client->setAccessToken([
                                           'access_token'  => $this->_calendar->access_token,
                                           'refresh_token' => $this->_calendar->refresh_token
                                       ]);

        if ($this->_client->isAccessTokenExpired()) {
            Log::info('Google Authorization Token Expired');
            $refreshToken = $this->_client->getRefreshToken();
            if ($refreshToken !== null) {
                $newCredentials = $this->_client->fetchAccessTokenWithRefreshToken($refreshToken);
                Log::info('NEW CREDENTIALS: ');
                Log::info($newCredentials);
                $this->updateUserTokens($newCredentials);
                return;
            }
            Log::channel('google_authorisation_failed')->info('Unable to refresh token:', [
                'user_id'       => $this->_calendar->user_id,
                'expired_at'    => $this->_calendar->expired_at,
                'refresh_token' => $this->_calendar->refresh_token,
                'access_token'  => $this->_calendar->access_token
            ]);
            throw new \Exception('Google Calendar Integration in not valid');
        }
    }

    public function updateUserTokens(array $accessToken): bool {
        $logData = array_merge(['user_id' => $this->_calendar->user_id], $accessToken);
        Log::info($accessToken);
        if (isset($accessToken['access_token'])) {
            $this->_calendar->access_token = $accessToken['access_token'];
            $this->_calendar->refresh_token = $accessToken['refresh_token'] ?? null;
            $this->_calendar->expired_at = Carbon::now()->addSeconds($accessToken['expires_in']);
            $this->_calendar->save();
            Log::channel('google_authorisation_success')->info('Authorisation Token Success:', $logData);
            return true;
        }
        Log::channel('google_authorisation_failed')->info('Unable to update access token:', $logData);
        return false;
    }

    public function getTokenByAuthCode(string $code): array {
        return $this->_client->fetchAccessTokenWithAuthCode($code);
    }

    public function getService(): \Google_Service_Calendar {
        if (!$this->_service) {
            $this->_service = new \Google_Service_Calendar($this->_client);
        }
        return $this->_service;
    }

    public function getEventList(array $params = []) {
        if (!$this->_calendar->calendar_id) {
            $this->updateUserCalendar();
        }
        $calendarService = $this->getService();
        $eventList = $calendarService->events->listEvents($this->_calendar->calendar_id);
        return $eventList->getItems();
    }

    private function updateUserCalendar(): void {
        $this->_calendar->calendar_id = $this->findCalendarId();
        $this->_calendar->save();
    }

    private function findCalendarId(): string {
        $service = $this->getService();
        $calendarEntry = null;
        $calendarList = $service->calendarList->listCalendarList();

        while (true) {
            foreach ($calendarList->getItems() as $calendarListEntry) {
                if ($calendarListEntry->getSummary() === config('app.platform_calendar')) {
                    $calendarEntry = $calendarListEntry;
                    break;
                }
            }
            $pageToken = $calendarList->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $calendarList = $service->calendarList->listCalendarList($optParams);
            } else {
                break;
            }
        }
        if ($calendarEntry === null) {
            $calendarId = $this->createNewCalendar();
        } else {
            $calendarId = $calendarEntry->getId();
        }

        return $calendarId;
    }

    private function createNewCalendar(): \Google_Service_Calendar_Calendar {
        $newCalendar = new \Google_Service_Calendar_Calendar();
        $newCalendar->setSummary(config('app.platform_calendar'));
        $newCalendar->setTimeZone($this->_calendar->timezone->getGMTCalendarValue());
        $result = $this->getService()->calendars->insert($newCalendar);
        return $result->getId();
    }

    private function getUserCalendar(): ?\Google_Service_Calendar_Calendar {
        if (!$this->_calendar->calendar_id) {
            return null;
        }
        return $this->getService()->calendars->get($this->_calendar->calendar_id);
    }

    public function updateTimezone(): bool {
        if (!$this->_calendar->calendar_id) {
            Log::channel('google_calendar_failed')->info('Unable to update timezone. Calendar not linked:', [
                'user_id'     => $this->_calendar->user_id,
                'id'          => $this->_calendar->id,
                'timezone_id' => $this->_calendar->timezone_id
            ]);
            return false;
        }
        $gCal = $this->getUserCalendar();
        $gCal->setTimeZone($this->_calendar->timezone->getGMTCalendarValue());
        $this->getService()->calendars->update($this->_calendar->calendar_id, $gCal);
        return true;
    }

    public function setEvent(AppointmentBooked $event) {
        $booking = $event->booking;

        $startTime = Carbon::parse($booking->datetime_from);
        $endTime = Carbon::parse($booking->datetime_to);

        $googleStartTime = new Google_Service_Calendar_EventDateTime();
        $googleStartTime->setTimeZone($this->_calendar->timezone->getGMTCalendarValue());
        $googleStartTime->setDateTime($startTime->format('c'));

        $googleEndTime = new Google_Service_Calendar_EventDateTime();
        $googleEndTime->setTimeZone($this->_calendar->timezone->getGMTCalendarValue());
        $googleEndTime->setDateTime($endTime->format('c'));

        $gEvent = new \Google_Service_Calendar_Event();
        $gEvent->setStart($googleStartTime);
        $gEvent->setEnd($googleEndTime);
        $gEvent->setSummary($event->service->title.' - '.$event->schedule->title);
        $gEvent->setLocation($event->schedule->venue_address.', '.$event->schedule->city.', '.$event->schedule->country.', '.$event->schedule->post_code);

        $attendee = new \Google_Service_Calendar_EventAttendee();
        $attendee->setDisplayName($event->client->first_name.' '.$event->client->last_name);
        $attendee->setEmail($event->client->email);

        $gEvent->setAttendees([$attendee]);
        $createdEvent = $this->getService()->events->insert($this->_calendar->calendar_id, $gEvent);

        if (!empty($createdEvent->id)) {
            Log::channel('google_calendar_success')->info('Google event successfully created', [
                'booking_id'  => $booking->id,
                'calendar_id' => $this->_calendar->id,
                'event_id'    => $createdEvent->id
            ]);
        } else {
            Log::channel('google_calendar_failed')->info('Unable to сreate event', [
                'booking_id'  => $booking->id,
                'calendar_id' => $this->_calendar->id,
                'message'     => 'Empty response'
            ]);
        }
    }

}
