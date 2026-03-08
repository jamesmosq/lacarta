<?php

namespace Tests\Feature;

use Tests\TenantTestCase;

class TenantAuthTest extends TenantTestCase
{
    public function test_login_page_returns_ok(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/admin/login");
        $response->assertStatus(200);

        tenancy()->end();
    }

    public function test_login_page_contains_form(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/admin/login");
        $response->assertSee('email');
        $response->assertSee('password');

        tenancy()->end();
    }

    public function test_valid_credentials_redirect_to_dashboard(): void
    {
        $response = $this->post("/{$this->tenant->id}/admin/login", [
            'email'    => "admin@{$this->tenant->id}.com",
            'password' => 'password',
        ]);

        $response->assertRedirect("/{$this->tenant->id}/admin/dashboard");

        tenancy()->end();
    }

    public function test_invalid_credentials_return_error(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->post("/{$this->tenant->id}/admin/login", [
            'email'    => "admin@{$this->tenant->id}.com",
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);

        tenancy()->end();
    }

    public function test_dashboard_requires_authentication(): void
    {
        tenancy()->initialize($this->tenant);

        $response = $this->get("/{$this->tenant->id}/admin/dashboard");
        $response->assertRedirect("/{$this->tenant->id}/admin/login");

        tenancy()->end();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $this->actingAsTenantUser();

        $response = $this->get("/{$this->tenant->id}/admin/dashboard");
        $response->assertStatus(200);

        tenancy()->end();
    }

    public function test_logout_redirects_to_login(): void
    {
        $this->actingAsTenantUser();

        $response = $this->post("/{$this->tenant->id}/admin/logout");
        $response->assertRedirect("/{$this->tenant->id}/admin/login");

        tenancy()->end();
    }
}
