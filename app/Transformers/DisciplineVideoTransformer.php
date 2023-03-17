<?php


namespace App\Transformers;


use App\Models\DisciplineVideo;


class DisciplineVideoTransformer extends Transformer
{
    public function transform(DisciplineVideo $disciplineVideo)
    {
        return [
            'id' => $disciplineVideo->id,
            'discipline_id' => $disciplineVideo->discipline_id,
            'link' => $disciplineVideo->link,
            'created_at' => $disciplineVideo->created_at,
            'updated_at' => $disciplineVideo->updated_at,
        ];
    }
}
