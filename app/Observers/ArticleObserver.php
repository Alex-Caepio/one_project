<?php

namespace App\Observers;

use App\Models\Article;
use Carbon\Carbon;

class ArticleObserver {

    /**
     * Handle the article "created" event.
     *
     * @param \App\Models\Article $article
     * @return void
     */
    public function created(Article $article) {
        //
    }


    /**
     * @param \App\Models\Article $article
     */
    public function creating(Article $article) {

    }

    /**
     * Handle the article "updated" event.
     *
     * @param \App\Models\Article $article
     * @return void
     */
    public function saved(Article $article) {
        //
    }

    /**
     * Handle the article "updated" event.
     *
     * @param \App\Models\Article $article
     * @return void
     */
    public function saving(Article $article) {
        if ($article->isDirty('is_published')) {
            if (!$article->is_published) {
                $this->clearPublishedState($article);
            } else {
                $article->forceFill(['published_at' => Carbon::now()->format('Y-m-d H:i:s')]);
            }
        }
    }


    /**
     * Handle the article "deleting" event.
     * Drop Published Fields.
     *
     * @param \App\Models\Article $article
     * @return void
     */
    public function deleting(Article $article) {
        $this->clearPublishedState($article);
        $article->saveQuietly();
    }


    /**
     * @param \App\Models\Article $article
     */
    private function clearPublishedState(Article $article): void {
        $article->forceFill(['is_published' => false, 'published_at' => null]);
    }

}
