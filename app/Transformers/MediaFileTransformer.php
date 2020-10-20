<?php

namespace App\Transformers;

use App\Models\MediaFiles;

class MediaFileTransformer extends Transformer
{
    protected $availableIncludes = [];

    public function transform(MediaFiles $file): array
    {
        return [
            'url' => $file->url,
        ];
    }
}
