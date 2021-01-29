<?php


namespace App\Transformers;


use App\Models\FocusArea;
use App\Models\FocusAreaImage;

class FocusAreaTransformer extends Transformer
{
    protected $availableIncludes = [
        'services', 'articles', 'practitioners',
        'disciplines', 'focus_area_images', 'focus_area_videos',
        'featured_practitioners', 'featured_disciplines',
        'featured_articles', 'featured_services',
        'featured_focus_areas', 'media_images',
        'media_videos', 'media_files',
    ];

    public function transform(FocusArea $focusArea)
    {
        return [
            'id'                        => $focusArea->id,
            'name'                      => $focusArea->name,
            'description'               => $focusArea->description,
            'introduction'              => $focusArea->introduction,
            'url'                       => $focusArea->url,
            'banner_url'                => $focusArea->banner_url,
            'icon_url'                  => $focusArea->icon_url,
            'is_published'              => (bool)$focusArea->is_published,

            'section_2_h2'              => $focusArea->section_2_h2,
            'section_2_h3'              => $focusArea->section_2_h3,
            'section_2_background'      => $focusArea->section_2_background,
            'section_2_textarea'        => $focusArea->section_2_textarea,

            'section_3_h2'              => $focusArea->section_3_h2,

            'section_4_tag_line'        => $focusArea->section_4_tag_line,
            'section_4_alt_text'        => $focusArea->section_4_alt_text,
            'section_4_url'             => $focusArea->section_4_url,
            'section_4_target_blanc'    => $focusArea->section_4_target_blanc,
            'section_4_video_url'       => $focusArea->section_4_video_url,
            'section_4_image_url'       => $focusArea->section_4_image_url,

            'section_5_h2'              => $focusArea->section_5_h2,
            'section_5_h3'              => $focusArea->section_5_h3,
            'section_5_background'      => $focusArea->section_5_background,
            'section_5_textarea'        => $focusArea->section_5_textarea,

            'section_6_h2'              => $focusArea->section_6_h2,

            'section_7_h2'              => $focusArea->section_7_h2,
            'section_7_h3'              => $focusArea->section_7_h3,
            'section_7_background'      => $focusArea->section_7_background,
            'section_7_textarea'        => $focusArea->section_7_textarea,
            'section_7_image_url'       => $focusArea->section_7_image_url,
            'section_7_video_url'       => $focusArea->section_7_video_url,

            'section_8_h2'              => $focusArea->section_8_h2,

            'section_9_tag_line'        => $focusArea->section_9_tag_line,
            'section_9_alt_text'        => $focusArea->section_9_alt_text,
            'section_9_url'             => $focusArea->section_9_url,
            'section_9_target_blanc'    => $focusArea->section_9_target_blanc,
            'section_9_video_url'       => $focusArea->section_9_video_url,
            'section_9_image_url'       => $focusArea->section_9_image_url,

            'section_10_h2'             => $focusArea->section_10_h2,
            'section_10_h3'             => $focusArea->section_10_h3,
            'section_10_background'     => $focusArea->section_10_background,
            'section_10_textarea'       => $focusArea->section_10_textarea,

            'section_11_h2'             => $focusArea->section_11_h2,
            'section_11_image_url'      => $focusArea->section_11_image_url,
            'section_11_video_url'      => $focusArea->section_11_video_url,

            'section_12_h2'             => $focusArea->section_11_h2,
            'section_12_h4'             => $focusArea->section_11_h4,

            'section_13_tag_line'       => $focusArea->section_13_tag_line,
            'section_13_alt_text'       => $focusArea->section_13_alt_text,
            'section_13_url'            => $focusArea->section_13_url,
            'section_13_target_blanc'   => $focusArea->section_13_target_blanc,
            'section_13_image_url'      => $focusArea->section_13_image_url,
            'section_13_video_url'      => $focusArea->section_13_video_url,

            'created_at'                => $focusArea->created_at,
            'updated_at'                => $focusArea->updated_at,
        ];
    }

    public function includeServices(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->services, new ServiceTransformer());
    }

    public function includeArticles(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->articles, new ArticleTransformer());
    }

    public function includeDisciplines(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->disciplines, new DisciplineTransformer());
    }

    public function includePractitioners(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->practitioners, new UserTransformer());
    }

    public function includeFocusAreaImages(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->focus_area_images, new FocusAreaImageTransformer());
    }

    public function includeFocusAreaVideos(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->focus_area_videos, new FocusAreaVideoTransformer());
    }

    public function includeFeaturedPractitioners(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->featured_practitioners, new UserTransformer());
    }

    public function includeFeaturedDisciplines(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->featured_disciplines, new DisciplineTransformer());
    }

    public function includeFeaturedArticles(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->featured_articles, new ArticleTransformer());
    }

    public function includeFeaturedServices(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->featured_services, new ServiceTransformer());
    }

   public function includeFeaturedFocusAreas(FocusArea $focusArea)
   {
       return $this->collectionOrNull($focusArea->featured_focus_areas, new FocusAreaTransformer());
   }

    public function includeMediaImages(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->media_images, new MediaImageTransformer());
    }

    public function includeMediaVideos(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->media_videos, new MediaVideoTransformer());
    }

    public function includeMediaFiles(FocusArea $focusArea)
    {
        return $this->collectionOrNull($focusArea->media_files, new MediaFileTransformer());
    }
}
