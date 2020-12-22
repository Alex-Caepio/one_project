<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Schedule
 *
 * @property int     id
 * @property Service service
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location_id',
        'cost',
        'meals_breakfast',
        'meals_lunch',
        'meals_dinner',
        'meals_alcoholic_beverages',
        'meals_dietry_accomodated',
        'refund_terms',
        'deposit_accepted',
        'deposit_amount',
        'deposit_final_date',
        'booking_message',
        'repeat',
        'repeat_every',
        'repeat_period',
        'notice_min_time',
        'notice_min_period',
        'buffer_time',
        'buffer_period',
        'is_virtual'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
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

    public function isSoldOut()
    {
        $time = Carbon::now()->subMinutes(15);
        $purchased = Booking::where('schedule_id', $this->id)->count();
        $personalFreezed = ScheduleFreeze::where('schedule_id', $this->id)
            ->where('user_id', Auth::id())
            ->where('freeze_at', '>', $time->toDateTimeString())->count();
        $freezed = ScheduleFreeze::where('schedule_id', $this->id)
            ->where('freeze_at', '>', $time->toDateTimeString())->count();
        if (isset($personalFreezed)) {
            return (bool)$this->attendees <= $purchased + $freezed - $personalFreezed;
        } else
            return (bool)$this->attendees <= $purchased + $freezed;

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
        $availabilities   = $this->schedule_availabilities;
        $unavailabilities = $this->schedule_unavailabilities;
        $q                = $this->bookings();

        foreach ($availabilities as $availability) {
            switch ($availability->days) {
                case 'everyday':
                    $q->where(function ($q) use ($availability) {
                        $q->where("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->where("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'weekends':
                    $q->where(function ($q) use ($availability) {
                        $q->whereIn(DB::raw('WEEKDAY(datetime_from)'), [5, 6])
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'weekdays':
                    $q->where(function ($q) use ($availability) {
                        $q->whereIn(DB::raw('WEEKDAY(datetime_from)'), [0, 1, 2, 3, 4])
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'monday':
                    $q->where(function ($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 0)
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'tuesday':
                    $q->where(function ($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 1)
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'wednesday':
                    $q->where(function ($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 2)
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'thursday':
                    $q->where(function ($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 3)
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'friday':
                    $q->where(function ($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 4)
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'saturday':
                    $q->where(function ($q) use ($availability) {
                        $q->where(DB::raw('WEEKDAY(datetime_from)'), '=', 5)
                            ->whereRaw("TIME(datetime_from) >= '{$availability->start_time}'")
                            ->whereRaw("TIME(datetime_from) <= '{$availability->end_time}'");
                    });
                    break;
                case 'sunday':
                    $q->where(function ($q) use ($availability) {
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

        return  $this->bookings()->whereNotIn('id', $q->pluck('id'))->get();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
