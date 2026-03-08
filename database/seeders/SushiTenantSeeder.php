<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\Category;
use App\Models\Dish;
use App\Models\RestaurantTable;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SushiTenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'id'            => 'sakura-sushi',
            'name'          => 'Sakura Sushi',
            'email'         => 'admin@sakurasushi.com',
            'slug'          => 'sakura-sushi',
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        tenancy()->initialize($tenant);

        TenantUser::create([
            'name'     => 'Administrador',
            'email'    => 'admin@sakurasushi.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        $rolls    = Category::create(['name' => 'Rolls especiales', 'order' => 1, 'image' => 'https://images.unsplash.com/photo-1617196034183-421b4040ed20?w=400&q=80']);
        $nigiris  = Category::create(['name' => 'Nigiris y Sashimi', 'order' => 2, 'image' => 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=400&q=80']);
        $entradas = Category::create(['name' => 'Entradas', 'order' => 3, 'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&q=80']);
        $bebidas  = Category::create(['name' => 'Bebidas', 'order' => 4, 'image' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&q=80']);

        Dish::create(['category_id' => $rolls->id, 'name' => 'Dragon Roll', 'description' => 'Camaron tempura, aguacate, pepino y anguila encima. 8 piezas.', 'price' => 42000, 'image' => 'https://images.unsplash.com/photo-1617196034183-421b4040ed20?w=400&q=80']);
        Dish::create(['category_id' => $rolls->id, 'name' => 'Philadelphia Roll', 'description' => 'Salmon fresco, queso crema y pepino. 8 piezas.', 'price' => 36000, 'image' => 'https://images.unsplash.com/photo-1562802378-063ec186a863?w=400&q=80']);
        Dish::create(['category_id' => $rolls->id, 'name' => 'Spicy Tuna Roll', 'description' => 'Atun con salsa spicy, pepino y sesamo. 8 piezas.', 'price' => 38000, 'image' => 'https://images.unsplash.com/photo-1611143669185-af224c5e3252?w=400&q=80']);
        Dish::create(['category_id' => $rolls->id, 'name' => 'Rainbow Roll', 'description' => 'California roll cubierto con variedad de pescados y aguacate. 8 piezas.', 'price' => 48000, 'image' => 'https://images.unsplash.com/photo-1559410545-0bdcd187e0a6?w=400&q=80']);
        Dish::create(['category_id' => $rolls->id, 'name' => 'Volcano Roll', 'description' => 'Camaron tempura con mezcla picante gratinada encima. 8 piezas.', 'price' => 44000, 'image' => 'https://images.unsplash.com/photo-1534482421-64566f976cfa?w=400&q=80']);

        Dish::create(['category_id' => $nigiris->id, 'name' => 'Nigiri de salmon', 'description' => 'Arroz de sushi con lamina de salmon fresco. 2 piezas.', 'price' => 18000, 'image' => 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=400&q=80']);
        Dish::create(['category_id' => $nigiris->id, 'name' => 'Nigiri de atun', 'description' => 'Arroz de sushi con lamina de atun rojo. 2 piezas.', 'price' => 20000, 'image' => 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?w=400&q=80']);
        Dish::create(['category_id' => $nigiris->id, 'name' => 'Sashimi mixto', 'description' => 'Seleccion de salmon, atun y pez blanco. 9 laminas.', 'price' => 52000, 'image' => 'https://images.unsplash.com/photo-1617196034099-f4c6d4e4e0b0?w=400&q=80']);

        Dish::create(['category_id' => $entradas->id, 'name' => 'Edamame', 'description' => 'Vainas de soya al vapor con sal de mar.', 'price' => 12000, 'image' => 'https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=400&q=80']);
        Dish::create(['category_id' => $entradas->id, 'name' => 'Gyozas de cerdo', 'description' => 'Dumplings fritos rellenos de cerdo y vegetales. 6 unidades con salsa ponzu.', 'price' => 22000, 'image' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=400&q=80']);
        Dish::create(['category_id' => $entradas->id, 'name' => 'Miso Soup', 'description' => 'Sopa tradicional japonesa con tofu, alga y cebolla verde.', 'price' => 10000, 'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&q=80']);

        Dish::create(['category_id' => $bebidas->id, 'name' => 'Sake frio', 'description' => 'Sake japones servido frio. 180ml.', 'price' => 24000, 'image' => 'https://images.unsplash.com/photo-1560963689-b5682b6440f8?w=400&q=80']);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Te verde japones', 'description' => 'Te verde Sencha caliente o frio.', 'price' => 8000, 'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80']);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Limonada de jengibre', 'description' => 'Limonada fresca con jengibre natural y miel.', 'price' => 10000, 'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=400&q=80']);

        foreach (range(1, 6) as $i) {
            RestaurantTable::create(['name' => "Mesa $i"]);
        }
        RestaurantTable::create(['name' => 'Barra 1']);
        RestaurantTable::create(['name' => 'Barra 2']);
        RestaurantTable::create(['name' => 'Barra 3']);

        $o1 = Order::create(['table_id' => 2, 'status' => 'preparing', 'customer_name' => 'Sofia R.', 'total' => 86000]);
        $o1->items()->createMany([
            ['dish_id' => 1, 'quantity' => 1, 'unit_price' => 42000],
            ['dish_id' => 2, 'quantity' => 1, 'unit_price' => 36000],
            ['dish_id' => 13, 'quantity' => 1, 'unit_price' => 8000],
        ]);

        $o2 = Order::create(['table_id' => 5, 'status' => 'pending', 'customer_name' => 'Miguel A.', 'total' => 84000]);
        $o2->items()->createMany([
            ['dish_id' => 8, 'quantity' => 1, 'unit_price' => 52000],
            ['dish_id' => 10, 'quantity' => 1, 'unit_price' => 22000],
            ['dish_id' => 14, 'quantity' => 1, 'unit_price' => 10000],
        ]);

        $o3 = Order::create(['table_id' => 1, 'status' => 'ready', 'customer_name' => 'Laura G.', 'total' => 64000]);
        $o3->items()->createMany([
            ['dish_id' => 3, 'quantity' => 1, 'unit_price' => 38000],
            ['dish_id' => 6, 'quantity' => 1, 'unit_price' => 18000],
            ['dish_id' => 13, 'quantity' => 1, 'unit_price' => 8000],
        ]);

        tenancy()->end();

        $this->command->info('Tenant creado: sakura-sushi | admin@sakurasushi.com / password');
    }
}
