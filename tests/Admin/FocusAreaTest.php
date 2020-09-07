<?php

namespace Tests\Api;

use App\Models\Article;
use App\Models\FocusArea;
use App\Models\FocusAreaVideo;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FocusAreaTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_can_get_all_focus_area(): void
    {
        factory(FocusArea::class, 2)->create();
        $response = $this->json('get', "/admin/focus-area");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'name', 'url'],
            ]);
    }

    public function test_store_focus_area(): void
    {
        $user = factory(User::class)->create();
        $service = factory(Service::class)->create();
        $article = factory(Article::class)->create();
        $focusArea = factory(FocusArea::class)->create();
        $response = $this->json('post', '/admin/focus-area', [
            'name' => $focusArea->name,
            'url' => $focusArea->url,
        ]);
        $focusArea->practitioners()->attach($user);
        $focusArea->services()->attach($service);
        $focusArea->articles()->attach($article);
        $response->assertOk($focusArea);
    }

    public function test_delete_focus_area(): void
    {
        $focusArea = factory(FocusArea::class)->create();
        $response = $this->json('delete', "admin/focus-area/$focusArea->id/destroy");
        $response->assertStatus(204);
    }

    public function test_update_focus_area(): void
    {
        $focusArea = factory(FocusArea::class)->create();
        $newFocusArea = factory(FocusArea::class)->make();

        $response = $this->json('put', "admin/focus-area/{$focusArea->id}/update",
            [
                'name' => $newFocusArea->name,
                'url' => $newFocusArea->url,
            ]);

        $response->assertOk()
            ->assertJson([
                'name' => $newFocusArea->name,
                'url' => $newFocusArea->url,
            ]);
    }
    public function test_store_video_focus_area(): void
    {
        $focus = factory(FocusArea::class)->create();
        $focusVideo = factory(FocusAreaVideo::class)->create();
        $response = $this->json('post', "/admin/focus-area/{$focus->id}/videos", [
            'discipline_id' => $focus->id,
            'link' => $focusVideo->link,
        ]);

        $response->assertOk();
    }
    public function test_store_image_focus_area(): void
    {
        $focus = factory(FocusArea::class)->create();
        Storage::fake('image');
        $response = $this->json('post', "/admin/focus-area/{$focus->id}/images", [
            'images' => $file = UploadedFile::fake()->image('images.jpg'),
            'focus_area_id' => $focus->id,
            'link' => $file->hashName()
        ])->assertStatus(200);
    }

}
