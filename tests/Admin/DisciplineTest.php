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

    public function test_index_discipline(): void
    {
        Discipline::factory()->count(2)->create();
        $response = $this->json('get', "/admin/disciplines");

        $response->assertOk()
            ->assertJsonStructure([['id', 'name', 'url', 'description']]);
    }

    public function test_show_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $response   = $this->json('get', "/admin/disciplines/{$discipline->id}");

        $response
            ->assertOk();
    }

    public function test_store_discipline(): void
    {
        $discipline = Discipline::factory()->make();

        $featuredServices   = Service::factory()->count(3)->create();
        $practitoners       = User::factory()->count(3)->create();
        $focusAreas         = FocusArea::factory()->count(3)->create();
        $relatedDisciplines = Discipline::factory()->count(3)->create();

        $response = $this->json('post', '/admin/disciplines', [
            'name'                   => $discipline->name,
            'featured_practitioners' => $practitoners->pluck('id'),
            'featured_services'      => $featuredServices->pluck('id'),
            'focus_areas'            => $focusAreas->pluck('id'),
            'related_disciplines'    => $relatedDisciplines->pluck('id'),
        ]);
        $response->assertOk();

        //assert everything saved correctly into database
        $discipline = Discipline::find($response->getOriginalContent()->id);
        $this->assertCount(3, $discipline->featured_practitioners);
        $this->assertCount(3, $discipline->featured_services);
        $this->assertCount(3, $discipline->focus_areas);
        $this->assertCount(3, $discipline->related_disciplines);
    }

    public function test_url_saving(): void
    {
        // 1. Check that name is correctly converted into url
        $this->json('post', '/admin/disciplines', ['name' => 'Stairway to heaven!'])
            ->assertOk()
            ->assertJsonFragment(['url' => 'stairway-to-heaven']);

        // 2. Check that url field get advantage over name
        $this->json('post', '/admin/disciplines', [
            'name' => 'Heartbreaker',
            'url'  => 'whole-lotta-love',
        ])
            ->assertOk()
            ->assertJsonFragment(['url' => 'whole-lotta-love']);

        // 3. Check that same url or name can not be saved twice
        $this->json('post', '/admin/disciplines', [
            'name' => 'Heartbreaker',
            'url'  => 'whole-lotta-love',
        ])
            ->assertStatus(422)
            ->assertJsonFragment([
                'url' => ['The slug whole-lotta-love is not unique! Please, chose the different url.']
            ])
            ->assertJsonFragment(['name' => ['The name has already been taken.']]);

        // 4. Check that generated url from name is unique
        $this->json('post', '/admin/disciplines', ['name' => 'Whole lotta love'])
            ->assertStatus(422)
            ->assertJsonFragment([
                'name' => ['The slug whole-lotta-love is not unique! Please, chose the different name.']
            ]);
    }

    public function test_update_discipline(): void
    {
        $discipline    = Discipline::factory()->create();
        $newDiscipline = Discipline::factory()->make();

        $response = $this->json('put', "admin/disciplines/{$discipline->id}",
            [
                'name' => $newDiscipline->name,
                'url'  => $newDiscipline->url,
            ]);

        $response->assertOk();
    }

    public function test_delete_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $response   = $this->json('delete', "/admin/disciplines/{$discipline->id}");

        $response->assertStatus(204);
    }

    public function test_discipline_publish(): void
    {
        $discipline = Discipline::factory()->create();
        $response   = $this->json('post', "admin/disciplines/{$discipline->id}/publish", [
            'is_published' => true
        ]);
        $response->assertOk();
    }

    public function test_discipline_unpublished(): void
    {
        $discipline = Discipline::factory()->create();
        $response   = $this->json('post', "admin/disciplines/{$discipline->id}/unpublish", [
            'is_published' => false
        ]);
        $response->assertOk();
    }
}
