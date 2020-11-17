<?php


namespace App\Transformers;

use App\Models\Service;

class ServiceTransformer extends Transformer
{
    protected $availableIncludes = ['user', 'keywords', 'disciplines', 'focus_areas', 'location', 'schedules', 'favourite_services', 'service_types', 'articles'];

    public function transform(Service $service)
    {
        return [
            'id' => $service->id,
            'title' => $service->title,
            'description' => $service->description,
            'type' => $service->type,
            'locations' => $service->locations,
            'basic_location' => $service->basic_location,
            'deposit_instalment_payments' => $service->deposit_instalment_payments,
            'user_id' => $service->user_id,
            'is_published' => (bool) $service->is_published,
            'introduction' => $service->introduction,
            'url' => $service->url,
            'service_type_id' => $service->service_type_id,
            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }

    public function includeUser(Service $service)
    {
        return $this->itemOrNull($service->user, new UserTransformer());
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

    public function includeFavoritesService(Service $service)
    {
        return $this->collectionOrNull($service->favourite_services, new ServiceTransformer());
    }

    public function includeServiceType(Service $service) {
        return $this->collectionOrNull($service->service_types, new ServiceTypeTransformer());
    }

    public function includeArticles(Service $service) {
        return $this->collectionOrNull($service->articles, new ArticleTransformer());
    }
}
