<?php


namespace App\Actions\Article;

use App\Http\Requests\Admin\ArticleUpdateRequest;
use App\Models\Article;

class AdminArticleUpdate extends AdminArticleAction {

    /**
     * @param \App\Http\Requests\Admin\ArticleUpdateRequest $request
     * @param \App\Models\Article $article
     * @return \App\Models\Article
     */
    public function execute(ArticleUpdateRequest $request, Article $article) {

        $this->updateArticle($article, $request);
        return $article;
    }

}
