<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MediaImage
 *
 * @property int    id
 * @property string url
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = ['url'];

    public function morphesTo()
    {
        return $this->morphTo('morphesTo', 'model_name', 'model_id');
    }
}