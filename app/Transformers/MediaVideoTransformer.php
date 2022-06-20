<?php

namespace App\Transformers;

use App\Models\MediaVideo;

class MediaVideoTransformer extends Transformer {
    protected $availableIncludes = [];

    public function transform(MediaVideo $video): array {
        return [
            'url'     => $video->url,
            'preview' => $video->preview,
        ];
    }
}
