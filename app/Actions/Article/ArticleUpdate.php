<?php


namespace App\Actions\Article;


use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleUpdate extends ArticleAction {

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @param \App\Models\Article $article
     */
    public function execute(ArticleRequest $request, Article $article) {
//        if ($request->filled('media_images'))
//        {
//            foreach ($request->media_images as $mediaImage)
//            {
//                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($mediaImage)))
//                {
//                    $image = Storage::disk(config('image.image_storage'))
//                        ->put("/images/articles/{$article->id}/media_images/", file_get_contents($mediaImage));
//                    $image_urls[] = Storage::url($image);
//                }
//            }
//            $request->media_images = $image_urls;
//        }
        $this->saveArticle($article, $request);
        if ($request->filled('media_images')){
            $article->media_images()->whereNotIn('url', $request->media_images)->delete();
            $urls = collect($request->media_images)->pluck('url');
            $recurringURL = $article->media_images()->whereIn('url', $urls)->pluck('url')->toArray();
            $newImages = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            $imageUrlToStore = [];
            foreach ($newImages as $url) {
                $imageUrlToStore[]['url'] = $url;
            }

            if ($imageUrlToStore) {
                $article->media_images()->createMany($imageUrlToStore);
            }
        }
        if ($request->filled('media_videos') && !empty($request->media_videos)) {
            $article->media_videos()->whereNotIn('url', $request->media_videos)->delete();
            $urls = collect($request->media_videos)->pluck('url');
            $recurringURL = $article->media_videos()->whereIn('url', $urls)->pluck('url')->toArray();
            $newVideos = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            $videoUrlToStore = [];
            foreach ($newVideos as $url) {
                $videoUrlToStore[]['url'] = $url;
            }

            if ($videoUrlToStore) {
                $article->media_videos()->createMany($videoUrlToStore);
            }
        }
        return $article;
    }

}
