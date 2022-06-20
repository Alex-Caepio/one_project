<?php
namespace App\Transformers;

use App\Models\DisciplineImage;


class DisciplineImageTransformer extends Transformer
{
    public function transform(DisciplineImage $disciplineImage)
    {
        return [
            'id' => $disciplineImage->id,
            'discipline_id' => $disciplineImage->discipline_id,
            'path' => $disciplineImage->path,
            'created_at' => $disciplineImage->created_at,
            'updated_at' => $disciplineImage->updated_at,
        ];
    }
}
