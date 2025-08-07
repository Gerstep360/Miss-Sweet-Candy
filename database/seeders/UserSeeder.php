<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin Café',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('administrador');
        // Cajero
        $cajero = User::create([
            'name' => 'María González',
            'email' => 'cajero@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
        $cajero->assignRole('cajero');

        // Barista
        $barista = User::create([
            'name' => 'Carlos López',
            'email' => 'barista@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
        $barista->assignRole('barista');

        //cliente genérico
        $clienteGenerico = User::create([
            'name' => 'Cliente',
            'email' => 'cliente.generico@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
        $clienteGenerico->assignRole('cliente');
        // Cliente 1
        $cliente1 = User::create([
            'name' => 'Juan Pérez',
            'email' => 'cliente@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
        $cliente1->assignRole('cliente');

        // Cliente 2
        $cliente2 = User::create([
            'name' => 'Ana Silva',
            'email' => 'ana@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
        $cliente2->assignRole('cliente');
    }
}
