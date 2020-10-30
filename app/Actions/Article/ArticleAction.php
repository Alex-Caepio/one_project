<?php


namespace App\Actions\Article;

use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

abstract class ArticleAction {

    protected function fillArticle(Article $article, ArticleRequest $request): Article {
        $requestData = $request->toArray();
        $requestData['user_id'] = Auth::id();
        $article->forceFill($requestData);
        $article->save();
        return $article;
    }

}
