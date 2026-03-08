<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\Category;
use App\Models\Dish;
use App\Models\RestaurantTable;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'id'            => 'rincon-paisa',
            'name'          => 'El Rincón Paisa',
            'email'         => 'admin@rinconpaisa.com',
            'slug'          => 'rincon-paisa',
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        tenancy()->initialize($tenant);

        // Usuario administrador
        TenantUser::create([
            'name'     => 'Administrador',
            'email'    => 'admin@rinconpaisa.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        // Categorías
        $entradas = Category::create(['name' => 'Entradas',       'order' => 1, 'image' => 'https://images.unsplash.com/photo-1541014741259-de529411b96a?w=400&q=80']);
        $platos   = Category::create(['name' => 'Platos fuertes', 'order' => 2, 'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&q=80']);
        $bebidas  = Category::create(['name' => 'Bebidas',        'order' => 3, 'image' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&q=80']);
        $postres  = Category::create(['name' => 'Postres',        'order' => 4, 'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&q=80']);

        // Entradas
        Dish::create([
            'category_id' => $entradas->id,
            'name'        => 'Empanadas de pipián',
            'description' => 'Empanadas fritas rellenas de papa y hogao. Porción de 3 unidades.',
            'price'       => 8000,
            'image'       => 'https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $entradas->id,
            'name'        => 'Chicharrón crocante',
            'description' => 'Chicharrón de cerdo frito con limón y ají casero.',
            'price'       => 12000,
            'image'       => 'https://images.unsplash.com/photo-1544025162-d76594d57ac1?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $entradas->id,
            'name'        => 'Morcilla a la parrilla',
            'description' => 'Morcilla artesanal acompañada de arepa de choclo.',
            'price'       => 9000,
            'image'       => 'https://images.unsplash.com/photo-1529042410759-befb1204b468?w=400&q=80',
        ]);

        // Platos fuertes
        Dish::create([
            'category_id' => $platos->id,
            'name'        => 'Bandeja Paisa',
            'description' => 'Frijoles, arroz, carne molida, chicharrón, huevo frito, aguacate, arepa y plátano maduro.',
            'price'       => 32000,
            'image'       => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $platos->id,
            'name'        => 'Sancocho de gallina',
            'description' => 'Sancocho tradicional con papa, plátano y yuca. Incluye arroz y aguacate.',
            'price'       => 28000,
            'image'       => 'https://images.unsplash.com/photo-1604152135912-04a022e23696?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $platos->id,
            'name'        => 'Sobrebarriga al horno',
            'description' => 'Sobrebarriga marinada en salsa criolla, servida con papas y arroz blanco.',
            'price'       => 30000,
            'image'       => 'https://images.unsplash.com/photo-1558030006-450675393462?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $platos->id,
            'name'        => 'Cazuela de mariscos',
            'description' => 'Camarones, calamar y mejillones en salsa de leche de coco y especias.',
            'price'       => 38000,
            'image'       => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=400&q=80',
        ]);

        // Bebidas
        Dish::create([
            'category_id' => $bebidas->id,
            'name'        => 'Agua de panela con limón',
            'description' => 'Bebida tradicional colombiana servida fría.',
            'price'       => 4000,
            'image'       => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $bebidas->id,
            'name'        => 'Jugo de lulo natural',
            'description' => 'Jugo natural en agua o en leche. Sin azúcar disponible.',
            'price'       => 6000,
            'image'       => 'https://images.unsplash.com/photo-1534353341-8f9caab7b3ce?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $bebidas->id,
            'name'        => 'Jugo de maracuyá',
            'description' => 'Jugo natural en agua o en leche. Sin azúcar disponible.',
            'price'       => 6000,
            'image'       => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $bebidas->id,
            'name'        => 'Cerveza Club Colombia',
            'description' => 'Botella 330ml fría.',
            'price'       => 7000,
            'image'       => 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=400&q=80',
        ]);

        // Postres
        Dish::create([
            'category_id' => $postres->id,
            'name'        => 'Arroz con leche',
            'description' => 'Arroz con leche cremoso, canela y pasas. Receta de la abuela.',
            'price'       => 7000,
            'image'       => 'https://images.unsplash.com/photo-1586040140878-a6d1bcca1432?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $postres->id,
            'name'        => 'Flan de caramelo',
            'description' => 'Flan casero bañado en caramelo, servido frío.',
            'price'       => 8000,
            'image'       => 'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=400&q=80',
        ]);
        Dish::create([
            'category_id' => $postres->id,
            'name'        => 'Brownie con helado',
            'description' => 'Brownie de chocolate tibio con una bola de helado de vainilla.',
            'price'       => 10000,
            'image'       => 'https://images.unsplash.com/photo-1564355808539-22fda35bed7e?w=400&q=80',
        ]);

        // Mesas
        foreach (range(1, 8) as $i) {
            RestaurantTable::create(['name' => "Mesa $i"]);
        }
        RestaurantTable::create(['name' => 'Terraza A']);
        RestaurantTable::create(['name' => 'Terraza B']);
        RestaurantTable::create(['name' => 'Barra']);

        // Pedido de ejemplo para ver el panel con datos
        $order = Order::create([
            'table_id'      => 1,
            'status'        => 'preparing',
            'customer_name' => 'Carlos M.',
            'total'         => 70000,
            'notes'         => 'Sin cebolla en la bandeja',
        ]);
        $order->items()->createMany([
            ['dish_id' => 4, 'quantity' => 1, 'unit_price' => 32000],
            ['dish_id' => 8, 'quantity' => 2, 'unit_price' => 6000],
            ['dish_id' => 3, 'quantity' => 1, 'unit_price' => 9000],
            ['dish_id' => 12, 'quantity' => 1, 'unit_price' => 7000],
        ]);

        $order2 = Order::create([
            'table_id'      => 3,
            'status'        => 'pending',
            'customer_name' => 'Ana P.',
            'total'         => 44000,
        ]);
        $order2->items()->createMany([
            ['dish_id' => 7, 'quantity' => 1, 'unit_price' => 38000],
            ['dish_id' => 9, 'quantity' => 1, 'unit_price' => 6000],
        ]);

        tenancy()->end();

        $this->command->info('Tenant creado: rincon-paisa');
        $this->command->info('URL admin:       http://localhost:8000/rincon-paisa/admin/login');
        $this->command->info('Email:           admin@rinconpaisa.com');
        $this->command->info('Password:        password');
        $this->command->info('Menu publico:    http://localhost:8000/rincon-paisa/menu');
    }
}
