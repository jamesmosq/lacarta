<?php

namespace Tests\Unit;

use App\Models\TenantUser;
use PHPUnit\Framework\TestCase;

class TenantUserTest extends TestCase
{
    public function test_is_owner_returns_true_for_owner_role(): void
    {
        $user = new TenantUser(['role' => 'owner']);
        $this->assertTrue($user->isOwner());
    }

    public function test_is_owner_returns_false_for_waiter_role(): void
    {
        $user = new TenantUser(['role' => 'waiter']);
        $this->assertFalse($user->isOwner());
    }

    public function test_is_owner_returns_false_for_kitchen_role(): void
    {
        $user = new TenantUser(['role' => 'kitchen']);
        $this->assertFalse($user->isOwner());
    }

    public function test_uses_users_table(): void
    {
        $user = new TenantUser();
        $this->assertSame('users', $user->getTable());
    }
}
