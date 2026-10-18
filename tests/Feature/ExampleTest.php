<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_handles_an_empty_database(): void
    {
        $this->get('/')->assertOk()->assertSee('Belum ada universitas yang ditambahkan.');
    }
}
