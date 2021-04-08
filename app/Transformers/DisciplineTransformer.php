<?php

namespace App\Transformers;

use App\Models\Discipline;

class DisciplineTransformer extends Transformer
{
    protected $availableIncludes = [
        'services', 'articles', 'focus_areas', 'practitioners',
        'featured_practitioners', 'featured_services',
        'media_images', 'media_videos', 'media_files',
        'featured_focus_areas', 'featured_articles',
        'related_disciplines'
    ];

    public function transform(Discipline $discipline): array
    {
        return [
            'id'           => $discipline->id,
            'name'         => $discipline->name,
            'introduction' => $discipline->introduction,
            'description'  => $discipline->description,
            'slug'         => $discipline->slug,
            'banner_url'   => $discipline->banner_url,
            'icon_url'     => $discipline->icon_url,
            'is_published' => (bool)$discipline->is_published,

            'section_2_h2'         => $discipline->section_2_h2,
            'section_2_h3'         => $discipline->section_2_h3,
            'section_2_background' => $discipline->section_2_background,
            'section_2_textarea'   => $discipline->section_2_textarea,

            'section_3_h2' => $discipline->section_3_h2,
            'section_3_h4' => $discipline->section_3_h4,

            'section_4_h2'         => $discipline->section_4_h2,
            'section_4_h3'         => $discipline->section_4_h3,
            'section_4_background' => $discipline->section_4_background,
            'section_4_textarea'   => $discipline->section_4_textarea,

            'section_5_header_h2' => $discipline->section_5_header_h2,

            'section_6_h2'         => $discipline->section_6_h2,
            'section_6_h3'         => $discipline->section_6_h3,
            'section_6_background' => $discipline->section_6_background,
            'section_6_textarea'   => $discipline->section_6_textarea,

            'section_7_tag_line'     => $discipline->section_7_tag_line,
            'section_7_alt_text'     => $discipline->section_7_alt_text,
            'section_7_url'          => $discipline->section_7_url,
            'section_7_target_blanc' => $discipline->section_7_target_blanc,
            'section_7_image_url'    => $discipline->section_7_image_url,
            'section_7_video_url'    => $discipline->section_7_video_url,

            'section_8_h2' => $discipline->section_8_h2,

            'section_9_h2'         => $discipline->section_9_h2,
            'section_9_h3'         => $discipline->section_9_h3,
            'section_9_background' => $discipline->section_9_background,
            'section_9_textarea'   => $discipline->section_9_textarea,

            'section_10_h2' => $discipline->section_10_h2,

            'section_11_tag_line'     => $discipline->section_11_tag_line,
            'section_11_alt_text'     => $discipline->section_11_alt_text,
            'section_11_url'          => $discipline->section_11_url,
            'section_11_target_blanc' => $discipline->section_11_target_blanc,
            'section_11_image_url'    => $discipline->section_11_image_url,
            'section_11_video_url'    => $discipline->section_11_video_url,

            'section_12_h2' => $discipline->section_12_h2,

            'section_13_tag_line'     => $discipline->section_13_tag_line,
            'section_13_alt_text'     => $discipline->section_13_alt_text,
            'section_13_url'          => $discipline->section_13_url,
            'section_13_target_blanc' => $discipline->section_13_target_blanc,
            'section_13_image_url'    => $discipline->section_13_image_url,
            'section_13_video_url'    => $discipline->section_13_video_url,


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

    public function includeFeaturedFocusAreas(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->featured_focus_areas, new FocusAreaTransformer());
    }

    public function includeFeaturedArticles(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->featured_articles, new ArticleTransformer());
    }

    public function includeRelatedDisciplines(Discipline $discipline)
    {
        return $this->collectionOrNull($discipline->related_disciplines, new DisciplineTransformer());
    }
}
