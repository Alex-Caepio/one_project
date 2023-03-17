<?php


namespace Tests\Api;

use App\Models\MainPage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MainPageTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    /**
     * @skip
     */
    public function test_admin_can_view_main_page(): void
    {
        MainPage::factory()->count(3)->create();

        $this->get('/api/mainpage')->assertOk();
    }
}
