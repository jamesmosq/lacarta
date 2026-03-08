<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

abstract class TenantTestCase extends TestCase
{
    use DatabaseMigrations;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = $this->createTenant();
    }

    protected function tearDown(): void
    {
        // Terminar tenancy antes de destruir
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        // Eliminar la BD del tenant de testing
        try {
            $this->tenant->database()->manager()->deleteDatabase($this->tenant);
        } catch (\Throwable $e) {
            // Ignorar si ya fue eliminada
        }

        parent::tearDown();
    }

    protected function createTenant(string $slug = 'test-restaurant'): Tenant
    {
        $tenant = Tenant::create([
            'id'            => $slug,
            'name'          => 'Test Restaurant',
            'email'         => "admin@{$slug}.com",
            'slug'          => $slug,
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        tenancy()->initialize($tenant);

        TenantUser::create([
            'name'     => 'Admin Test',
            'email'    => "admin@{$slug}.com",
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        tenancy()->end();

        return $tenant;
    }

    protected function actingAsTenantUser(Tenant $tenant = null): static
    {
        $tenant ??= $this->tenant;
        tenancy()->initialize($tenant);
        $user = TenantUser::first();
        $this->actingAs($user);
        return $this;
    }
}
