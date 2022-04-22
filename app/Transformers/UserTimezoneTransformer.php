<?php

namespace App\Transformers;

use App\Models\User;

class UserTimezoneTransformer extends Transformer
{
    public function transform(User $user)
    {
        return [
            'timezone_id' => $user->timezone_id,
            'timezone_code' => $user->user_timezone->label,
            'timezone_value' => $user->user_timezone->value,
        ];
    }
}
