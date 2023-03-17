<?php

namespace App\Transformers;

use App\Models\ScheduleUnavailability;
use App\Models\UserUnavailabilities;
use Carbon\Carbon;

class UnavailabilityTransformer extends Transformer
{
    private $schedule_id = 0;

    public function __construct( int $id_schedule = 0 )
    {
        $this->schedule_id = $id_schedule;
    }

    public function transform( $objUnavailability, $id_schedule = 0 )
    {
        if( is_a( $objUnavailability, Carbon::class ) ){
            return [
                'id'            => 0,
                'schedule_id'   => $this->schedule_id,
                'start_date'    => $objUnavailability->startOfDay()->format('Y-m-d H:i:s'),
                'end_date'      => $objUnavailability->endOfDay()->format('Y-m-d H:i:s'),
            ];
        }elseif( is_a( $objUnavailability, ScheduleUnavailability::class  ) ){
            return [
                'id'            => $objUnavailability->id,
                'schedule_id'   => $this->schedule_id,
                'start_date'    => $objUnavailability->start_date,
                'end_date'      => $objUnavailability->end_date,
            ];
        }

        return [];
    }
}
