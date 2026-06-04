<?php

namespace Tests\Feature;

use Tests\TestCase;

class PraEventIntegrationTest extends TestCase
{
    public function test_landing_page_shows_pra_2026_menu_links(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PRA 2026');
        $response->assertSee(route('events.pra-2026'), false);
    }

    public function test_pra_2026_landing_page_is_reachable(): void
    {
        $response = $this->get(route('events.pra-2026'));

        $response->assertOk();
        $response->assertSee('PEKAN ROHANI ANAK 2026');
    }
}
