<?php


namespace App\Transformers;


use App\Models\Article;

class ArticleTransformer extends Transformer
{
    protected $availableIncludes = ['user','disciplines','favourite_articles'];

    public function transform(Article $article)
    {
        return [
            'id'           => $article->id,
            'title'        => $article->title,
            'description'  => $article->description,
            'user_id'      => $article->user_id,
            'is_published' => $article->is_published,
            'introduction' => $article->introduction,
            'url'          => $article->url,
            'created_at'   => $article->created_at,
            'updated_at'   => $article->updated_at,
        ];
    }

    public function includeUser(Article $article)
    {
        return $this->itemOrNull($article->user, new UserTransformer());
    }
    public function includeDiscipline(Article $article)
    {
        return $this->collectionOrNull($article->disciplines, new DisciplineTransformer());
    }
    public function includeFavoriteArticles(Article $article)
    {
        return $this->collectionOrNull($article->favourite_articles, new ArticleTransformer());
    }
}
