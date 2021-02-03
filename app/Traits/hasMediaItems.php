<?php


namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait hasMediaItems
{
    public function links(array $links,Model $model,bool $flag): array
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
    public function syncImages($links,$model)
    {
        $model->media_images()->whereNotIn('url', $links)->delete();
        $urls         = collect($links);
        $recurringURL = $model->media_images()->whereIn('url', $urls)->pluck('url')->toArray();
        $newImages    = $urls->filter(function ($value) use ($recurringURL) {
            return !in_array($value, $recurringURL);
        })->toArray();

        $imageUrlToStore = [];
        foreach ($newImages as $url) {
            $imageUrlToStore[]['url'] = $url;
        }

        if ($imageUrlToStore) {
            $model->media_images()->createMany($imageUrlToStore);
        }
    }
    public function syncVideos($links,$model)
    {
        $model->media_videos()->whereNotIn('url', $links)->delete();
        $urls         = collect($links);
        $recurringURL = $model->media_videos()->whereIn('url', $urls)->pluck('url')->toArray();
        $newVideos    = $urls->filter(function ($value) use ($recurringURL) {
            return !in_array($value, $recurringURL);
        });

        $videoUrlToStore = [];
        foreach ($newVideos as $url) {
            $videoUrlToStore[]['url'] = $url;
        }

        if ($videoUrlToStore) {
            $model->media_videos()->createMany($videoUrlToStore);
        }
    }

    public function syncDocuments($links,$model)
    {
        $model->documents()->whereNotIn('url', $links)->delete();
        $model->documents()->createMany($links);
    }


}
