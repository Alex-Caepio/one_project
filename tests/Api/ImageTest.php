<?php

namespace Tests\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Image;

class ImageTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_upload_image()
    {
        Storage::fake('public');

        $response = $this->json('post', 'api/images', [
            'title' => 'kek',
        ]);
        $response->assertOk()->assertJsonStructure([
            'url',
            'message?',
            'bytes' ,
            'mime',
            'original_extension' ,
            'original_filename'  ,
            'public_id' ,
            'signature?',
            'status'
        ]);
    }
}

