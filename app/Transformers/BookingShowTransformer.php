<?php


namespace App\Transformers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\BookingSnapshot;
use League\Fractal\Resource\Collection;

class BookingShowTransformer extends Transformer {

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

    public function transform(Booking $booking) {
        $booking = $booking->snapshot ?? $booking;

        if ($booking instanceof BookingSnapshot) {
            return [
                'id'                          => $booking->booking->schedule_with_trashed->service->id,
                'title'                       => $booking->purchase->service->title,
                'description'                 => $booking->purchase->service->description,
                'locations'                   => $booking->purchase->service->locations,
                'basic_location'              => $booking->purchase->service->basic_location,
                'user_id'                     => $booking->purchase->service->user_id,
                'is_published'                => (bool)$booking->purchase->service->is_published,
                'introduction'                => $booking->purchase->service->introduction,
                'slug'                        => $booking->purchase->service->slug,
                'service_type_id'             => $booking->purchase->service->service_type_id,
                'created_at'                  => $booking->purchase->service->created_at,
                'updated_at'                  => $booking->purchase->service->updated_at,
                'deleted_at'                  => $booking->purchase->service->deleted_at,
                'image_url'                   => $booking->purchase->service->image_url,
                'icon_url'                    => $booking->purchase->service->icon_url,
                'stripe_id'                   => $booking->purchase->service->stripe_id,
                'published_at'                => $booking->purchase->service->published_at,
                'last_published'              => $this->dateTime($booking->schedule_with_trashed->service->last_published),
            ];
        }

        return [
            'id'                          => $booking->schedule_with_trashed->service->id,
            'title'                       => $booking->schedule_with_trashed->service->title,
            'description'                 => $booking->schedule_with_trashed->service->description,
            'locations'                   => $booking->schedule_with_trashed->service->locations,
            'basic_location'              => $booking->schedule_with_trashed->service->basic_location,
            'user_id'                     => $booking->schedule_with_trashed->service->user_id,
            'is_published'                => (bool)$booking->schedule_with_trashed->service->is_published,
            'introduction'                => $booking->schedule_with_trashed->service->introduction,
            'slug'                        => $booking->schedule_with_trashed->service->slug,
            'service_type_id'             => $booking->schedule_with_trashed->service->service_type_id,
            'created_at'                  => $booking->schedule_with_trashed->service->created_at,
            'updated_at'                  => $booking->schedule_with_trashed->service->updated_at,
            'deleted_at'                  => $booking->schedule_with_trashed->service->deleted_at,
            'image_url'                   => $booking->schedule_with_trashed->service->image_url,
            'icon_url'                    => $booking->schedule_with_trashed->service->icon_url,
            'stripe_id'                   => $booking->schedule_with_trashed->service->stripe_id,
            'published_at'                => $booking->schedule_with_trashed->service->published_at,
            'last_published'              => $this->dateTime($booking->schedule_with_trashed->service->last_published),
        ];
    }

    public function includeUser(Booking $booking) {
        return $this->itemOrNull($booking->schedule_with_trashed->service->user, new UserTransformer());
    }

    public function includePractitioner(Booking $booking) {
        return $this->itemOrNull($booking->schedule_with_trashed->service->practitioner, new UserTransformer());
    }

    public function includeKeywords(Booking $booking) {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->keywords, new KeywordTransformer());
    }

    public function includeDisciplines(Booking $booking) {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->disciplines, new DisciplineTransformer());
    }

    public function includeFocusAreas(Booking $booking) {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->focus_areas, new FocusAreaTransformer());
    }

    public function includeLocation(Booking $booking) {
        return $this->itemOrNull($booking->schedule_with_trashed->service->location, new LocationTransformer());
    }

    public function includeSchedules(Booking $booking) {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->schedules, new ScheduleTransformer());
    }

    public function includeActiveSchedules(Booking $booking) {
        return $this->collectionOrNull(collect([$booking->schedule_with_trashed->service->active_schedules()->withTrashed()->first()]),
                (new ScheduleTransformer())->setBooking($booking));
    }

    public function includeFavoritesService(Booking $booking) {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->favourite_services, new ServiceTransformer());
    }

    public function includeServiceType(Booking $booking) {
        return $this->itemOrNull($booking->schedule_with_trashed->service->service_type, new ServiceTypeTransformer());
    }

    public function includeArticles(Booking $booking) {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->articles, new ArticleTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaImages(Booking $booking): ?Collection {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->media_images, new MediaImageTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaVideos(Booking $booking): ?Collection {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->media_videos, new MediaVideoTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaFiles(Booking $booking): ?Collection {
        return $this->collectionOrNull($booking->schedule_with_trashed->service->media_files, new MediaFileTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeLastPublished(Booking $booking): ?Collection {
        return $this->collectionOrNull(Service::where('id', '<>', $booking->schedule_with_trashed->service->id)->published()
                                              ->orderBy('updated_at', 'desc')->limit(3)->get(), new self());
    }

}
