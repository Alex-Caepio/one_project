<?php

namespace App\Actions\Article;

use App\Models\User;

class UnpublishArticles
{
    public function execute(User $user, array $articlesIds = []): void
    {
        $query = $user->articles();

        if ($articlesIds) {
            $query->whereIn('id', $articlesIds);
        }

        $query->update(['is_published' => false]);
    }
}
