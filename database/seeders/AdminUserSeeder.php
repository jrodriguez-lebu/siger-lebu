<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@siger.lebu.cl'],
            [
                'name'     => 'Administrador SIGER',
                'password' => Hash::make('Admin@1234'),
                'phone'    => '+56912345678',
                'role'     => 'admin',
                'active'   => true,
            ]
        );
        $admin->assignRole('admin');

        // Coordinador de prueba
        $coordinador = User::firstOrCreate(
            ['email' => 'coordinador@siger.lebu.cl'],
            [
                'name'     => 'Juan Coordinador',
                'password' => Hash::make('Coord@1234'),
                'phone'    => '+56987654321',
                'role'     => 'coordinador',
                'active'   => true,
            ]
        );
        $coordinador->assignRole('coordinador');

        // Líder de prueba
        $lider = User::firstOrCreate(
            ['email' => 'lider@siger.lebu.cl'],
            [
                'name'     => 'Pedro Líder',
                'password' => Hash::make('Lider@1234'),
                'phone'    => '+56911223344',
                'role'     => 'lider',
                'active'   => true,
            ]
        );
        $lider->assignRole('lider');

        // Digitador de prueba
        $digitador = User::firstOrCreate(
            ['email' => 'digitador@siger.lebu.cl'],
            [
                'name'     => 'María Digitadora',
                'password' => Hash::make('Digit@1234'),
                'phone'    => '+56944556677',
                'role'     => 'digitador',
                'active'   => true,
            ]
        );
        $digitador->assignRole('digitador');

        $this->command->info('✅ Usuarios iniciales creados:');
        $this->command->table(
            ['Nombre', 'Email', 'Rol', 'Contraseña'],
            [
                ['Administrador SIGER', 'admin@siger.lebu.cl', 'admin', 'Admin@1234'],
                ['Juan Coordinador', 'coordinador@siger.lebu.cl', 'coordinador', 'Coord@1234'],
                ['Pedro Líder', 'lider@siger.lebu.cl', 'lider', 'Lider@1234'],
                ['María Digitadora', 'digitador@siger.lebu.cl', 'digitador', 'Digit@1234'],
            ]
        );
    }
}
