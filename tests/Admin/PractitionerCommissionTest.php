<?php


namespace Tests\Admin;

use App\Models\Plan;
use App\Models\PractitionerCommission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PractitionerCommissionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_get_all_practitionerCommission()
    {
        $plan = Plan::factory()->create(['commission_on_sale' => '66']);
        $user = User::factory()->create(['account_type' => 'practitioner', 'plan_id' => $plan->id]);
        PractitionerCommission::factory()->create(['practitioner_id' => $user->id]);

        $response = $this->actingAs($this->user)->json('get', "/api/admin/practitioner-commissions");
        $response->assertOk();
    }

    public function test_show_practitionerCommission()
    {
        $user = User::factory()->make(['account_type' => 'practitioner', 'id' => 1]);
        $pc   = PractitionerCommission::factory()->create(['practitioner_id' => $user->id]);
        $response = $this->actingAs($this->user)->json('get', "/admin/practitioner-commissions/{$pc->id}");

        $response
            ->assertOk();
    }

    public function test_create_practitionerCommission()
    {
        $user = User::factory()->create(['account_type' => 'practitioner','id' => 1]);
        $pc   = PractitionerCommission::factory()->create();
        $payload = [
            'practitioner_id' => $user->id,
            'date_from'       => $pc->date_from,
            'date_to'         => $pc->date_to,
            'rate'            => 30,
            'is_dateless'     => false
        ];

        $response = $this->actingAs($this->user)->json('post', "/admin/practitioner-commissions",$payload);

        $response
            ->assertOk();
    }

    public function test_update_practitionerCommission()
    {
        User::factory()->create(['account_type' => 'practitioner','id' => 1]);
        $pc   = PractitionerCommission::factory()->create(['is_dateless' => false]);

        $payload = [
            'date_from'       => $pc->date_from,
            'date_to'         => $pc->date_to,
            'rate'            => 30,
            'is_dateless'     => false
        ];

        $response = $this->actingAs($this->user)->json('put', "/admin/practitioner-commissions/".$pc->id,
            $payload);

        $response
            ->assertOk();
    }

    public function test_delete_practitionerCommission(): void
    {
        $pc = PractitionerCommission::factory()->create(['is_dateless' => false]);
        $response = $this->json('delete', "/admin/practitioner-commissions/{$pc->id}");

        $response->assertStatus(204);
    }
}
