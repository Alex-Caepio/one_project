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
        factory(Discipline::class, 2)->create();
        $response = $this->json('get', "/admin/discipline");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'title'],
            ]);
    }

    public function test_store_discipline(): void
    {
        $discipline = factory(Discipline::class)->create();
        $user = factory(User::class)->create();
        $service = factory(Service::class)->create();
        $article = factory(Article::class)->create();
        $focus_area = factory(FocusArea::class)->create();
        $response = $this->json('post', '/admin/discipline', [
            'title' => $discipline->title,
        ]);
        $discipline->practitioners()->attach($user);
        $discipline->services()->attach($service);
        $discipline->articles()->attach($article);
        $discipline->focus_areas()->attach($focus_area);
        $response->assertOk($discipline);
    }

    public function test_update_discipline(): void
    {
        $discipline = factory(Discipline::class)->create();
        $newDiscipline = factory(Discipline::class)->make();

        $response = $this->json('put', "admin/discipline/{$discipline->id}",
            [
                'title' => $newDiscipline->title,
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => $newDiscipline->title,
            ]);
    }

    public function test_delete_discipline(): void
    {
        $discipline = factory(Discipline::class)->create();
        $response = $this->json('delete', "/admin/discipline/{$discipline->id}");

        $response->assertStatus(204);
    }

    public function test_store_video_discipline(): void
    {
        $discipline = factory(Discipline::class)->create();
        $disciplineVideo = factory(DisciplineVideo::class)->create();
        $response = $this->json('post', "/admin/discipline/{$discipline->id}/videos", [
            'discipline_id' => $discipline->id,
            'link' => $disciplineVideo->link,
        ]);

        $response->assertOk();
    }
    public function test_store_image_discipline(): void
    {
        $discipline = factory(Discipline::class)->create();
        Storage::fake('public');
        $response = $this->json('post', "/admin/discipline/{$discipline->id}/images", [
            'image' => $file = UploadedFile::fake()->image('image.jpg'),
            'discipline_id' => $discipline->id,
            'link' => $file->hashName()
        ])->assertStatus(200);
    }

}
