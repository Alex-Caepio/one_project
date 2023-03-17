<?php

namespace App\Transformers;

use App\Models\MediaFile;

class MediaFileTransformer extends Transformer
{
    protected $availableIncludes = [];

    public function transform(MediaFile $file): array
    {
        return [
            'url' => $file->url,
            'name' => $file->name,
        ];
    }
}
