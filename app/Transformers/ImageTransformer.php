<?php


namespace App\Transformers;

use App\Models\image;
use League\Fractal\Resource\Item;

class ImageTransformer extends Transformer {
    protected $availableIncludes = [
        'user',
    ];

    /**
     * A Fractal transformer.
     *
     * @param \App\Models\image $image
     * @return array
     */
    public function transform(Image $image): array {
        return [
            'id'             => $image->id,
            'user_id'        => $image->user_id,
            'path'           => $image->path,
            'url'            => $image->url,
            'size'           => $image->size,
            'created_at'     => $image->created_at,
            'updated_at'     => $image->updated_at,
        ];
    }

    public function includeUser(image $image): ?Item {
        return $this->itemOrNull($image->user, new UserTransformer());
    }
}
