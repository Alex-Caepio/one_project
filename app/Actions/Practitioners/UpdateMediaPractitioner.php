<?php

namespace App\Actions\Practitioners;

use App\Http\Requests\Request;
use App\Models\User;
use App\Traits\HasMediaItems;
use App\Traits\KeywordCollection;

class UpdateMediaPractitioner
{
    use HasMediaItems, KeywordCollection;

    public function execute(User $user, Request $request): void
    {
        $user->forceFill($request->all($request->getValidatorKeys()));
        $user->save();

        if ($request->filled('disciplines')) {
            $user->disciplines()->sync($request->disciplines);
        }

        if ($request->filled('focus_areas')) {
            $user->focus_areas()->sync($request->focus_areas);
        }

        if ($request->filled('service_types')) {
            $user->service_types()->sync($request->service_types);
        }

        $keywords = $this->collectKeywordModelsFromRequest($request);
        $user->keywords()->detach();
        $user->keywords()->sync($keywords);

        if ($request->filled('media_images')) {
            $this->syncImages($request->media_images, $user);
        }

        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos, $user);
        }
    }
}
