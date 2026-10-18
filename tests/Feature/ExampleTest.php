<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }
}
