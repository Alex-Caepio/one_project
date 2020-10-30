<?php


namespace App\Actions\Article;


use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;

class ArticleUpdate extends ArticleAction {

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @param \App\Models\Article $article
     */
    public function execute(ArticleRequest $request, Article $article) {
        $this->fillArticle($article, $request);
        if ($request->has('media_images')) {
            $article->media_images()->delete();
            $article->media_images()->createMany($request->get('media_images'));
        }
        if ($request->has('media_videos')) {
            $article->media_videos()->delete();
            $article->media_videos()->createMany($request->get('media_videos'));
        }
        if ($request->has('media_files')) {
            $article->media_files()->delete();
            $article->media_files()->createMany($request->get('media_files'));
        }

        return $article;
    }

}
