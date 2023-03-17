<?php

namespace App\Actions\Article;

use App\Models\Article;
use App\Http\Requests\Articles\ArticleRequest;
use Illuminate\Support\Facades\Auth;

class ArticleStore extends ArticleAction
{
    public function execute(ArticleRequest $request): Article
    {
        $article = new Article();
        $article->user_id = Auth::id();
        $this->saveArticle($article, $request);

        return $article;
    }
}
