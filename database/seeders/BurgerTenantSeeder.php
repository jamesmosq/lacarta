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

class BurgerTenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'id'            => 'brutus-burgers',
            'name'          => 'Brutus Burgers',
            'email'         => 'admin@brutusburgers.com',
            'slug'          => 'brutus-burgers',
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        tenancy()->initialize($tenant);

        TenantUser::create([
            'name'     => 'Administrador',
            'email'    => 'admin@brutusburgers.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        $burgers   = Category::create(['name' => 'Hamburguesas', 'order' => 1, 'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&q=80']);
        $combos    = Category::create(['name' => 'Combos', 'order' => 2, 'image' => 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=400&q=80']);
        $sides     = Category::create(['name' => 'Acompanantes', 'order' => 3, 'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=400&q=80']);
        $bebidas   = Category::create(['name' => 'Bebidas', 'order' => 4, 'image' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&q=80']);

        // Hamburguesas
        Dish::create(['category_id' => $burgers->id, 'name' => 'Brutus Classic', 'description' => 'Carne de res 180g, queso americano, lechuga, tomate, cebolla y salsa especial Brutus.', 'price' => 28000, 'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&q=80']);
        Dish::create(['category_id' => $burgers->id, 'name' => 'Double Smash', 'description' => 'Doble carne aplastada al estilo smash, doble queso cheddar y pepinillos encurtidos.', 'price' => 36000, 'image' => 'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=400&q=80']);
        Dish::create(['category_id' => $burgers->id, 'name' => 'BBQ Bacon', 'description' => 'Carne 200g, tocino crocante, cebolla caramelizada, queso suizo y salsa BBQ artesanal.', 'price' => 38000, 'image' => 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=400&q=80']);
        Dish::create(['category_id' => $burgers->id, 'name' => 'Mushroom Swiss', 'description' => 'Carne 180g, champinones salteados, queso suizo derretido y mayo de trufa.', 'price' => 36000, 'image' => 'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=400&q=80']);
        Dish::create(['category_id' => $burgers->id, 'name' => 'Crispy Chicken', 'description' => 'Pechuga de pollo empanada y frita, coleslaw, pepinillos y salsa picante.', 'price' => 30000, 'image' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=400&q=80']);
        Dish::create(['category_id' => $burgers->id, 'name' => 'Veggie Brutus', 'description' => 'Hamburguesa de lentejas y betabel, queso de almendras, aguacate y tomate.', 'price' => 32000, 'image' => 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=400&q=80']);

        // Combos
        Dish::create(['category_id' => $combos->id, 'name' => 'Combo Classic', 'description' => 'Brutus Classic + papas fritas medianas + gaseosa 400ml.', 'price' => 38000, 'image' => 'https://images.unsplash.com/photo-1561758033-d89a9ad46330?w=400&q=80']);
        Dish::create(['category_id' => $combos->id, 'name' => 'Combo Double Smash', 'description' => 'Double Smash + papas fritas grandes + gaseosa 400ml.', 'price' => 48000, 'image' => 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=400&q=80']);
        Dish::create(['category_id' => $combos->id, 'name' => 'Combo BBQ Bacon', 'description' => 'BBQ Bacon + papas fritas grandes + limonada natural.', 'price' => 52000, 'image' => 'https://images.unsplash.com/photo-1555992336-03a23c7b20ee?w=400&q=80']);

        // Acompanantes
        Dish::create(['category_id' => $sides->id, 'name' => 'Papas fritas clasicas', 'description' => 'Papas cortadas a mano, fritas y sazonadas con sal gruesa. Porcion grande.', 'price' => 12000, 'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=400&q=80']);
        Dish::create(['category_id' => $sides->id, 'name' => 'Aros de cebolla', 'description' => 'Cebolla dulce empanada en cerveza y frita. Con salsa ranch.', 'price' => 14000, 'image' => 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=400&q=80']);
        Dish::create(['category_id' => $sides->id, 'name' => 'Mac and Cheese', 'description' => 'Pasta corta en salsa de queso cheddar y mozzarella gratinada.', 'price' => 16000, 'image' => 'https://images.unsplash.com/photo-1543339318-b43dc53e19b6?w=400&q=80']);
        Dish::create(['category_id' => $sides->id, 'name' => 'Alitas BBQ', 'description' => 'Alitas de pollo al horno bañadas en salsa BBQ artesanal. 6 unidades.', 'price' => 22000, 'image' => 'https://images.unsplash.com/photo-1608039858788-d45e44bd8e1b?w=400&q=80']);

        // Bebidas
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Limonada natural', 'description' => 'Limonada recien exprimida con azucar o stevia. 500ml.', 'price' => 8000, 'image' => 'https://images.unsplash.com/photo-1534353341-8f9caab7b3ce?w=400&q=80']);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Milkshake de chocolate', 'description' => 'Malteada espesa de helado con chocolate belga. 400ml.', 'price' => 18000, 'image' => 'https://images.unsplash.com/photo-1572490122747-3a4b57a4db3a?w=400&q=80']);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Cerveza artesanal', 'description' => 'Cerveza artesanal local de temporada. 330ml.', 'price' => 14000, 'image' => 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=400&q=80']);
        Dish::create(['category_id' => $bebidas->id, 'name' => 'Gaseosa', 'description' => 'Coca-Cola, Sprite o Manzana. 400ml.', 'price' => 6000, 'image' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=400&q=80']);

        // Mesas
        foreach (range(1, 8) as $i) {
            RestaurantTable::create(['name' => "Mesa $i"]);
        }
        RestaurantTable::create(['name' => 'Counter 1']);
        RestaurantTable::create(['name' => 'Counter 2']);
        RestaurantTable::create(['name' => 'Para llevar']);

        // Pedidos
        $o1 = Order::create(['table_id' => 1, 'status' => 'preparing', 'customer_name' => 'Juan C.', 'total' => 66000, 'notes' => 'Sin pepinillos en el Double Smash']);
        $o1->items()->createMany([
            ['dish_id' => 2, 'quantity' => 1, 'unit_price' => 36000],
            ['dish_id' => 10, 'quantity' => 1, 'unit_price' => 12000],
            ['dish_id' => 14, 'quantity' => 1, 'unit_price' => 8000],
            ['dish_id' => 16, 'quantity' => 1, 'unit_price' => 14000],
        ]);

        $o2 = Order::create(['table_id' => 4, 'status' => 'pending', 'customer_name' => 'Grupo mesa 4', 'total' => 156000]);
        $o2->items()->createMany([
            ['dish_id' => 7, 'quantity' => 2, 'unit_price' => 38000],
            ['dish_id' => 9, 'quantity' => 1, 'unit_price' => 52000],
            ['dish_id' => 13, 'quantity' => 2, 'unit_price' => 22000],
        ]);

        $o3 = Order::create(['table_id' => 11, 'status' => 'ready', 'customer_name' => 'Para llevar - Carlos', 'total' => 52000]);
        $o3->items()->createMany([
            ['dish_id' => 3, 'quantity' => 1, 'unit_price' => 38000],
            ['dish_id' => 11, 'quantity' => 1, 'unit_price' => 14000],
        ]);

        tenancy()->end();

        $this->command->info('Tenant creado: brutus-burgers | admin@brutusburgers.com / password');
    }
}
