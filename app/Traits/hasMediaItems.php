<?php


namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait hasMediaItems{

    public function links(array $links,Model $model,bool $flag)
    {
            $flag === true
                ? $relation = $model->media_images()
                : $relation = $model->media_videos();
            $links = collect($links);
            $recurringURL = $relation->whereIn('url', $links)->pluck('url')->toArray();
            $newImage = $links->filter(function ($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            })->toArray();

            foreach ($newImage as $url) {
                $newMediaToStore[]['url'] = $url;
            }

        return $newMediaToStore;
    }


}
