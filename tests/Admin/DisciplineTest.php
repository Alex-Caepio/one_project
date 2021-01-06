<?php

namespace Tests\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\MediaImage;
use App\Models\MediaVideo;
use App\Models\MediaFile;
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
        $relatedDisciplines = Discipline::factory()->count(3)->create(['is_published' => true]);

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
       $response =  $this->json('post', '/admin/disciplines', [
            'name' => 'Heartbreaker',
            'url'  => 'http://wholelottalove.com',
        ]);
       $response->assertOk()
            ->assertJsonFragment(['url' => 'http://wholelottalove.com']);

        // 3. Check that same url or name can not be saved twice
        $this->json('post', '/admin/disciplines', [
            'name' => 'Heartbreaker',
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['name' => ['The name has already been taken.']]);

        $this->json('post', '/admin/disciplines', [
            'name' => 'Moby Dick',
            'url'  => 'http://wholelottalove.com',
        ])
            ->assertStatus(422)
            ->assertJsonFragment([
                'url' => ['The slug http://wholelottalove.com is not unique! Please, chose the different url.']
            ])
            ->assertJsonMissing(['name' => ['The name has already been taken.']]);

        $this->json('post', '/admin/disciplines', [
            'name' => 'Heartbreaker',
            'url'  => 'http://wholelottalove.com',
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['name' => ['The name has already been taken.']]);

        // 4. Check that generated url from name is unique
       $response = $this->json('post', '/admin/disciplines', ['name' => 'Heartbreaker']);
       $response->assertStatus(422)
            ->assertJsonFragment([
                'name' => ['The name has already been taken.']
            ]);
    }

    public function test_update_discipline(): void
    {
        $featuredServices = Service::factory()->count(3)->create();
        $practitoners     = User::factory()->count(3)->create();
        $mediaImage       = MediaImage::factory()->count(2)->create();
        $mediaVideo       = MediaVideo::factory()->count(2)->create();
        $mediaFile        = MediaFile::factory()->count(2)->create();

        /** @var Discipline $discipline */
        $discipline         = Discipline::factory()->create();
        $discipline['name'] = 'Discipline';

        $response = $this->json('put', '/admin/disciplines/' . $discipline->id, [
            'name'                   => 'name',
            'featured_practitioners' => $practitoners->pluck('id'),
            'featured_services'      => $featuredServices->pluck('id'),
        ]);

        $discipline->featured_practitioners()->sync($practitoners);
        $discipline->featured_services()->sync($featuredServices);

        $discipline->media_images()->delete();
        $discipline->media_videos()->delete();
        $discipline->media_files()->delete();

        $discipline->media_images()->saveMany($mediaImage);
        $discipline->media_videos()->saveMany($mediaVideo);
        $discipline->media_files()->saveMany($mediaFile);

        $response->assertOk()->assertJsonStructure(self::$disciplineStructure);

        $this->assertEquals($discipline['name'], $discipline->name);

        $discipline = Discipline::find($response->getOriginalContent()->id);
        self::assertCount(3, $discipline->featured_practitioners);
        self::assertCount(3, $discipline->featured_services);
        self::assertCount(2, $discipline->media_images);
        self::assertCount(2, $discipline->media_videos);
        self::assertCount(2, $discipline->media_files);
    }

    public function test_delete_discipline(): void
    {
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create();
        $mediaImage = MediaImage::factory()->create();
        $mediaVideo = MediaVideo::factory()->create();
        $mediaFile  = MediaFile::factory()->create();

        $response = $this->json('delete', "/admin/disciplines/{$discipline->id}");
        $discipline->media_images()->delete();
        $discipline->media_videos()->delete();
        $discipline->media_files()->delete();

        $this->assertDatabaseMissing('media_images', [
            'model_id' => $discipline->id,
        ]);
        $this->assertDatabaseMissing('media_videos', [
            'model_id' => $discipline->id,
        ]);
        $this->assertDatabaseMissing('media_files', [
            'model_id' => $discipline->id,
        ]);

        $response->assertStatus(204);
    }

    public function test_discipline_publish(): void
    {
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create();
        $this->json('post', "admin/disciplines/{$discipline->id}/publish",
            [
                'is_published' => true
            ])
            ->assertStatus(204);
    }

    public function test_discipline_unpublished(): void
    {
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create();
        $this->json('post', "admin/disciplines/{$discipline->id}/unpublish",
            [
                'is_published' => false
            ])
            ->assertStatus(204);
    }

    public function test_discipline_published_with_status_422(): void
    {
        /** @var Discipline $discipline */
        $discipline = Discipline::factory()->create([
            'url' => null
        ]);

        $this->json('post', "admin/disciplines/{$discipline->id}/publish")
            ->assertStatus(422);

    }

    public function test_index_can_search_disciplines(): void
    {
        $searchOne = Discipline::factory()->count(2)->create();

        $response = $this->json('get', '/admin/disciplines?search', ['search' => $searchOne[1]->name])
            ->assertJsonFragment(['url' => $searchOne[1]->url])
            ->assertJsonCount(1);;

        $response->assertOk();

        $response = $this->json('get', '/admin/disciplines?search', ['search' => $searchOne[0]->name]);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['url' => $searchOne[0]->url]);

        $response->assertOk();

    }
}
