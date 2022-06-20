<?php
namespace App\Transformers;

use App\Models\FocusAreaImage;

class FocusAreaImageTransformer extends Transformer
{
    public function transform(FocusAreaImage $focusAreaImage)
    {
        return [
            'id' => $focusAreaImage->id,
            'focus_area_id' => $focusAreaImage->focus_area_id,
            'path' => $focusAreaImage->path,
            'created_at' => $focusAreaImage->created_at,
            'updated_at' => $focusAreaImage->updated_at,
        ];
    }
}
