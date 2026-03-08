<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CentralRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_returns_ok(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_landing_page_contains_brand_name(): void
    {
        $response = $this->get('/');
        $response->assertSee('LaCarta');
    }

    public function test_register_page_returns_ok(): void
    {
        $response = $this->get('/registro');
        $response->assertStatus(200);
    }

    public function test_register_page_contains_form_fields(): void
    {
        $response = $this->get('/registro');
        $response->assertSee('restaurant_name');
        $response->assertSee('slug');
        $response->assertSee('email');
        $response->assertSee('password');
    }

    public function test_register_validates_required_fields(): void
    {
        $response = $this->post('/registro', []);
        $response->assertSessionHasErrors(['restaurant_name', 'slug', 'email', 'password']);
    }

    public function test_register_validates_slug_format(): void
    {
        $response = $this->post('/registro', [
            'restaurant_name'      => 'Test',
            'slug'                 => 'Invalid Slug!',
            'email'                => 'test@test.com',
            'password'             => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['slug']);
    }

    public function test_register_validates_password_confirmation(): void
    {
        $response = $this->post('/registro', [
            'restaurant_name'      => 'Test',
            'slug'                 => 'test',
            'email'                => 'test@test.com',
            'password'             => 'password',
            'password_confirmation' => 'diferente',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

}
