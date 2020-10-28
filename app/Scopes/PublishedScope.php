<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait PublishedScope {

    public function scopePublished(Builder $query)
    {
        return $query->where('is_published', true);
    }
}
