<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class ServiceSnapshot
 *
 * @property int id
 * @property int user_id
 * @property int is_published
 * @property int service_type_id
 * @property string url
 * @property string title
 * @property string string
 * @property string stripe_id
 * @property string description
 * @property string introduction
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property ServiceType service_type
 * @property Service service
 */
class ServiceSnapshot extends Service
{
    protected $fillable = [
        'title',
        'description',
        'introduction',
        'slug',
        'service_type_id',
        'stripe_id',
        'published_at',
        'is_published',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function keywords(): BelongsToMany
    {
        return $this->service->keywords();
    }

    public function disciplines()
    {
        return $this->service->disciplines();
    }
}
