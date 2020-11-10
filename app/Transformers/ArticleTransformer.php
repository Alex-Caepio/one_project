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
class ArticleTransformer extends Transformer {
    /**
     * @var string[]
     */
    protected $availableIncludes = [
        'user',
        'disciplines',
        'favourite_articles',
        'media_images',
        'media_videos',
        'media_files',
        'focus_areas',
        'keywords',
        'services',
        'last_published'
    ];

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
            'published_at' => $this->dateTime($article->published_at),
        ];
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUser(Article $article): ?Item {
        return $this->itemOrNull($article->user, new UserTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDisciplines(Article $article): ?Collection {
        return $this->collectionOrNull($article->disciplines, new DisciplineTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeFavouriteArticles(Article $article): ?Collection {
        return $this->collectionOrNull($article->favourite_articles, new ArticleTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaImages(Article $article): ?Collection {
        return $this->collectionOrNull($article->media_images, new MediaImageTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaVideos(Article $article): ?Collection {
        return $this->collectionOrNull($article->media_videos, new MediaVideoTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaFiles(Article $article): ?Collection {
        return $this->collectionOrNull($article->media_files, new MediaFileTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeFocusAreas(Article $article): ?Collection {
        return $this->collectionOrNull($article->focus_areas, new FocusAreaTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeKeywords(Article $article): ?Collection {
        return $this->collectionOrNull($article->keywords, new KeywordTransformer());
    }

    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeServices(Article $article): ?Collection {
        return $this->collectionOrNull($article->services, new ServiceTransformer());
    }


    /**
     * @param \App\Models\Article $article
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeLastPublished(Article $article): ?Collection {
        return $this->collectionOrNull(Article::where('id', '<>', $article->id)
                                              ->where('user_id', $article->user_id)
                                              ->published()
                                              ->orderBy('published_at', 'desc')
                                              ->limit(3)
                                              ->get(), new self());
    }
}
