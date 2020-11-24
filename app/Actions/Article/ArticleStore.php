<?php

namespace App\Actions\Article;

use App\Models\Article;
use App\Http\Requests\Articles\ArticleRequest;
use Illuminate\Support\Facades\DB;

class ArticleStore extends ArticleAction {

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @return \App\Models\Article
     */
    public function execute(ArticleRequest $request): Article {
        $article = new Article();
        $this->saveArticle($article, $request);
        return $article;
    }
}
