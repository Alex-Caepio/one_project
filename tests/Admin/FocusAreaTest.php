<?php

namespace Tests\Admin;

use App\Models\Article;
use App\Models\FocusArea;
use App\Models\FocusAreaImage;
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
        FocusArea::factory()->count(2)->create();
        $response = $this->json('get', "/admin/focus-area");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'name', 'url'],
            ]);
    }

    public function test_store_focus_area(): void
    {
        $user =User::factory()->create();
        $service = Service::factory()->create();
        $article = Article::factory()->create();
        $focusArea = FocusArea::factory()->create();
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
        $focusArea = FocusArea::factory()->create();
        $response = $this->json('delete', "admin/focus-area/$focusArea->id/destroy");
        $response->assertStatus(204);
    }

    public function test_update_focus_area(): void
    {
        $focusArea = FocusArea::factory()->create();
        $newFocusArea = FocusArea::factory()->make();

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
    public function test_store_videos_focus_area(): void
    {
        $focus = FocusArea::factory()->create();
        $focusVideo = FocusAreaVideo::factory()->create();
        $response = $this->json('post', "/admin/focus-area/{$focus->id}/videos", [
            'focus_area_id' => $focus->id,
            'link' => $focusVideo->link,
        ]);

        $response->assertOk();
    }
    public function test_store_images_focus_area(): void
    {
        $focus = FocusArea::factory()->make();
        $path = public_path('\img\focus-areas\images\\' . $focus->id . '\\');
        $file = UploadedFile::fake()->image('image.jpg');
        $fileName = $file->getClientOriginalName();
        Storage::files($path, $fileName);
        $focusImage = FocusAreaImage::factory()->make();
        $response = $this->json('post', "/admin/focus-area/{$focus->id}/images", [
            'focus_area_id' => $focus->id,
            'path' => $path,
        ]);
        $response->assertOk();

    }
    public function test_image_focus_area(): void
    {
        $focus = FocusArea::factory()->make();
        $path = public_path('\img\focus-areas\\' . $focus->id . '\\');
        $file = UploadedFile::fake()->image('image.jpg');
        $fileName = $file->getClientOriginalName();
        $this->json('post', 'admin/focus-area/{focusArea}/image', [
            'image' => $file,
        ]);
        Storage::files($path, $fileName);
    }
    public function test_icon_focus_area(): void
    {
        $focus = FocusArea::factory()->make();
        $path = public_path('\icon\focus-areas\\' . $focus->id . '\\');
        $file = UploadedFile::fake()->image('icon.jpg');
        $fileName = $file->getClientOriginalName();
        $this->json('post', 'admin/focus-area/{focusArea}/icon', [
            'icon' => $file,
        ]);
        Storage::files($path, $fileName);
    }

}
