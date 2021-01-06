<?php

namespace App\Observers;

use App\Events\ArticlePublished;
use App\Events\ArticleUnpublished;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ArticleObserver {

    /**
     * Handle the article "updated" event.
     *
     * @param \App\Models\Article $article
     * @return void
     */
    public function saved(Article $article) {
        if ($article->isDirty('is_published')) {
            try {
                if (!$article->is_published && !$article->wasRecentlyCreated) {
                    event(new ArticleUnpublished($article, Auth::user()));
                } elseif ($article->is_published) {
                    event(new ArticlePublished($article, Auth::user()));
                }
            } catch (\Exception $e) {
                Log::error(__METHOD__.': '.$e->getMessage().'-'.$e->getFile().':'.$e->getLine());
            }
        }
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
