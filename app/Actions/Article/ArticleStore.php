<?php

namespace App\Actions\Article;

use App\Models\Article;
use App\Events\ArticlePublished;
use App\Events\ArticleUnpublished;
use App\Http\Requests\Articles\ArticleRequest;
use Illuminate\Support\Facades\Auth;

class ArticleStore extends ArticleAction {

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @return \App\Models\Article
     */
    public function execute(ArticleRequest $request): Article {
        $article = new Article();
        return $this->fillArticle($article, $request);
    }
}
