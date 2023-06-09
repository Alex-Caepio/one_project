<?php

namespace App\Transformers;

use App\Models\Service;
use App\Models\ServiceSnapshot;
use League\Fractal\Resource\Collection;

class ServiceTransformer extends Transformer
{
    protected $availableIncludes = [
        'user',
        'practitioner',
        'keywords',
        'disciplines',
        'focus_areas',
        'location',
        'schedules',
        'active_schedules',
        'favourite_services',
        'service_type',
        'articles',
        'media_images',
        'media_files',
        'media_videos',
        'last_published'
    ];

    /**
     * @param Service|ServiceSnapshot $service
     */
    public function transform(Service $service)
    {
        return [
            'id'                          => $service->id,
            'title'                       => $service->title,
            'description'                 => $service->description,
            'locations'                   => $service->locations,
            'basic_location'              => $service->basic_location,
            'user_id'                     => $service->user_id ?? $service->service->user_id,
            'is_published'                => (bool)$service->is_published,
            'introduction'                => $service->introduction,
            'slug'                        => $service->slug,
            'service_type_id'             => $service->service_type_id,
            'created_at'                  => $service->created_at,
            'updated_at'                  => $service->updated_at,
            'deleted_at'                  => $service->deleted_at,
            'image_url'                   => $service->image_url,
            'icon_url'                    => $service->icon_url,
            'stripe_id'                   => $service->stripe_id,
            'published_at'                => $service->published_at,
            'last_published'              => $this->dateTime($service->last_published),
        ];
    }

    /**
     * @param Service|ServiceSnapshot $service
     */
    public function includeUser(Service $service)
    {
        return $this->itemOrNull($service->user ?? $service->service->user, new UserTransformer());
    }

    public function includePractitioner(Service $service)
    {
        return $this->itemOrNull($service->practitioner, new UserTransformer());
    }

    public function includeKeywords(Service $service)
    {
        return $this->collectionOrNull($service->keywords, new KeywordTransformer());
    }

    public function includeDisciplines(Service $service)
    {
        return $this->collectionOrNull($service->disciplines, new DisciplineTransformer());
    }

    public function includeFocusAreas(Service $service)
    {
        return $this->collectionOrNull($service->focus_areas, new FocusAreaTransformer());
    }

    public function includeLocation(Service $service)
    {
        return $this->itemOrNull($service->location, new LocationTransformer());
    }

    public function includeSchedules(Service $service)
    {
        return $this->collectionOrNull($service->schedules, new ScheduleTransformer());
    }

    public function includeActiveSchedules(Service $service)
    {
        return $this->collectionOrNull($service->active_schedules, new ScheduleTransformer());
    }

    public function includeFavoritesService(Service $service)
    {
        return $this->collectionOrNull($service->favourite_services, new ServiceTransformer());
    }

    public function includeServiceType(Service $service)
    {
        return $this->itemOrNull($service->service_type, new ServiceTypeTransformer());
    }

    public function includeArticles(Service $service)
    {
        return $this->collectionOrNull($service->articles, new ArticleTransformer());
    }

    public function includeMediaImages(Service $service): ?Collection
    {
        return $this->collectionOrNull($service->media_images, new MediaImageTransformer());
    }

    public function includeMediaVideos(Service $service): ?Collection
    {
        return $this->collectionOrNull($service->media_videos, new MediaVideoTransformer());
    }

    public function includeMediaFiles(Service $service): ?Collection
    {
        return $this->collectionOrNull($service->media_files, new MediaFileTransformer());
    }

    public function includeLastPublished(Service $service): ?Collection
    {
        return $this->collectionOrNull(
            Service::query()
                ->where('id', '<>', $service->id)
                ->published()
                ->orderBy('updated_at', 'desc')->limit(3)->get(),
            new self()
        );
    }
}
