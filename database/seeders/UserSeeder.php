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
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Café',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('administrador');

        // Cajero
        $cajero = User::firstOrCreate(
            ['email' => 'cajero@gmail.com'],
            [
                'name' => 'María González',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $cajero->assignRole('cajero');

        // Barista
        $barista = User::firstOrCreate(
            ['email' => 'barista@gmail.com'],
            [
                'name' => 'Carlos López',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $barista->assignRole('barista');

        // Cliente genérico
        $clienteGenerico = User::firstOrCreate(
            ['email' => 'cliente.generico@gmail.com'],
            [
                'name' => 'Cliente',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $clienteGenerico->assignRole('cliente');

        // Cliente 1
        $cliente1 = User::firstOrCreate(
            ['email' => 'cliente@gmail.com'],
            [
                'name' => 'Juan Pérez',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $cliente1->assignRole('cliente');

        // Cliente 2
        $cliente2 = User::firstOrCreate(
            ['email' => 'ana@gmail.com'],
            [
                'name' => 'Ana Silva',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $cliente2->assignRole('cliente');
    }
}