<?php


namespace App\Actions\Article;


use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\DB;

class ArticleUpdate extends ArticleAction {

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @param \App\Models\Article $article
     * @return \App\Models\Article
     */
    public function execute(ArticleRequest $request, Article $article) {

        $this->saveArticle($article, $request);
        return $article;
    }

}
