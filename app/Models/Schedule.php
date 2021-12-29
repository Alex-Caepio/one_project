<?php

namespace App\Models;

use App\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Schedule
 *
 * @property int id
 * @property int buffer_time
 * @property int deposit_instalments
 * @property int deposit_instalment_frequency
 * @property bool deposit_accepted
 * @property float deposit_amount
 * @property string deposit_final_date
 * @property Service service
 */
class Schedule extends Model
{
    use HasFactory, SoftDeletes, PublishedScope;

    const DEPOSIT_DELAY = 14; // in days

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
        return $this->belongsTo(Service::class);
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

        return $this->bookings()->whereNotIn('id', $q->pluck('id'))->whereNotIn('status', ['completed', 'canceled'])
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
            $changes['venue_address'],
            $changes['venue_name'],
            $changes['post_code'],
            $changes['appointment'],
            $changes['comments'],
            $changes['title'],
            $changes['attendees'],
            $changes['meals_alcoholic_beverages'],
            $changes['meals_breakfast'],
            $changes['meals_dietry_accomodated'],
            $changes['meals_dinner'],
            $changes['meals_lunch'],
        );

        return count($changes) > 0;
    }

    public function hasNonContractualChanges(): bool
    {
        $changes = $this->getRealChangesList();

        $result = false;
        if (count($changes)) {
            if ($this->service->service_type_id === 'bespoke') {
                $result = true;
            } else {
                // Unset, because another event will be fired for Reschedule Request
                unset(
                    $changes['end_date'],
                    $changes['start_date'],
                    $changes['location_id'],
                    $changes['venue'],
                    $changes['city'],
                    $changes['country_id'],
                );
                $result = count($changes) > 0;
            }
        }

        return $result;
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

        if ($this->service->service_type_id === 'bespoke') {
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
        if ($this->service->service_type_id === 'bespoke') {
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

    public function calculateInstallmentsCalendar($amount, int $periods = 1): array
    {
        $calendar = [];
        $installmentInfo = $this->getInstallmentInfo($amount, $periods);

        if ($installmentInfo !== null) {
            if ($this->service->service_type_id === Service::TYPE_BESPOKE) {
                $start = Carbon::now()->addDays(self::DEPOSIT_DELAY);
                $amountPerPeriod = round($installmentInfo['amountPerPeriod'], 2);
                $calendar[$start->format('d.m.Y')] = $amountPerPeriod;
                $date = $start;
                for ($i = 0; $i < $this->deposit_instalments - 1; $i++) {
                    $date = $date->addDays($this->deposit_instalment_frequency);
                    $calendar[$date->format('d.m.Y')] = $amountPerPeriod;
                }
            } else {
                /** @var Carbon $depositFinalDate */
                $depositFinalDate = $installmentInfo['finalPaymentDate'];
                $amountPerPeriod = round($installmentInfo['amountPerPeriod'], 2);
                $p = $depositFinalDate;
                while ($p->isFuture() && $periods > 0) {
                    $calendar[$p->format('d.m.Y')] = $amountPerPeriod;
                    $p = $depositFinalDate->subDays(self::DEPOSIT_DELAY);
                    $periods--;
                }
            }
        }

        return $calendar;
    }

    public function getInstallmentInfo($amount, int $periods = 1): ?array
    {
        $result = null;
        if ($this->isInstallmentAvailable()) {
            $depositInitialValue = $this->deposit_amount;
            $furtherPayments = $amount - $depositInitialValue;
            if ($furtherPayments > 0) {
                if ($this->service->service_type_id === Service::TYPE_BESPOKE) {
                    $daysPerPeriod = $this->deposit_instalment_frequency;
                    $depositStartDate = Carbon::now()->addDays(self::DEPOSIT_DELAY);
                    $depositFinalDate = Carbon::now()->addDays(
                        self::DEPOSIT_DELAY + ($this->deposit_instalments - 1) * $daysPerPeriod
                    );
                } else {
                    $depositFinalDate = $this->deposit_final_date;
                    $installmentPeriods = $periods;
                    $date = Carbon::createFromFormat("Y-m-d H:i:s", $this->deposit_final_date)
                        ->setTimezone('UTC');
                    $minStart = Carbon::now()->addDays(self::DEPOSIT_DELAY);
                    $calendar = [];
                    while ($date->diffInDays($minStart) > 0 && $installmentPeriods > 0) {
                        $calendar[] = $date->toDateString();
                        $date = $date->subDays(self::DEPOSIT_DELAY);
                        $installmentPeriods--;
                    }
                    $depositStartDate = Carbon::parse(end($calendar));
                    $daysPerPeriod = self::DEPOSIT_DELAY;
                }
                $amountPerPeriod = (float)($furtherPayments / $periods);
                $result = [
                    'daysPerPeriod' => $daysPerPeriod,
                    'amountPerPeriod' => $amountPerPeriod,
                    'startPaymentDate' => $depositStartDate,
                    'finalPaymentDate' => $depositFinalDate
                ];
            }
        }

        return $result;
    }
}
