<?php


namespace App\Transformers;

use App\Models\CustomEmail;

class CustomEmailFooterTransformer extends Transformer {

    public function transform(CustomEmail $customEmail): array {
        return [
            'footer' => $customEmail->footer,
        ];
    }
}
