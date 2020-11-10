<?php


namespace App\Actions\Article;

use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class ArticleAction {

    protected function fillArticle(Article $article, ArticleRequest $request): Article {
        $requestData = $request->toArray();
        $requestData['user_id'] = Auth::id();
        $article->forceFill($requestData);

        DB::transaction(function () use ($article, $request) {
            $article->save();

            if ($request->has('media_images')) {
                $article->media_images()->delete();
                $article->media_images()->createMany($request->get('media_images'));
            }

            if ($request->has('media_videos')) {
                $article->media_videos()->delete();
                $article->media_videos()->createMany($request->get('media_videos'));
            }

            if ($request->has('media_files')) {
                $article->media_files()->delete();
                $article->media_files()->createMany($request->get('media_files'));
            }

            if ($request->filled('focus_areas')) {
                $article->focus_areas()->sync($request->get('focus_areas'));
            }

            if ($request->filled('disciplines')) {
                $article->focus_areas()->sync($request->get('disciplines'));
            }

            if ($request->filled('keywords')) {
                $article->focus_areas()->sync($request->get('keywords'));
            }

            if ($request->filled('services')) {
                $article->focus_areas()->sync($request->get('services'));
            }
        });
        return $article;
    }

}
