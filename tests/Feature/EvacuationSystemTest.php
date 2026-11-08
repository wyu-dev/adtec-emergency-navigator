<?php

namespace Tests\Feature;

use App\Models\AssemblyPoint;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvacuationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_client_navigator_screen_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Assembly Navigator');
        $response->assertSee('ADTEC JTM KUANTAN');
    }

    public function test_emergency_status_api(): void
    {
        $response = $this->getJson('/api/emergency/status');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'is_emergency',
            'alert',
            'assembly_point' => ['name', 'latitude', 'longitude'],
            'timestamp',
        ]);
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Kod PIN Pantas');
        $response->assertSee('Panel Kawalan Kecemasan');
    }

    public function test_admin_can_login_with_pin(): void
    {
        $response = $this->post('/login/pin', [
            'pin' => '9999',
        ]);
        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    public function test_admin_can_login_with_email_and_password(): void
    {
        $response = $this->post('/login/email', [
            'email' => 'admin@adtec.edu.my',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Pusat Kawalan Amaran Kecemasan');
        $response->assertSee('Padang ADTEC Kuantan');
    }
}
