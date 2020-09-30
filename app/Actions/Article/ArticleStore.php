<?php


namespace App\Actions\Article;


use App\Events\ArticlePublished;
use App\Events\ArticleUnpublished;
use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ArticleStore
{
    public function execute(ArticleRequest $request)
    {
        $article = new Article();
        $article->forceFill([
            'title' => $request->get('title'),
            'introduction' => $request->get('introduction'),
            'user_id' => Auth::id(),
            'url' => $request->get('url'),
            'description' => $request->get('description'),
        ]);
        $article->save();
        $user = Auth::user();
        if (!$article) {
            event(new ArticleUnpublished($article, $user));
        } else
            event(new ArticlePublished($article, $user));
        return $article;
    }
}
