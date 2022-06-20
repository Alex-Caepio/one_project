<?php

namespace App\Transformers;

use App\Models\ScheduleUnavailability;
use App\Models\UserUnavailabilities;

class UserUnavailabilitiesTransformer extends Transformer {

    public function transform(UserUnavailabilities $userUnavailabilities) {
        return [
            'id'              => $userUnavailabilities->id,
            'practitioner_id' => $userUnavailabilities->practitioner_id,
            'start_date'      => $userUnavailabilities->start_date,
            'end_date'        => $userUnavailabilities->end_date,
            'created_at'      => $userUnavailabilities->created_at,
            'updated_at'      => $userUnavailabilities->updated_at,
        ];
    }

}
