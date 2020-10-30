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
        $response = $this->json('get', "/admin/focus-areas");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'name', 'url'],
            ]);
    }

    public function test_store_focus_area(): void
    {
        $focusArea = FocusArea::factory()->make();
        $response = $this->json('post', '/admin/focus-areas', [
            'name' => $focusArea->name,
            'url' => $focusArea->url,
        ]);
        $response->assertOk($focusArea);
    }

    public function test_delete_focus_area(): void
    {
        $focusArea = FocusArea::factory()->create();
        $response = $this->json('delete', "admin/focus-areas/$focusArea->id/destroy");
        $response->assertStatus(204);
    }

    public function test_update_focus_area(): void
    {
        $focusArea = FocusArea::factory()->create();
        $newFocusArea = FocusArea::factory()->make();

        $response = $this->json('put', "admin/focus-areas/{$focusArea->id}/update",
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
        $response = $this->json('post', "/admin/focus-areas/{$focus->id}/videos", [
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
        FocusAreaImage::factory()->make();
        $response = $this->json('post', "admin/focus-areas/{$focus->id}/images", [
            'focus_area_id' => $focus->id,
            'path' => $path,
        ]);
        Storage::files($path, $fileName);

    }
    public function test_image_focus_area(): void
    {
        $focus = FocusArea::factory()->make();
        $path = public_path('\img\focus-areas\\' . $focus->id . '\\');
        $file = UploadedFile::fake()->image('image.jpg');
        $fileName = $file->getClientOriginalName();
        $this->json('post', "admin/focus-areas/{$focus->id}/image", [
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
        $this->json('post', "admin/focus-areas/{$focus->id}/icon", [
            'icon' => $file,
        ]);
        Storage::files($path, $fileName);
    }

}
