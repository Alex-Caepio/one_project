<?php

namespace App\Models;

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
 * @property bool deposit_accepted
 * @property float deposit_amount
 * @property string deposit_final_date
 * @property Service service
 */
class Schedule extends Model {
    use HasFactory, SoftDeletes;

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
        'country',
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
    ];

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function prices() {
        return $this->hasMany(Price::class);
    }

    public function service() {
        return $this->belongsTo(Service::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function isSoldOut(): bool {
        $time = Carbon::now()->subMinutes(15);
        $purchased = Booking::where('schedule_id', $this->id)->count();
        $personalFreezed = ScheduleFreeze::where('schedule_id', $this->id)->where('user_id', Auth::id())
                                         ->where('freeze_at', '>', $time->toDateTimeString())->count();
        $freezed = ScheduleFreeze::where('schedule_id', $this->id)->where('freeze_at', '>', $time->toDateTimeString())
                                 ->count();

        if (isset($personalFreezed)) {
            return $this->attendees <= ($purchased + $freezed - $personalFreezed);
        }

        return $this->attendees <= ($purchased + $freezed);

    }

    public function media_files() {
        return $this->morphMany(MediaFile::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function schedule_availabilities() {
        return $this->hasMany(ScheduleAvailability::class);
    }

    public function schedule_unavailabilities() {
        return $this->hasMany(ScheduleUnavailability::class);
    }

    public function schedule_files() {
        return $this->hasMany(ScheduleFile::class);
    }

    public function schedule_hidden_files() {
        return $this->hasMany(ScheduleHiddenFile::class);
    }

    public function freezes(): HasMany {
        return $this->hasMany(ScheduleFreeze::class);
    }

    public function rescheduleRequests() {
        return $this->hasMany(RescheduleRequest::class);
    }

    public function getOutsiderBookings() {
        $availabilities = $this->schedule_availabilities;
        $unavailabilities = $this->schedule_unavailabilities;
        $q = $this->bookings();

        foreach ($availabilities as $availability) {
            switch ($availability->days) {
                case 'everyday':
                    $q->where(function($q) use ($availability) {
                        $q->where("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->where("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'weekends':
                    $q->where(function($q) use ($availability) {
                        $q->whereIn(DB::raw('WEEKDAY(datetime_from)'), [5, 6])
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'weekdays':
                    $q->where(function($q) use ($availability) {
                        $q->whereIn(DB::raw('WEEKDAY(datetime_from)'), [0, 1, 2, 3, 4])
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'monday':
                    $q->where(function($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 0)
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'tuesday':
                    $q->where(function($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 1)
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'wednesday':
                    $q->where(function($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 2)
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'thursday':
                    $q->where(function($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 3)
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'friday':
                    $q->where(function($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 4)
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'saturday':
                    $q->where(function($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 5)
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'sunday':
                    $q->where(function($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 6)
                          ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                          ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
            }
        }

        foreach ($unavailabilities as $unavailability) {
            $q->whereNotBetween('datetime_from', [$unavailability->start_date, $unavailability->end_date]);
        }

        return $this->bookings()->whereNotIn('id', $q->pluck('id'))->get();
    }

    public function bookings(): HasMany {
        return $this->hasMany(Booking::class);
    }

    public function purchases(): HasMany {
        return $this->hasMany(Purchase::class);
    }
}
