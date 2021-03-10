<?php


namespace App\Traits;


use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

trait GenerateCalendarLink {

    public bool $calendarPresented = false;

    /**
     * @var string
     */
    private static string $format = 'Ymd\TH:i:s\Z';


    /**
     * @param \App\Models\Schedule $schedule
     * @return string
     */
    public function generateGoogleLink(Schedule $schedule): string {
        $location =
            $schedule->url . $schedule->venue_name . ' ' . $schedule->venue_address . ' ' . $schedule->city . ' ' .
            $schedule->country . ' ' . $schedule->post_code;
        $startDate = Carbon::parse($schedule->start_date);
        $endDate = Carbon::parse($schedule->end_date);
        return 'https://www.google.com/calendar/render?action=TEMPLATE&text=' . $schedule->service->title .
               '&details=' . $schedule->title . '&location=' . $location . '&dates=' .
               $startDate->format(self::$format) . '%2F' . $endDate->format(self::$format);

    }


    public function generateIcs(Schedule $schedule): string {
        $calendarName = $schedule->service->title;
        $fileName = Str::slug($calendarName) . date('YmdHis') . '.ics';
        $calendarContent = Calendar::create($calendarName)->event(Event::create($schedule->title)
                                                                       ->startsAt(Carbon::parse($schedule->start_date))
                                                                       ->endsAt(Carbon::parse($schedule->end_date)))
                                   ->get();
        Storage::disk('local')->put($fileName, $calendarContent);
        return $fileName;
    }

}
