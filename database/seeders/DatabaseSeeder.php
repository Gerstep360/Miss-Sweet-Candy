<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RolePermissionSeeder::class,
        ]);
        $this->call([
            UserSeeder::class,
        ]);
        $this->call(HorarioSeeder::class);

        $this->call(CategoriaSeeder::class);

        $this->call(ProductoSeeder::class);
        $this->call(MesaSeeder::class);
        $this->call(PedidoMesaSeeder::class);
        $this->call(PedidoMesaItemSeeder::class);
        $this->call(PedidoMostradorSeeder::class);

        this->call(CobroCajaSeeder::class);
    }
}
