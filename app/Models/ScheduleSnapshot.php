<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ScheduleSnapshot
 *
 * @property int id
 * @property int buffer_time
 * @property int deposit_instalments
 * @property int deposit_instalment_frequency
 * @property bool deposit_accepted
 * @property float deposit_amount
 * @property string deposit_final_date
 * @property Service service
 * @property Schedule schedule
 */
class ScheduleSnapshot extends Schedule
{
    protected $fillable = [
        'title',
        'service_snapshot_id',
        'location_snapshot_id',
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
        'is_published',
        'schedule_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function service()
    {
        return $this->belongsTo(ServiceSnapshot::class, 'service_snapshot_id');
    }

    public function prices()
    {
        return $this->hasMany(PriceSnapshot::class);
    }

    public function location()
    {
        return $this->belongsTo(LocationSnapshot::class, 'location_snapshot_id');
    }

    public function schedule_availabilities()
    {
        return $this->schedule->schedule_availabilities();
    }

    public function schedule_unavailabilities()
    {
        return $this->schedule->schedule_unavailabilities();
    }

    public function schedule_files()
    {
        return $this->schedule->schedule_files();
    }

    public function schedule_hidden_files()
    {
        return $this->schedule->schedule_hidden_files();
    }

    public function freezes(): HasMany
    {
        return $this->schedule->freezes();
    }

    public function rescheduleRequests()
    {
        return $this->schedule->rescheduleRequests();
    }

}
