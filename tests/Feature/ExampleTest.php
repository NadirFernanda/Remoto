<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * A homepage consulta a BD (PlatformStatsService) — sem RefreshDatabase
     * este teste falha com "no such table: users" sempre que corre antes de
     * qualquer outro teste que já tenha migrado a BD SQLite em memória.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
