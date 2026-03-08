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

class PizzaTenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'id'            => 'la-piazza',
            'name'          => 'La Piazza',
            'email'         => 'admin@lapiazza.com',
            'slug'          => 'la-piazza',
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        tenancy()->initialize($tenant);

        TenantUser::create([
            'name'     => 'Administrador',
            'email'    => 'admin@lapiazza.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        $pizzas   = Category::create(['name' => 'Pizzas', 'order' => 1, 'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=80']);
        $pastas   = Category::create(['name' => 'Pastas', 'order' => 2, 'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400&q=80']);
        $entradas = Category::create(['name' => 'Entradas', 'order' => 3, 'image' => 'https://images.unsplash.com/photo-1541014741259-de529411b96a?w=400&q=80']);
        $postres  = Category::create(['name' => 'Postres', 'order' => 4, 'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&q=80']);
        $bebidas  = Category::create(['name' => 'Bebidas', 'order' => 5, 'image' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&q=80']);

        // Pizzas
        Dish::create(['category_id' => $pizzas->id, 'name' => 'Margherita', 'description' => 'Tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva. Masa artesanal.', 'price' => 35000, 'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=400&q=80']);
        Dish::create(['category_id' => $pizzas->id, 'name' => 'Pepperoni', 'description' => 'Salsa de tomate, mozzarella abundante y pepperoni importado.', 'price' => 40000, 'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=80']);
        Dish::create(['category_id' => $pizzas->id, 'name' => 'Cuatro quesos', 'description' => 'Mozzarella, gorgonzola, parmesano y brie sobre base blanca.', 'price' => 44000, 'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400&q=80']);
        Dish::create(['category_id' => $pizzas->id, 'name' => 'Trufa y champinones', 'description' => 'Base blanca con aceite de trufa, champinones salteados y rucola fresca.', 'price' => 52000, 'image' => 'https://images.unsplash.com/photo-1571407970349-bc81e7e96d47?w=400&q=80']);
        Dish::create(['category_id' => $pizzas->id, 'name' => 'Pollo BBQ', 'description' => 'Salsa BBQ, pollo a la plancha, cebolla caramelizada y cilantro.', 'price' => 42000, 'image' => 'https://images.unsplash.com/photo-1555072956-7758afb20e8f?w=400&q=80']);

        // Pastas
        Dish::create(['category_id' => $pastas->id, 'name' => 'Spaghetti Carbonara', 'description' => 'Pasta fresca con panceta, huevo, parmesano y pimienta negra.', 'price' => 36000, 'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400&q=80']);
        Dish::create(['category_id' => $pastas->id, 'name' => 'Penne Arrabiata', 'description' => 'Salsa de tomate picante con ajo, chile y albahaca fresca.', 'price' => 30000, 'image' => 'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=400&q=80']);
        Dish::create(['category_id' => $pastas->id, 'name' => 'Fettuccine al Salmon', 'description' => 'Fettuccine en salsa de crema con salmon ahumado y alcaparras.', 'price' => 42000, 'image' => 'https://images.unsplash.com/photo-1556761223-4c4282c73f77?w=400&q=80']);
        Dish::create(['category_id' => $pastas->id, 'name' => 'Lasagna de carne', 'description' => 'Lasagna tradicional con ragu de carne, bechamel y parmesano gratinado.', 'price' => 38000, 'image' => 'https://images.unsplash.com/photo-1574894709920-11b28be1e274?w=400&q=80']);

        // Entradas
        Dish::create(['category_id' => $entradas->id, 'name' => 'Bruschetta clasica', 'description' => 'Pan tostado con tomate, ajo, albahaca y aceite de oliva extra virgen.', 'price' => 18000, 'image' => 'https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?w=400&q=80']);
        Dish::create(['category_id' => $entradas->id, 'name' => 'Tabla de antipasto', 'description' => 'Jamon serrano, aceitunas, quesos curados y vegetales marinados.', 'price' => 38000, 'image' => 'https://images.unsplash.com/photo-1541014741259-de529411b96a?w=400&q=80']);
        Dish::create(['category_id' => $entradas->id, 'name' => 'Sopa minestrone', 'description' => 'Sopa italiana de vegetales con pasta y parmesano.', 'price' => 20000, 'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&q=80']);

        // Postres
        Dish::create(['category_id' => $postres->id, 'name' => 'Tiramisu', 'description' => 'Clasico italiano con mascarpone, cafe espresso y cacao. Receta original.', 'price' => 18000, 'image' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=400&q=80']);
        Dish::create(['category_id' => $postres->id, 'name' => 'Panna cotta de frutos rojos', 'description' => 'Crema italiana con coulis de frutos rojos frescos.', 'price' => 16000, 'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&q=80']);

        // Bebidas
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Vino tinto de la casa', 'description' => 'Copa de vino tinto seleccionado por nuestro sommelier. 150ml.', 'price' => 22000, 'image' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=400&q=80']);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Agua con gas San Pellegrino', 'description' => 'Botella 500ml.', 'price' => 10000, 'image' => 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400&q=80']);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Cafe espresso', 'description' => 'Espresso doble en taza de ceramica italiana.', 'price' => 8000, 'image' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80']);

        // Mesas
        foreach (range(1, 10) as $i) {
            RestaurantTable::create(['name' => "Mesa $i"]);
        }
        RestaurantTable::create(['name' => 'Terraza 1']);
        RestaurantTable::create(['name' => 'Terraza 2']);

        // Pedidos
        $o1 = Order::create(['table_id' => 3, 'status' => 'preparing', 'customer_name' => 'Familia Torres', 'total' => 124000, 'notes' => 'Una pizza sin gluten si es posible']);
        $o1->items()->createMany([
            ['dish_id' => 1, 'quantity' => 1, 'unit_price' => 35000],
            ['dish_id' => 3, 'quantity' => 1, 'unit_price' => 44000],
            ['dish_id' => 6, 'quantity' => 1, 'unit_price' => 36000],
            ['dish_id' => 15, 'quantity' => 1, 'unit_price' => 22000],
        ]);

        $o2 = Order::create(['table_id' => 7, 'status' => 'pending', 'customer_name' => 'David M.', 'total' => 68000]);
        $o2->items()->createMany([
            ['dish_id' => 4, 'quantity' => 1, 'unit_price' => 52000],
            ['dish_id' => 13, 'quantity' => 1, 'unit_price' => 18000],
        ]);

        $o3 = Order::create(['table_id' => 11, 'status' => 'delivered', 'customer_name' => 'Pareja mesa terraza', 'total' => 114000]);
        $o3->items()->createMany([
            ['dish_id' => 2, 'quantity' => 1, 'unit_price' => 40000],
            ['dish_id' => 8, 'quantity' => 1, 'unit_price' => 42000],
            ['dish_id' => 14, 'quantity' => 1, 'unit_price' => 16000],
            ['dish_id' => 16, 'quantity' => 1, 'unit_price' => 10000],
        ]);

        tenancy()->end();

        $this->command->info('Tenant creado: la-piazza | admin@lapiazza.com / password');
    }
}
