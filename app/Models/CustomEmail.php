<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $subject
 * @property string $from_title
 * @property string $from_email
 */
class CustomEmail extends Model
{
    use HasFactory;

    public const CLIENT_EMAIL = User::ACCOUNT_CLIENT;
    public const PRACTITIONER_EMAIL = User::ACCOUNT_PRACTITIONER;
    public const ALL_EMAIL = 'all';

    protected $fillable = [
        'logo',
        'logo_filename',
        'subject',
        'text',
        'delay',
        'from_email',
        'from_title',
        'footer',
        'user_type',
        'name',
    ];

    /**
     * @return string|null
     */
    public function getEmbedImageContent(): ?string
    {
        return !empty($this->logo) ? base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $this->logo)) : null;
    }
}
