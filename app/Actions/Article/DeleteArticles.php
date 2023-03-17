<?php

namespace App\Actions\Article;

use App\Models\User;

class DeleteArticles
{
    public function execute(User $user): void
    {
        $user->articles()->update([
            'deleted_at' => date('Y-m-d H:i:s'),
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
