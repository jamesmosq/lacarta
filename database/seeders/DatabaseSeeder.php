<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TenantSeeder::class);
        $this->call(SushiTenantSeeder::class);
        $this->call(PizzaTenantSeeder::class);
        $this->call(BurgerTenantSeeder::class);
    }
}
