<?php

namespace App\Actions\Article;

use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;

class ArticleUpdate extends ArticleAction
{
    public function execute(ArticleRequest $request, Article $article): Article
    {
        $this->saveArticle($article, $request);

        return $article;
    }
}
