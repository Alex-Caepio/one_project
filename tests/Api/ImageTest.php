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
        Storage::fake('local');

        $response = $this->json('post', 'api/images', [
            'title' => 'kek',
            'file'  => $file = UploadedFile::fake()->image('photo1.jpg'),
        ]);
        $response->assertOk()->assertJsonStructure(['url']);
    }
}

