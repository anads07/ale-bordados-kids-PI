<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $senha = config('admin.password');

        // Exige credenciais antes de criar a conta administrativa.
        if (! is_string($email) || trim($email) === '' || ! is_string($senha) || $senha === '') {
            throw new RuntimeException('Defina ADMIN_EMAIL e ADMIN_PASSWORD antes de executar o seeder administrativo.');
        }

        DB::table('usuarios')->updateOrInsert(
            ['email' => $email],
            [
                'nome_usuario' => 'ADM',
                'sobrenome_usuario' => 'Sistema',
                'senha' => Hash::make($senha),
                'is_admin' => 1,
            ]
        );
    }
}
