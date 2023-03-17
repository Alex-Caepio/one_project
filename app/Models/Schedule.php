<?php

namespace App\Models;

use App\Actions\Schedule\GetAvailableAppointmentTimeOnDate;
use App\Scopes\PublishedScope;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @property int $id
 * @property int $service_id
 * @property int $buffer_time
 * @property string $buffer_period
 * @property int $notice_min_time
 * @property string $notice_min_period
 * @property int $deposit_instalments
 * @property int $deposit_instalment_frequency
 * @property bool $deposit_accepted
 * @property float $deposit_amount
 * @property string $deposit_final_date
 *
 * @property-read Service $service
 * @property-read Collection|ScheduleAvailability[] $schedule_availabilities
 * @property-read Collection|ScheduleUnavailability[] $schedule_unavailabilities
 * @property-read Collection|Price[] $prices
 * @property-read Collection|ScheduleFile[] $schedule_files
 *
 * @method static Collection|self[]|self|null find(int|array $ids)
 */
class Schedule extends Model
{
    use HasFactory, SoftDeletes, PublishedScope;

    public const MINS_BUFFER_PERIOD = 'mins';
    public const HOURS_BUFFER_PERIOD = 'hours';
    public const DAYS_BUFFER_PERIOD = 'days';

    const DEPOSIT_DELAY = 14; // in days

    private const DATE_FORMAT = 'd.m.Y';

    protected $fillable = [
        'title',
        'service_id',
        'location_id',
        'start_date',
        'end_date',
        'attendees',
        'cost',
        'comments',
        'city',
        'country_id',
        'post_code',
        'location_displayed',
        'meals_breakfast',
        'meals_lunch',
        'meals_dinner',
        'meals_alcoholic_beverages',
        'meals_dietry_accomodated',
        'refund_terms',
        'deposit_accepted',
        'deposit_amount',
        'deposit_instalments',
        'deposit_instalment_frequency',
        'deposit_final_date',
        'booking_message',
        'url',
        'book_full_series',
        'accomodation',
        'accomodation_details',
        'travel',
        'travel_details',
        'repeat',
        'repeat_every',
        'repeat_period',
        'notice_min_time',
        'notice_min_period',
        'buffer_time',
        'buffer_period',
        'address',
        'appointment',
        'venue_name',
        'venue_address',
        'within_kilometers',
        'deleted_at',
        'is_published'
    ];

    private array $relatedChanges = [];

    protected $casts = [
        'is_published' => 'boolean',
        'meals_breakfast' => 'boolean',
        'meals_lunch' => 'boolean',
        'meals_dinner' => 'boolean',
        'meals_alcoholic_beverages' => 'boolean',
        'meals_dietry_accomodated' => 'boolean',
        'deposit_accepted' => 'boolean',
        'deposit_final_date' => 'datetime:Y-m-d H:i:s',
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function media_files()
    {
        return $this->morphMany(MediaFile::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function schedule_availabilities()
    {
        return $this->hasMany(ScheduleAvailability::class);
    }

    public function schedule_unavailabilities()
    {
        return $this->hasMany(ScheduleUnavailability::class);
    }

    public function schedule_files()
    {
        return $this->hasMany(ScheduleFile::class);
    }

    public function schedule_hidden_files()
    {
        return $this->hasMany(ScheduleHiddenFile::class);
    }

    public function freezes(): HasMany
    {
        return $this->hasMany(ScheduleFreeze::class);
    }

    public function getUserUnavailabilities()
    {
        $userUnavailabilities = UserUnavailabilities::query()
            ->where('practitioner_id', $this->service->user_id )
            ->where('end_date', '>', now() )
            ->get();

        return $userUnavailabilities;
    }

    public function rescheduleRequests()
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    public function getOutsiderBookings()
    {
        $availabilities = $this->schedule_availabilities;
        $unavailabilities = $this->schedule_unavailabilities;
        $q = $this->bookings();

        foreach ($availabilities as $availability) {
            switch ($availability->days) {
                case 'everyday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where("TIME(datetime_from) >= '{$availability->start_time}'")->where(
                                "TIME(datetime_from) <= '{$availability->end_time}'"
                            );
                        }
                    );
                    break;
                case 'weekends':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->whereIn(DB::raw('WEEKDAY(datetime_from)'), [5, 6])->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'weekdays':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->whereIn(DB::raw('WEEKDAY(datetime_from)'), [0, 1, 2, 3, 4])->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'monday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 0)->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'tuesday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 1)->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'wednesday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 2)->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'thursday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 3)->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'friday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 4)->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'saturday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 5)->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
                case 'sunday':
                    $q->where(
                        function ($q) use ($availability) {
                            $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 6)->whereRaw(
                                "TIME(datetime_from) >= '{$availability->start_time}'"
                            )->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                        }
                    );
                    break;
            }
        }

        foreach ($unavailabilities as $unavailability) {
            $q->whereNotBetween('datetime_from', [$unavailability->start_date, $unavailability->end_date]);
        }

        return $this->bookings()->whereNotIn('id', $q->pluck('id'))->whereNotIn('status', Booking::getInactiveStatuses())
            ->get();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function hasContractualChanges(): bool
    {
        $changes = $this->getRealChangesList();
        // The Following Data Fields are considered Contractual Terms [Start Date], [Start Time], [End Date], [End Time], [City] and [Country]
        unset(
            $changes['location_displayed'],
            $changes['venue_name'],
            $changes['appointment'],
            $changes['comments'],
            $changes['title'],
            $changes['attendees'],
            $changes['meals_alcoholic_beverages'],
            $changes['meals_breakfast'],
            $changes['meals_dietry_accomodated'],
            $changes['meals_dinner'],
            $changes['meals_lunch'],
            $changes['is_schedule_files_updated'],
        );

        return count($changes);
    }

    public function hasNonContractualChanges(): bool
    {
        $changes = $this->getRealChangesList();

        if (empty($changes)) {
            return false;
        }

        if ($this->service->service_type_id === Service::TYPE_BESPOKE) {
            return true;
        }

        // Unset, because another event will be fired for Reschedule Request
        unset(
            $changes['end_date'],
            $changes['start_date'],
            $changes['location_id'],
            $changes['venue'],
            $changes['city'],
            $changes['post_code'],
            $changes['country_id'],
        );

        return count($changes) || $this->hasRelatedChanges();
    }

    public function getRealChangesList(): array
    {
        $changes = $this->getChanges();

        unset($changes['updated_at'], $changes['created_at'], $changes['is_published'], $changes['deleted_at']);

        if (isset($changes['start_date']) && !empty($changes['start_date'])) {
            $startDate = Carbon::parse($changes['start_date'])->format('Y-m-d H:i:s');
            if ($startDate === (string)$this->getOriginal('start_date')) {
                unset($changes['start_date']);
            }
        }

        if (isset($changes['end_date']) && !empty($changes['end_date'])) {
            $endDate = Carbon::parse($changes['end_date'])->format('Y-m-d H:i:s');
            if ($endDate === (string)$this->getOriginal('end_date')) {
                unset($changes['end_date']);
            }
        }

        if (isset($changes['deposit_final_date']) && !empty($changes['deposit_final_date'])) {
            $depositFinal = Carbon::parse($changes['deposit_final_date'])->format('Y-m-d H:i:s');
            if ($depositFinal === (string)$this->getOriginal('deposit_final_date')) {
                unset($changes['deposit_final_date']);
            }
        }

        return $changes;
    }

    public function getCalculatedStatus(): string
    {
        if (!$this->end_date || !$this->start_date) {
            return 'ongoing';
        }

        if (Carbon::parse($this->end_date) < now()) {
            return 'completed';
        }
        if (Carbon::parse($this->start_date) > now()) {
            return 'upcoming';
        }

        return '';
    }

    public function isSoldOut(): bool
    {
        return $this->getAvailableTicketsCount() === 0;
    }

    public function getAvailableTicketsCount(): int
    {
        $time = Carbon::now()->subMinutes(15);
        $purchased = Booking::where('schedule_id', $this->id)->uncanceled()->sum('amount');
        $personalFreezed = ScheduleFreeze::query()
            ->where('schedule_id', $this->id)
            ->where('user_id', Auth::id())
            ->where('freeze_at', '>', $time->toDateTimeString())
            ->count();
        $freezed =
            ScheduleFreeze::query()
                ->where('schedule_id', $this->id)
                ->where('freeze_at', '>', $time->toDateTimeString())
                ->count();
        $available = (int)$this->attendees - ($purchased + $freezed - $personalFreezed);

        return $available < 0 ? 0 : $available;
    }

    public function isInstallmentAvailable(): bool
    {
        if (!$this->deposit_accepted) {
            return false;
        }

        if ($this->service->service_type_id === Service::TYPE_BESPOKE) {
            return true;
        }

        $dateNow = Carbon::now();
        $dateFinal = Carbon::parse($this->deposit_final_date);

        //can't put installments on expired schedules
        return !$dateNow->isAfter($dateFinal);
    }

    public function getInstallmentPeriods(): array
    {
        $periods = [];
        if ($this->service->service_type_id === Service::TYPE_BESPOKE) {
            $periods[] = $this->deposit_instalments;
        } else {
            $periods = range(
                1,
                intdiv(
                    Carbon::parse($this->deposit_final_date)
                        ->diffInDays(Carbon::now()->addDays(self::DEPOSIT_DELAY)),
                    self::DEPOSIT_DELAY
                )
                + 1
            );
        }

        return $periods;
    }

    public function calculateInstallmentsCalendar(float $cost, int $amount = 1, int $periods = 1): array
    {
        $calendar = [];
        $installmentInfo = $this->getInstallmentInfo($cost, $amount, $periods);

        if ($installmentInfo !== null && $this->service->service_type_id === Service::TYPE_BESPOKE) {
            $start = Carbon::now()->addDays($this->deposit_instalment_frequency);
            $amountPerPeriod = round($installmentInfo['amountPerPeriod'], 2);
            $calendar[$start->format(self::DATE_FORMAT)] = $amountPerPeriod;
            $date = $start;

            for ($i = 0; $i < $this->deposit_instalments - 1; $i++) {
                $date = $date->addDays($this->deposit_instalment_frequency);
                $calendar[$date->format(self::DATE_FORMAT)] = $amountPerPeriod;
            }
        } elseif ($installmentInfo !== null) {
            $calendar = $this->calculateInternalCalendar($installmentInfo);
        }

        $calendar = array_reverse($calendar);

        Log::channel('stripe_plans_info')
            ->info('calculateInstallmentsCalendar', [
                'installmentInfo' => $installmentInfo,
                'calendar' => $calendar,
            ]);

        return $calendar;
    }

    public function getInstallmentInfo(float $cost, int $amount = 1, int $periods = 1): ?array
    {
        $result = null;
        if ($this->isInstallmentAvailable()) {
            $depositInitialValue = $this->deposit_amount;
            $furtherPayments = $cost - $depositInitialValue * $amount;
            if ($furtherPayments > 0) {
                if ($this->service->service_type_id === Service::TYPE_BESPOKE) {
                    $installmentPeriodDays = $this->deposit_instalment_frequency;
                    $installmentFirstDate = Carbon::now()->addDays($installmentPeriodDays);
                    $installmentCancelDate = Carbon::now()->addDays(
                        $installmentPeriodDays + $this->deposit_instalments * $installmentPeriodDays
                    );
                    $installmentLastDate = Carbon::now()->addDays(
                        $installmentPeriodDays + ($this->deposit_instalments - 1) * $installmentPeriodDays
                    );
                } else {
                    $installmentLastDate = Carbon::parse($this->deposit_final_date, 'UTC');
                    $installmentTotalDays = CarbonPeriod::create(Carbon::now('UTC'), $installmentLastDate)->days()->count();
                    $installmentPeriods = $periods;
                    $installmentPeriodDays = floor($installmentTotalDays / $installmentPeriods);
                    $installmentCancelDate =  Carbon::parse($this->deposit_final_date, 'UTC')->addDays($installmentPeriodDays);

                    $calendarCurrentDate = Carbon::parse($installmentLastDate, 'UTC');
                    for ($i = $installmentPeriods; $i > 0; $i--) {
                        $calendar[] = $calendarCurrentDate->toDateTimeString();
                        $calendarCurrentDate->subDays($installmentPeriodDays);
                    }

                    $installmentFirstDate = Carbon::parse(last($calendar), 'UTC');
                }
                $amountPerPeriod = (float)($furtherPayments / $periods);

                $result = [
                    'calendar' => $calendar ?? [],
                    'totalAmount' => $cost,
                    'furtherPayments' => $furtherPayments,
                    'amountPerPeriod' => $amountPerPeriod,
                    'daysPerPeriod' => $installmentPeriodDays,
                    'startPaymentDate' => $installmentFirstDate,
                    'finalPaymentDate' => $installmentLastDate,
                    'installmentFirstDate' => $installmentFirstDate,
                    'installmentLastDate' => $installmentLastDate,
                    'installmentCancelDate' => $installmentCancelDate,
                    'installmentTotalDays' => $installmentTotalDays ?? null,
                    'installmentPeriods' => $installmentPeriods ?? null,
                    'installmentPeriodDays' => $installmentPeriodDays,
                ];
            }
        }

        return $result;
    }

    private function calculateInternalCalendar($installmentInfo = []): array
    {
        $calendar = [];

        if (!$installmentInfo) {
            return $calendar;
        }

        $amountPerPeriod = round($installmentInfo['amountPerPeriod'], 2);
        $calendarCurrentDate = Carbon::parse($installmentInfo['finalPaymentDate'], 'UTC');

        for ($i = $installmentInfo['installmentPeriods']; $i > 0; $i--) {
            $calendar[$calendarCurrentDate->format(self::DATE_FORMAT)] = $amountPerPeriod;
            $calendarCurrentDate->subDays($installmentInfo['installmentPeriodDays']);
        }

        return $calendar;
    }

    /**
     * Returns a notice time in minutes.
     *
     * @return int
     */
    public function getNoticeTime(): int
    {
        return $this->timeToMinutes($this->notice_min_period, $this->notice_min_time);
    }

    /**
     * Returns a buffer time in minutes.
     *
     * @return int
     */
    public function getBufferTime(): int
    {
        return $this->timeToMinutes($this->buffer_perio ?? 'mins', $this->buffer_time);
    }

    private function timeToMinutes(string $type, int $time): int
    {
        switch ($type) {
            case 'hours':
                $multiplier = 60;
                break;
            case 'days':
                $multiplier = 60 * 24;
                break;
            case 'mins':
            default:
                $multiplier = 1;
                break;
        }

        return $time * $multiplier;
    }

    public function hasRelatedChanges(): bool
    {
        return count($this->relatedChanges);
    }

    /**
     * Resets statuses of the related changes.
     *
     * @return void
     */
    public function resetUpdateStatuses(): void
    {
        $this->relatedChanges = [];
    }

    /**
     * Checks the given files are different with the current schedule files.
     *
     * @param array $files Files with URLs.
     */
    public function areDifferentFiles(array $files): bool
    {
        $newFiles = array_column($files, 'url');
        $oldFiles = $this->schedule_files->pluck('url')->toArray();

        return count(array_diff($newFiles, $oldFiles)) || count(array_diff($oldFiles, $newFiles));
    }

    /**
     * Updates schedule files if it is necessary.
     *
     * @param array $files Files with URLs.
     */
    public function updateScheduleFiles(array $files): self
    {
        if ($this->areDifferentFiles($files)) {
            $this->schedule_files()->delete();
            $this->schedule_files()->createMany($files);

            $this->relatedChanges['schedule_files'] = true;
        }

        return $this;
    }

    // method for checking available time in the days where we have customers unavailability.
    public function getUnavailableDays( string $timeZone ){
        $resultCollectionDates = new \Illuminate\Support\Collection();

        $userUnavailabilities = $this->getUserUnavailabilities();
        if( empty( $userUnavailabilities ) ) return $resultCollectionDates;

        $datesForCheck = [];
        /* @var UserUnavailabilities  $userUnavailability*/
        foreach ( $userUnavailabilities as $userUnavailability ){
            $startDate = Carbon::parse( $userUnavailability->start_date );
            $endDate = Carbon::parse( $userUnavailability->end_date );

            $datesForCheck[ $startDate->format('Y-m-d') ] = $startDate->format('Y-m-d');

            $diff = $endDate->diffInDays( $startDate );
            for( $i = 0; $i <= $diff; $i++ ){

                $nextDate = $startDate->addDay();
                $datesForCheck[ $nextDate->format('Y-m-d') ] = $nextDate->format('Y-m-d');
            }
        }

        if( !empty( $this->prices ) ){
            $isAvailableDay = false;
            foreach ( $datesForCheck as $date ){
                foreach ( $this->prices as $price ) {
                    $times = run_action(GetAvailableAppointmentTimeOnDate::class, $price, $date, $timeZone);
                    if (!empty($times)) {
                        $isAvailableDay = true;
                        break;
                    }
                }
                if( $isAvailableDay === true ){
                    break;
                }
                $resultCollectionDates->add( Carbon::parse( $date ) );
            }
        }

        return $resultCollectionDates;
    }
}
