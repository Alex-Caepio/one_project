<?php


namespace App\Actions\Article;

use App\Http\Requests\Admin\ArticleUpdateRequest;
use App\Http\Requests\Request;
use App\Models\Article;
use App\Models\Keyword;
use App\Traits\hasMediaItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class AdminArticleAction {

    use hasMediaItems;

    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     */
    protected function updateArticle(Article $article, ArticleUpdateRequest $request) {
        DB::transaction(function() use ($article, $request) {
            $this->fillArticle($article, $request);
            $this->fillRelations($article, $request);
        });
    }


    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @return \App\Models\Article
     */
    protected function fillArticle(Article $article, ArticleUpdateRequest $request): Article {
        $article->forceFill([
                                'title'        => $request->get('title'),
                                'description'  => $request->get('description'),
                                'is_published' => $request->getBoolFromRequest('is_published'),
                                'introduction' => $request->get('introduction'),
                                'slug'         => $request->get('slug'),
                                'image_url'    => $request->get('image_url'),
                                'user_id'      => Auth::id(),
                            ]);
        $article->save();
        return $article;
    }

    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     */
    protected function fillRelations(Article $article, ArticleUpdateRequest $request): void {

        if ($request->filled('media_images')){
//            foreach ($request->media_images as $mediaImage)
//            {
//                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($mediaImage)))
//                {
//                    $image = Storage::disk(config('image.image_storage'))
//                        ->put("/images/articles/{$article->id}/media_images/", file_get_contents($mediaImage));
//                    $image_urls[] = Storage::url($image);
//                }
//            }
//            $request->media_images = $image_urls;
            $this->syncImages($request->media_images,$article);
        }
        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos,$article);
        }

        if ($request->has('media_files')) {
            $article->media_files()->delete();
            $article->media_files()->createMany($request->get('media_files'));
        }

        if ($request->filled('focus_areas')) {
            $article->focus_areas()->sync($request->get('focus_areas'));
        }

        if ($request->filled('disciplines')) {
            $article->disciplines()->sync($request->get('disciplines'));
        }

        $keywords = $this->collectKeywordModelsFromRequest($request);
        if (count($keywords)) {
            $article->keywords()->sync($keywords);
        }

        if ($request->filled('services')) {
            $article->services()->sync($request->get('services'));
        }
    }

    /**
     * @param \App\Http\Requests\Request $request
     * @return array
     */
    private function collectKeywordModelsFromRequest(Request $request): array {
        $ids = [];
        if ($request->filled('keywords') && is_array($request->get('keywords'))) {
            $keywords = array_unique($request->get('keywords'));
            foreach ($keywords as $keyword) {
                $keyword = Keyword::firstOrCreate(['title' => strtoupper($keyword)]);
                $ids[] = $keyword->id;
            }
        }
        return $ids;
    }

}
