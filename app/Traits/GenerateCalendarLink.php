<?php


namespace App\Traits;


use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

trait GenerateCalendarLink
{

    public bool $calendarPresented = false;

    /**
     * @var string
     */
    private static string $format = 'Ymd\TH:i\Z';


    /**
     * @param Schedule $schedule
     * @return string
     */
    public function generateGoogleLink(Schedule $schedule): string
    {
        $locationData = array_filter([
            $schedule->url,
            $schedule->venue_name,
            $schedule->venue_address,
            $schedule->city,
            $schedule->country ? $schedule->country->nicename : '',
            $schedule->post_code
        ], static function ($value) {
            return !empty(trim($value));
        });
        $location = urlencode(implode(', ', $locationData));
        $startDate = Carbon::parse($schedule->start_date);
        $endDate = Carbon::parse($schedule->end_date);
        return 'https://www.google.com/calendar/render?action=TEMPLATE&text=' . $schedule->service->title .
            '&details=' . urlencode($schedule->title) . '&location=' . $location . '&dates=' .
            $startDate->format(self::$format) . '%2F' . $endDate->format(self::$format);
    }


    public function generateIcs(Schedule $schedule, User $practitioner): string
    {
        $calendarName = $schedule->service->title;
        $fileName = Str::slug($calendarName) . date('YmdHis') . '.ics';
        $calendarContent = Calendar::create($calendarName)
            ->event(
                Event::create($schedule->title)
                    ->startsAt(Carbon::parse($schedule->start_date))
                    ->endsAt(Carbon::parse($schedule->end_date))
                    ->organizer(
                        $practitioner->business_email,
                        $practitioner->business_name
                    )
            )
            ->get();
        Storage::disk('local')->put($fileName, $calendarContent);
        return $fileName;
    }

}
