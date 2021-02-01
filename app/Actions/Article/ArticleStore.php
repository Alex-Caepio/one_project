<?php

namespace App\Actions\Article;

use App\Models\Article;
use App\Http\Requests\Articles\ArticleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleStore extends ArticleAction {

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @return \App\Models\Article
     */
    public function execute(ArticleRequest $request): Article {
        $article = new Article();

//        if (!empty($request->image_url))
//        {
//            $image = Storage::disk(config('image.image_storage'))
//                ->put("/images/artcles/{$article->id}/media_images/", file_get_contents($request->image_url));
//            $request->image_url = Storage::url($image);
//        }

        if ($request->filled('media_images'))
        {
            foreach ($request->media_images as $mediaImage)
            {
                $image = Storage::disk(config('image.image_storage'))
                    ->put("/images/artcles/{$article->id}/media_images/", file_get_contents($mediaImage['url']));
                $mediaImage[] = Storage::url($image);
            }
            $request->media_images = $mediaImage;
        }

        $this->saveArticle($article, $request);
        return $article;
    }
}
