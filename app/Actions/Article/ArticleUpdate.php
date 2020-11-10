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
        return $article;
    }

}
