<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard_brand_and_cart_shortcut_navigate_to_their_destinations(): void
    {
        $cashier = User::factory()->make();

        $response = $this->actingAs($cashier)->get(route('dashboard'));

        $response->assertSee('href="'.route('dashboard').'"', false);
        $response->assertSee('href="'.route('transaction.create').'"', false);
        $response->assertSee('aria-label="Mulai transaksi baru"', false);
    }
}
