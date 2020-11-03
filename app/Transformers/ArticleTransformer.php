<?php


namespace App\Transformers;


use App\Models\Article;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * Class ArticleTransformer
 *
 * @package App\Transformers
 */
class ArticleTransformer extends Transformer
{
    /**
     * @var string[]
     */
    protected $availableIncludes = ['user', 'disciplines', 'favourite_articles', 'media_images', 'media_videos', 'media_files'];

    /**
     * @param \App\Models\Article $article
     * @return array
     */
    public function transform(Article $article) {
        return [
            'id'           => $article->id,
            'title'        => $article->title,
            'description'  => $article->description,
            'user_id'      => $article->user_id,
            'is_published' => (bool)$article->is_published,
            'introduction' => $article->introduction,
            'url'          => $article->url,
            'image_url'    => $article->image_url,
            'created_at'   => $this->dateTime($article->created_at),
            'updated_at'   => $this->dateTime($article->updated_at),
            'deleted_at'   => $this->dateTime($article->deleted_at),
        ];
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUser(Article $article): ?Item
    {
        return $this->itemOrNull($article->user, new UserTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDisciplines(Article $article): ?Collection
    {
        return $this->collectionOrNull($article->disciplines, new DisciplineTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeFavouriteArticles(Article $article): ?Collection
    {
        return $this->collectionOrNull($article->favourite_articles, new ArticleTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaImages(Article $article): ?Collection
    {
        return $this->collectionOrNull($article->media_images, new MediaImageTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaVideos(Article $article): ?Collection
    {
        return $this->collectionOrNull($article->media_videos, new MediaVideoTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaFiles(Article $article): ?Collection
    {
        return $this->collectionOrNull($article->media_files, new MediaFileTransformer());
    }
}
