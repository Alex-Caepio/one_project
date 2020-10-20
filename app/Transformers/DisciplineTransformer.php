<?php


namespace App\Transformers;


use App\Models\Discipline;
use App\Models\DisciplineImage;
use App\Models\DisciplineVideo;
use App\Models\FocusArea;

class DisciplineTransformer extends Transformer
{
    protected $availableIncludes = [
        'services', 'articles', 'focus_areas', 'practitioners',
        'featured_practitioners', 'featured_services',
        'media_images', 'media_videos', 'media_files'
    ];

    public function transform(Discipline $discipline): array
    {
        return [
            'id'           => $discipline->id,
            'name'         => $discipline->name,
            'introduction' => $discipline->introduction,
            'description'  => $discipline->description,
            'url'          => $discipline->url,
            'banner_url'   => $discipline->banner_url,
            'icon_url'     => $discipline->icon_url,
            'is_published' => (bool)$discipline->is_published,
            'created_at'   => $this->dateTime($discipline->created_at),
            'updated_at'   => $this->dateTime($discipline->updated_at),
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

    public function includePractitioners(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->practitioners, new UserTransformer());
    }

    public function includeMediaImages(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->media_images, new MediaImageTransformer());
    }

    public function includeMediaVideos(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->media_videos, new MediaVideoTransformer());
    }

    public function includeMediaFiles(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->media_files, new MediaFileTransformer());
    }
}
