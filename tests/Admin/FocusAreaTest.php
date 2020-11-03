<?php

namespace Tests\Admin;

use App\Models\Article;
use App\Models\Discipline;
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

    public static $focusAreasStructure = [
        'id', 'name', 'url', 'description',
        'introduction', 'icon_url', 'banner_url',
    ];

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
            ->assertOk()->assertJsonStructure([self::$focusAreasStructure]);
    }

    public function test_show_discipline(): void
    {
        $focusArea = FocusArea::factory()->create();

        $this->json('get', "/admin/focus-areas/{$focusArea->id}")
            ->assertOk()
            ->assertJsonStructure(self::$focusAreasStructure);
    }

    public function test_store_focus_area(): void
    {
        $focusArea = FocusArea::factory()->make();

        $services = Service::factory()->count(3)->create();
        $articles = Article::factory()->count(3)->create();
        $disciplines = Discipline::factory()->count(3)->create(['is_published' => true]);

        $featured_practitioners = User::factory()->count(4)->create();
        $featured_disciplines = Discipline::factory()->count(4)->create(['is_published' => true]);
        $featured_articles = Article::factory()->count(4)->create();
        $featured_services = Service::factory()->count(4)->create();
        $featured_focus_areas = FocusArea::factory()->count(4)->create();

        $response = $this->json('post', '/admin/focus-areas', [
            'name' => $focusArea->name,
            'url' => $focusArea->url,
            'services' => $services->pluck('id'),
            'articles' => $articles->pluck('id'),
            'disciplines' => $disciplines->pluck('id'),

            'featured_practitioners' => $featured_practitioners->pluck('id'),
            'featured_disciplines' => $featured_disciplines->pluck('id'),
            'featured_articles' => $featured_articles->pluck('id'),
            'featured_services' => $featured_services->pluck('id'),
            'featured_focus_areas' => $featured_focus_areas->pluck('id')
        ]);

        $response->assertOk()->assertJsonStructure(self::$focusAreasStructure);

        $focusArea = FocusArea::find($response->getOriginalContent()->id);
        self::assertCount(3, $focusArea->services);
        self::assertCount(3, $focusArea->articles);
        self::assertCount(3, $focusArea->disciplines);
        self::assertCount(4, $focusArea->featured_practitioners);
        self::assertCount(4, $focusArea->featured_disciplines);
        self::assertCount(4, $focusArea->featured_articles);
        self::assertCount(4, $focusArea->featured_services);
        self::assertCount(4, $focusArea->featured_focus_areas);

    }

    public function test_delete_focus_area(): void
    {
        $focusArea = FocusArea::factory()->create();

        $response = $this->json('delete', "admin/focus-areas/$focusArea->id/destroy");
        $focusArea->services()->delete();
        $focusArea->articles()->delete();
        $focusArea->disciplines()->delete();

        $focusArea->featured_practitioners()->delete();
        $focusArea->featured_disciplines()->delete();
        $focusArea->featured_articles()->delete();
        $focusArea->featured_services()->delete();
        $focusArea->featured_focus_areas()->delete();

        $this->assertDatabaseMissing('focus_area_article', [
            'focus_area_id' => $focusArea->id,
        ]);
        $this->assertDatabaseMissing('focus_area_service', [
            'focus_area_id' => $focusArea->id,
        ]);
        $this->assertDatabaseMissing('discipline_focus_area', [
            'focus_area_id' => $focusArea->id,
        ]);

        $this->assertDatabaseMissing('focus_area_featured_user', [
            'focus_area_id' => $focusArea->id,
        ]);
        $this->assertDatabaseMissing('focus_area_featured_discipline', [
            'focus_area_id' => $focusArea->id,
        ]);
        $this->assertDatabaseMissing('focus_area_featured_article', [
            'focus_area_id' => $focusArea->id,
        ]);
        $this->assertDatabaseMissing('focus_area_featured_service', [
            'focus_area_id' => $focusArea->id,
        ]);
        $this->assertDatabaseMissing('focus_area_featured_focus_area', [
            'parent_focus_area_id' => $focusArea->id,
        ]);

        $response->assertStatus(204);
    }

    public function test_update_focus_area(): void
    {
        $focusArea = FocusArea::factory()->create();

        $services = Service::factory()->count(3)->create();
        $articles = Article::factory()->count(3)->create();
        $disciplines = Discipline::factory()->count(3)->create(['is_published' => true]);

        $featured_practitioners = User::factory()->count(4)->create();
        $featured_disciplines = Discipline::factory()->count(4)->create(['is_published' => true]);
        $featured_articles = Article::factory()->count(4)->create();
        $featured_services = Service::factory()->count(4)->create();
        $featured_focus_areas = FocusArea::factory()->count(4)->create();

        $response = $this->json('put', "admin/focus-areas/{$focusArea->id}/update",
            [
                'name' => 'new name',
                'url' => 'http://google.com',
                'services' => $services->pluck('id'),
                'articles' => $articles->pluck('id'),
                'disciplines' => $disciplines->pluck('id'),
                'featured_practitioners' => $featured_practitioners->pluck('id'),
                'featured_disciplines' => $featured_disciplines->pluck('id'),
                'featured_articles' => $featured_articles->pluck('id'),
                'featured_services' => $featured_services->pluck('id'),
                'featured_focus_areas' => $featured_focus_areas->pluck('id')
            ]);

        $focusArea->services()->sync($services);
        $focusArea->articles()->sync($articles);
        $focusArea->disciplines()->sync($disciplines);

        $focusArea->featured_practitioners()->sync($featured_practitioners);
        $focusArea->featured_disciplines()->sync($featured_disciplines);
        $focusArea->featured_articles()->sync($featured_articles);
        $focusArea->featured_services()->sync($featured_services);
        $focusArea->featured_focus_areas()->sync($featured_focus_areas);

        $response->assertOk()->assertJsonStructure(self::$focusAreasStructure);

        $this->assertEquals($focusArea['name'], $focusArea->name);
        $this->assertEquals($focusArea['url'], $focusArea->url);

        $focusArea = FocusArea::find($response->getOriginalContent()->id);
        self::assertCount(3, $focusArea->services);
        self::assertCount(3, $focusArea->articles);
        self::assertCount(3, $focusArea->disciplines);
        self::assertCount(4, $focusArea->featured_practitioners);
        self::assertCount(4, $focusArea->featured_disciplines);
        self::assertCount(4, $focusArea->featured_articles);
        self::assertCount(4, $focusArea->featured_services);
        self::assertCount(4, $focusArea->featured_focus_areas);
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

