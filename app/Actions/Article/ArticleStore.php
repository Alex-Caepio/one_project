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

        if ($request->filled('image_url'))
        {
            $image = Storage::move('tmp/' . basename($request->image_url), "/images/artcles/{$article->id}/media_images/"
                . basename($request->image_url));
            $request->image_url = Storage::url($image);
        }

        $this->saveArticle($article, $request);
        return $article;
    }
}
