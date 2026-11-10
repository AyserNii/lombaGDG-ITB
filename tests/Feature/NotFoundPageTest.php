<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    public function test_unknown_page_returns_the_animated_not_found_page(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada');

        $response->assertNotFound();
        $response->assertSee('Halaman tidak ditemukan');
        $response->assertSee('https://lottie.host/embed/74e9ba8a-d3a7-4d9a-a333-37ffc31c26c8/fPWcaZABGL.lottie');
    }
}
