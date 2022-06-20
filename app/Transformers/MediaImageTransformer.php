<?php

namespace App\Transformers;

use App\Models\MediaImage;

class MediaImageTransformer extends Transformer
{
    protected $availableIncludes = [];

    public function transform(MediaImage $image): array
    {
        return [
            'url' => $image->url,
            'name' => $image->name,
        ];
    }
}
