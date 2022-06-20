<?php

namespace App\Transformers;

use Carbon\Carbon;
use League\Fractal;

class Transformer extends Fractal\TransformerAbstract
{

    public $fields;

    /**
     * Converts carbon datetime instance to datetime string
     *
     * @param Carbon $date Carbon datetime instance
     * @return null|string
     */
    public function dateTime($date)
    {
        return $date instanceof Carbon
            ? $date->toDateTimeString()
            : $date;
    }

    /**
     * Transforms model with selected transformer
     *
     * @param $model
     * @param $transformer
     * @return Fractal\Resource\Item|null
     */
    public function itemOrNull($model, $transformer)
    {
        return $model ? $this->item($model, $transformer) : null;
    }

    /**
     * Transforms model with selected transformer
     *
     * @param $model
     * @param $transformer
     * @return Fractal\Resource\Collection|null
     */
    public function collectionOrNull($model, $transformer)
    {
        return $model ? $this->collection($model, $transformer) : null;
    }

    public function filterFields(array $data): array
    {
        if (!$this->fields) {
            return $data;
        }

        return array_intersect_key($data, array_flip((array)$this->fields));
    }
}
