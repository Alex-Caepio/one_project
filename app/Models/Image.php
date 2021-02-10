<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable=[
        'title', 'path', 'user_id', 'size'
    ];
    public $appends = ['url', 'uploaded_time', 'size_in_kb'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($image) {
            $image->user_id = auth()->user()->id;
        });
    }

    public function getUploadedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getSizeInKbAttribute()
    {
        return round($this->size / 1024, 2);
    }

    public function user()
    {
        return $this->belongsTo(User::class, );
    }
}
