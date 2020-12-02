<?php

namespace Tests\Admin;

use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\MainPage;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MainPageTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    /**
     * @skip
     */
    public function test_admin_can_view_main_page(): void
    {
        MainPage::factory()->count(3)->create();

        $this->get('/admin/mainpage')->assertOk();
    }

    /**
     * @skip
     */
    public function test_admin_can_create_main_page(): void
    {
        $mainPage = MainPage::factory()->make();

        $this->put('/admin/mainpage', $mainPage->toArray())
            ->assertOk();
    }

    /**
     * @skip
     */
    public function test_admin_can_update_main_page(): void
    {
        $mainPage = MainPage::factory()->create();
        $mainPage->first();

        $featured_practitioners = User::factory()->count(4)->create();
        $featured_disciplines = Discipline::factory()->count(4)->create(['is_published' => true]);
        $featured_services = Service::factory()->count(4)->create();
        $featured_focus_areas = FocusArea::factory()->count(4)->create();

        $response = $this->json('put','/admin/mainpage',
            [
                'section_2_background' => '121212',
                'featured_practitioners' => $featured_practitioners->pluck('id'),
                'featured_disciplines' => $featured_disciplines->pluck('id'),
                'featured_services' => $featured_services->pluck('id'),
                'featured_focus_areas' => $featured_focus_areas->pluck('id')
            ]);

        $response->assertOk();

        $this->assertEquals($mainPage['section_2_background'], $mainPage->section_2_background);

        $mainPage = MainPage::first();
        self::assertCount(4, $mainPage->featured_practitioners);
        self::assertCount(4, $mainPage->featured_disciplines);
        self::assertCount(4, $mainPage->featured_services);
        self::assertCount(4, $mainPage->featured_focus_areas);
    }
}
