<?php


namespace App\Actions\Practitioners;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Auth\UnpublishPractitionerRequest;
use App\Http\Requests\Request;
use App\Models\Keyword;
use App\Models\User;


class UpdateMediaPractitioner {

    public function execute(User $user, Request $request): void {

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

        if ($request->filled('keywords')) {
            $user->keywords()->whereNotIn('title', $request->keywords)->delete();
            foreach ($request->keywords as $keyword) {
                $ids = Keyword::firstOrCreate(['title' => $keyword])->pluck('id');
                $keywordIds = collect($ids);
            }

            if (isset($keywordIds) && !empty($keywordIds)) {
                $user->keywords()->sync($keywordIds);
            }
        }

        if ($request->filled('media_images')) {
            $this->syncImages($request->media_images, $user);
        }

        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos, $user);
        }
    }
}
