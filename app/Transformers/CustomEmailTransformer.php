<?php


namespace App\Transformers;

use App\Models\CustomEmail;

class CustomEmailTransformer extends Transformer {

    public function transform(CustomEmail $customEmail): array {
        return [
            'id'            => $customEmail->id,
            'name'          => $customEmail->name,
            'user_type'     => $customEmail->user_type,
            'from_email'    => $customEmail->from_email,
            'from_title'    => $customEmail->from_title,
            'subject'       => $customEmail->subject,
            'logo'          => $customEmail->logo,
            'logo_filename' => $customEmail->logo_filename,
            'text'          => $customEmail->text,
            'footer'        => $customEmail->footer,
            'delay'         => $customEmail->delay,
            'created_at'    => $customEmail->created_at,
            'updated_at'    => $customEmail->updated_at,

        ];
    }
}
