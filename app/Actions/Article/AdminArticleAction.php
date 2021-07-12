<?php


namespace App\Actions\Article;

use App\Http\Requests\Admin\ArticleUpdateRequest;
use App\Http\Requests\Request;
use App\Models\Article;
use App\Models\Keyword;
use App\Traits\hasMediaItems;
use App\Traits\KeywordCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class AdminArticleAction {

    use hasMediaItems, KeywordCollection;

    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Admin\ArticleUpdateRequest $request
     */
    protected function updateArticle(Article $article, ArticleUpdateRequest $request) {
        DB::transaction(function() use ($article, $request) {
            $this->fillArticle($article, $request);
            $this->fillRelations($article, $request);
        });
    }


    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Admin\ArticleUpdateRequest $request
     * @return \App\Models\Article
     */
    protected function fillArticle(Article $article, ArticleUpdateRequest $request): Article {
        $article->forceFill([
                                'title'        => $request->get('title'),
                                'description'  => $request->get('description'),
                                'is_published' => $request->getBoolFromRequest('is_published'),
                                'introduction' => $request->get('introduction'),
                                'slug'         => $request->get('slug'),
                                'image_url'    => $request->get('image_url')
                            ]);
        $article->save();
        return $article;
    }

    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Admin\ArticleUpdateRequest $request
     */
    protected function fillRelations(Article $article, ArticleUpdateRequest $request): void {

        if ($request->filled('media_images')) {
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
        $article->keywords()->detach();
        if (count($keywords)) {
            $article->keywords()->sync($keywords);
        }

        if ($request->filled('services')) {
            $article->services()->sync($request->get('services'));
        }
    }

}
