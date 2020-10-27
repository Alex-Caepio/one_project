<?php

namespace Tests\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\FocusArea;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DisciplineTest extends TestCase
{
    use DatabaseTransactions;

    public static $disciplineStructure = [
        'id', 'name', 'url', 'description',
        'introduction', 'icon_url', 'banner_url',
        'is_published'
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_index_discipline(): void
    {
        Discipline::factory()->count(10)->create();
        $this->json('get', "/admin/disciplines")
            ->assertOk()
            ->assertJsonStructure([self::$disciplineStructure]);
    }

    public function test_show_discipline(): void
    {
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create();

        //find by id
        $this->json('get', "/admin/disciplines/{$discipline->id}")
            ->assertOk()
            ->assertJsonStructure(self::$disciplineStructure);

        //find by url
        $this->json('get', "/admin/disciplines/{$discipline->url}")
            ->assertOk()
            ->assertJsonStructure(self::$disciplineStructure);
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
            'media_images'           => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ],
            'media_videos'           => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ],
            'media_files'            => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ],
        ]);
        $response->assertOk()->assertJsonStructure(self::$disciplineStructure);

        //assert everything saved correctly into database
        $discipline = Discipline::find($response->getOriginalContent()->id);
        self::assertCount(3, $discipline->featured_practitioners);
        self::assertCount(3, $discipline->featured_services);
        self::assertCount(3, $discipline->focus_areas);
        self::assertCount(3, $discipline->related_disciplines);
        self::assertCount(2, $discipline->media_images);
        self::assertCount(2, $discipline->media_videos);
        self::assertCount(2, $discipline->media_files);
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
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create();
        /** @var Discipline $newDiscipline */
        $newDiscipline = Discipline::factory()->make();

        $this->json('put', "admin/disciplines/{$discipline->id}",
            [
                'name' => $newDiscipline->name,
                'url'  => $newDiscipline->url,
            ])
            ->assertOk()
            ->assertJsonStructure(self::$disciplineStructure);
    }

    public function test_delete_discipline(): void
    {
        /** @var Discipline $discipline */
        $discipline        = Discipline::factory()->create();
        $featuredService   = Service::factory()->create();
        $practitoner       = User::factory()->create();
        $focusArea         = FocusArea::factory()->create();
        $relatedDiscipline = Discipline::factory()->create();

        $discipline->featured_practitioners()->attach($practitoner->id);
        $discipline->featured_services()->attach($featuredService->id);
        $discipline->focus_areas()->attach($focusArea->id);
        $discipline->related_disciplines()->attach($relatedDiscipline->id);

        $this->json('delete', "/admin/disciplines/{$discipline->id}")
            ->assertStatus(204);

        $this->assertDeleted($discipline);

        //assert relations are deleted
        $this->assertEquals(0, $discipline->featured_practitioners()->count());
        $this->assertEquals(0, $discipline->featured_services()->count());
        $this->assertEquals(0, $discipline->focus_areas()->count());
        $this->assertEquals(0, $discipline->related_disciplines()->count());
    }

    public function test_discipline_publish(): void
    {
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create();
        $this->json('post', "admin/disciplines/{$discipline->id}/publish", [
            'is_published' => true
        ])
            ->assertOk()
            ->assertJsonStructure(self::$disciplineStructure);
    }

    public function test_discipline_unpublished(): void
    {
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create();
        $this->json('post', "admin/disciplines/{$discipline->id}/unpublish", [
            'is_published' => false
        ])
            ->assertOk()
            ->assertJsonStructure(self::$disciplineStructure);
    }
}
