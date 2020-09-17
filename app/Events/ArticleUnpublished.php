<?php

namespace App\Events;

use App\Models\Article;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleUnpublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $article;
    public $user;

    public function __construct(Article $article,User $user)
    {
        $this->article = $article;
        $this->user = $user;
    }
}
