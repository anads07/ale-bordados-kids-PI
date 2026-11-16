<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica se a vitrine principal responde com sucesso usando o schema real.
 */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Garante que a página inicial seja renderizada com o banco migrado.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
