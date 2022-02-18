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
                'id'                          => $booking->booking->schedule->service->id,
                'title'                       => $booking->purchase->service->title,
                'description'                 => $booking->purchase->service->description,
                'locations'                   => $booking->purchase->service->locations,
                'basic_location'              => $booking->purchase->service->basic_location,
                'deposit_instalment_payments' => $booking->purchase->service->deposit_instalment_payments,
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
                'last_published'              => $this->dateTime($booking->schedule->service->last_published),
            ];
        }

        return [
            'id'                          => $booking->schedule->service->id,
            'title'                       => $booking->schedule->service->title,
            'description'                 => $booking->schedule->service->description,
            'locations'                   => $booking->schedule->service->locations,
            'basic_location'              => $booking->schedule->service->basic_location,
            'deposit_instalment_payments' => $booking->schedule->service->deposit_instalment_payments,
            'user_id'                     => $booking->schedule->service->user_id,
            'is_published'                => (bool)$booking->schedule->service->is_published,
            'introduction'                => $booking->schedule->service->introduction,
            'slug'                        => $booking->schedule->service->slug,
            'service_type_id'             => $booking->schedule->service->service_type_id,
            'created_at'                  => $booking->schedule->service->created_at,
            'updated_at'                  => $booking->schedule->service->updated_at,
            'deleted_at'                  => $booking->schedule->service->deleted_at,
            'image_url'                   => $booking->schedule->service->image_url,
            'icon_url'                    => $booking->schedule->service->icon_url,
            'stripe_id'                   => $booking->schedule->service->stripe_id,
            'published_at'                => $booking->schedule->service->published_at,
            'last_published'              => $this->dateTime($booking->schedule->service->last_published),
        ];
    }

    public function includeUser(Booking $booking) {
        return $this->itemOrNull($booking->schedule->service->user, new UserTransformer());
    }

    public function includePractitioner(Booking $booking) {
        return $this->itemOrNull($booking->schedule->service->practitioner, new UserTransformer());
    }

    public function includeKeywords(Booking $booking) {
        return $this->collectionOrNull($booking->schedule->service->keywords, new KeywordTransformer());
    }

    public function includeDisciplines(Booking $booking) {
        return $this->collectionOrNull($booking->schedule->service->disciplines, new DisciplineTransformer());
    }

    public function includeFocusAreas(Booking $booking) {
        return $this->collectionOrNull($booking->schedule->service->focus_areas, new FocusAreaTransformer());
    }

    public function includeLocation(Booking $booking) {
        return $this->itemOrNull($booking->schedule->service->location, new LocationTransformer());
    }

    public function includeSchedules(Booking $booking) {
        return $this->collectionOrNull($booking->schedule->service->schedules, new ScheduleTransformer());
    }

    public function includeActiveSchedules(Booking $booking) {
        return $this->collectionOrNull($booking->schedule->service->active_schedules, (new ScheduleTransformer())->setBooking($booking));
    }

    public function includeFavoritesService(Booking $booking) {
        return $this->collectionOrNull($booking->schedule->service->favourite_services, new ServiceTransformer());
    }

    public function includeServiceType(Booking $booking) {
        return $this->itemOrNull($booking->schedule->service->service_type, new ServiceTypeTransformer());
    }

    public function includeArticles(Booking $booking) {
        return $this->collectionOrNull($booking->schedule->service->articles, new ArticleTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaImages(Booking $booking): ?Collection {
        return $this->collectionOrNull($booking->schedule->service->media_images, new MediaImageTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaVideos(Booking $booking): ?Collection {
        return $this->collectionOrNull($booking->schedule->service->media_videos, new MediaVideoTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMediaFiles(Booking $booking): ?Collection {
        return $this->collectionOrNull($booking->schedule->service->media_files, new MediaFileTransformer());
    }

    /**
     * @param \App\Models\Booking $booking
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeLastPublished(Booking $booking): ?Collection {
        return $this->collectionOrNull(Service::where('id', '<>', $booking->schedule->service->id)->published()
                                              ->orderBy('updated_at', 'desc')->limit(3)->get(), new self());
    }

}
