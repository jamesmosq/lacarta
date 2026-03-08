<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Tests that involve actual tenant database creation (CREATE DATABASE).
 * These use DatabaseMigrations instead of RefreshDatabase because
 * PostgreSQL cannot run CREATE DATABASE inside a transaction block.
 */
class TenantRegistrationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_register_creates_tenant_and_redirects(): void
    {
        $response = $this->post('/registro', [
            'restaurant_name'       => 'Mi Restaurante',
            'slug'                  => 'mi-restaurante',
            'email'                 => 'admin@mirestaurante.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tenants', [
            'slug'  => 'mi-restaurante',
            'email' => 'admin@mirestaurante.com',
        ]);

        $tenant = Tenant::find('mi-restaurante');
        if ($tenant) {
            $tenant->delete();
        }
    }

    public function test_register_prevents_duplicate_slug(): void
    {
        Tenant::create([
            'id'    => 'existente',
            'name'  => 'Existente',
            'email' => 'existente@test.com',
            'slug'  => 'existente',
            'plan'  => 'trial',
        ]);

        $response = $this->post('/registro', [
            'restaurant_name'       => 'Otro',
            'slug'                  => 'existente',
            'email'                 => 'otro@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['slug']);

        $tenant = Tenant::find('existente');
        if ($tenant) {
            $tenant->delete();
        }
    }
}
