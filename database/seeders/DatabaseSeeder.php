<?php

namespace Database\Seeders;

use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\User\ProdutoSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ProdutoSeeder::class,
        ]);
    }
}
