<?php


namespace App\Transformers;


use App\Models\FocusArea;
use App\Models\FocusAreaImage;

class FocusAreaTransformer extends Transformer
{
    protected $availableIncludes = ['services', 'articles', 'practitioners', 'disciplines', 'focus_area_images', 'focus_area_videos'];

    public function transform(FocusArea $focusArea)
    {
        return [
            'id' => $focusArea->id,
            'name' => $focusArea->name,
            'description' => $focusArea->description,
            'introduction' => $focusArea->introduction,
            'url' => $focusArea->url,
            'banner_url'=> $focusArea->banner_url,
            'icon_url'=> $focusArea->icon_url,
            'created_at' => $focusArea->created_at,
            'updated_at' => $focusArea->updated_at,
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

}
