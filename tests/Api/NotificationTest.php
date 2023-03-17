<?php


namespace Tests\Api;

use App\Models\Notification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_see_notifications_list(): void
    {
        Notification::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->json('get','/api/notifications/practitioner');
        $response->assertOk();
    }
}
