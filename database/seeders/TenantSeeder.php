<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\Category;
use App\Models\Dish;
use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Crear restaurante de prueba
        $tenant = Tenant::create([
            'id'            => 'rincon-paisa',
            'name'          => 'El Rincón Paisa',
            'email'         => 'admin@rinconpaisa.com',
            'slug'          => 'rincon-paisa',
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Inicializar el tenant (cambia a su BD)
        tenancy()->initialize($tenant);

        // Usuario administrador
        TenantUser::create([
            'name'     => 'Administrador',
            'email'    => 'admin@rinconpaisa.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        // Categorías del menú
        $entradas = Category::create(['name' => 'Entradas', 'order' => 1]);
        $platos   = Category::create(['name' => 'Platos Fuertes', 'order' => 2]);
        $bebidas  = Category::create(['name' => 'Bebidas', 'order' => 3]);
        $postres  = Category::create(['name' => 'Postres', 'order' => 4]);

        // Entradas
        Dish::create(['category_id' => $entradas->id, 'name' => 'Empanadas de pipián', 'description' => 'Empanadas fritas rellenas de papa y hogao. Porción x3', 'price' => 8000]);
        Dish::create(['category_id' => $entradas->id, 'name' => 'Chicharrón', 'description' => 'Chicharrón crocante con ají y limón', 'price' => 12000]);
        Dish::create(['category_id' => $entradas->id, 'name' => 'Morcilla', 'description' => 'Morcilla a la parrilla con arepa', 'price' => 9000]);

        // Platos fuertes
        Dish::create(['category_id' => $platos->id, 'name' => 'Bandeja Paisa', 'description' => 'Frijoles, arroz, carne molida, chicharrón, huevo, aguacate, arepa y plátano', 'price' => 32000]);
        Dish::create(['category_id' => $platos->id, 'name' => 'Sancocho de gallina', 'description' => 'Sancocho tradicional con papa, plátano y yuca. Incluye arroz y aguacate', 'price' => 28000]);
        Dish::create(['category_id' => $platos->id, 'name' => 'Sobrebarriga al horno', 'description' => 'Sobrebarriga en salsa criolla con papas y arroz', 'price' => 30000]);
        Dish::create(['category_id' => $platos->id, 'name' => 'Cazuela de mariscos', 'description' => 'Cazuela con camarones, calamar y mejillones en leche de coco', 'price' => 38000]);

        // Bebidas
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Agua de panela con limón', 'price' => 4000]);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Jugo de lulo', 'description' => 'Natural en agua o en leche', 'price' => 6000]);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Jugo de maracuyá', 'description' => 'Natural en agua o en leche', 'price' => 6000]);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Cerveza Club Colombia', 'price' => 7000]);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Gaseosa', 'description' => 'Coca-Cola, Sprite o Manzana. 250ml', 'price' => 4000]);

        // Postres
        Dish::create(['category_id' => $postres->id, 'name' => 'Arroz con leche', 'description' => 'Arroz con leche con canela y pasas', 'price' => 7000]);
        Dish::create(['category_id' => $postres->id, 'name' => 'Flan de caramelo', 'price' => 8000]);

        // Mesas
        foreach (range(1, 8) as $i) {
            RestaurantTable::create(['name' => "Mesa $i"]);
        }
        RestaurantTable::create(['name' => 'Terraza A']);
        RestaurantTable::create(['name' => 'Terraza B']);
        RestaurantTable::create(['name' => 'Barra']);

        tenancy()->end();

        $this->command->info('✅ Tenant creado: rincon-paisa');
        $this->command->info('   URL admin:  http://localhost:8000/rincon-paisa/admin/login');
        $this->command->info('   Email:      admin@rinconpaisa.com');
        $this->command->info('   Password:   password');
        $this->command->info('   Menú público: http://localhost:8000/rincon-paisa/menu');
    }
}
