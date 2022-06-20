<?php


namespace App\Transformers;

use App\Models\Keyword;

class KeywordTransformer extends Transformer
{

    public function transform(Keyword $keyword)
    {
        return [
            'id'           => $keyword->id,
            'title'        => $keyword->title,
        ];
    }

}
