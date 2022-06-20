<?php


namespace App\Transformers;


use App\Models\FocusAreaVideo;

class FocusAreaVideoTransformer extends Transformer
{
    public function transform(FocusAreaVideo $focusAreaVideo)
    {
        return [
            'id' => $focusAreaVideo->id,
            'focus_area_id' => $focusAreaVideo->focus_area_id,
            'link' => $focusAreaVideo->link,
            'created_at' => $focusAreaVideo->created_at,
            'updated_at' => $focusAreaVideo->updated_at,
        ];
    }
}
