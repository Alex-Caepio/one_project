<?php

namespace Tests;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createUser(array $attributes = [])
    {
        $attributes = array_merge(
            [
                'password' => Hash::make('test'),
            ],
            $attributes
        );

        return User::factory()->create($attributes);
    }

    public function createAdmin(array $attributes = [])
    {
        $attributes = array_merge(
            [
                'password' => Hash::make('12dsfsdDDD'),
                'is_admin' => 1
            ],
            $attributes
        );

        return User::factory()->create($attributes);
    }

    public function login($user)
    {
        return Sanctum::actingAs($user, ['*']);
    }
}
