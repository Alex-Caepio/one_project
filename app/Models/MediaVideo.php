<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $url
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MediaVideo extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'preview'];

    public function morphesTo()
    {
        return $this->morphTo('morphesTo', 'model_name', 'model_id');
    }
}
