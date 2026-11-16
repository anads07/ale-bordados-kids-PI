<?php

namespace Tests\Feature\Database\Seeders\Admin;

use Database\Seeders\Admin\AdminSeeder;
use RuntimeException;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    public function test_seeder_rejects_missing_admin_credentials_before_writing(): void
    {
        config([
            'admin.email' => 'admin@example.test',
            'admin.password' => '',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Defina ADMIN_EMAIL e ADMIN_PASSWORD');

        (new AdminSeeder)->run();
    }
}
