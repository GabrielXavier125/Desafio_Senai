<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin padrão
        User::firstOrCreate(
            ['email' => 'admin@senai.br'],
            [
                'name'     => 'Administrador SENAI',
                'password' => Hash::make('senai@2026'),
                'role'     => 'admin',
                'active'   => true,
            ]
        );

        // Professor de exemplo
        User::firstOrCreate(
            ['email' => 'professor@senai.br'],
            [
                'name'     => 'Professor Exemplo',
                'password' => Hash::make('senai@2026'),
                'role'     => 'professor',
                'active'   => true,
            ]
        );
    }
}
