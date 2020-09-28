<?php


namespace App\Transformers;


use App\Models\Discipline;
use App\Models\DisciplineImage;
use App\Models\DisciplineVideo;
use App\Models\FocusArea;

class DisciplineTransformer extends Transformer
{
    protected $availableIncludes = ['services', 'articles',
        'focus_areas', 'practitioners', 'discipline_videos',
        'discipline_images','featured_practitioners','featured_services'];

    public function transform(Discipline $discipline)
    {
        return [
            'id' => $discipline->id,
            'name' => $discipline->name,
            'is_published' => (bool) $discipline->is_published,
            'url' => $discipline->url,
            'created_at' => $discipline->created_at,
            'updated_at' => $discipline->updated_at,
        ];
    }
    public function includeFeaturedPractitioners(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->featured_practitioners, new UserTransformer());
    }
    public function includeFeaturedServices(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->featured_services, new ServiceTransformer());
    }
    public function includeServices(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->services, new ServiceTransformer());
    }

    public function includeArticles(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->articles, new ArticleTransformer());
    }

    public function includeFocusAreas(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->focus_areas, new FocusAreaTransformer());
    }

    public function includeDisciplineVideos(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->discipline_videos, new DisciplineVideoTransformer());
    }

    public function includeDisciplineImage(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->discipline_images, new DisciplineImageTransformer());
    }

    public function includePractitioners(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->practitioners, new UserTransformer());
    }

}
