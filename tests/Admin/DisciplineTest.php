<?php

namespace Tests\Admin;

use App\Models\Article;
use App\Models\Discipline;
use App\Models\DisciplineVideo;
use App\Models\FocusArea;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DisciplineTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_all_discipline(): void
    {
        Discipline::factory()->count(2)->create();
        $response = $this->json('get', "/admin/discipline");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'name','url'],
            ]);
    }
    public function test_show_discipline(): void
    {
        $discipline=Discipline::factory()->create();
        $response = $this->json('get', "/admin/discipline/{$discipline->id}");

        $response
            ->assertOk();
    }

    public function test_store_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $user = User::factory()->create();
        $service = Service::factory()->create();
        $article = Article::factory()->create();
        $focus_area = FocusArea::factory()->create();
        $response = $this->json('post', '/admin/discipline', [
            'name' => $discipline->name,
            'url' => $discipline->url,
        ]);
        $discipline->practitioners()->attach($user);
        $discipline->services()->attach($service);
        $discipline->articles()->attach($article);
        $discipline->focus_areas()->attach($focus_area);
        $response->assertOk($discipline);
    }

    public function test_update_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $newDiscipline = Discipline::factory()->make();

        $response = $this->json('put', "admin/discipline/{$discipline->id}",
            [
                'name' => $newDiscipline->name,
                'url' => $newDiscipline->url,
            ]);

        $response->assertOk();
    }

    public function test_delete_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $response = $this->json('delete', "/admin/discipline/{$discipline->id}");

        $response->assertStatus(204);
    }

    public function test_store_video_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $disciplineVideo = DisciplineVideo::factory()->create();
        $response = $this->json('post', "/admin/discipline/{$discipline->id}/videos", [
            'discipline_id' => $discipline->id,
            'link' => $disciplineVideo->link,
        ]);

        $response->assertOk();
    }

    public function test_store_image_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        Storage::fake('public');
        $response = $this->json('post', "/admin/discipline/{$discipline->id}/images", [
            'image' => $file = UploadedFile::fake()->image('image.jpg'),
            'discipline_id' => $discipline->id,
            'link' => $file->hashName()
        ])->assertStatus(200);
    }
    public function test_discipline_publish(): void
    {
        $discipline = Discipline::factory()->create();
        $response = $this->json('post', "admin/discipline/{$discipline->id}/publish", [
            'is_published' => true
        ]);
        $response->assertOk();
    }
    public function test_discipline_unpublished(): void
    {
        $discipline = Discipline::factory()->create();
        $response = $this->json('post', "admin/discipline/{$discipline->id}/publish", [
            'is_published' => false
        ]);
        $response->assertOk();
    }
}
