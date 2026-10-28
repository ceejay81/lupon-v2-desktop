<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_remember_me_stores_cookie_and_authenticates(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@bula.gov.ph',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@bula.gov.ph',
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $response->assertCookie('remembered_email', 'admin@bula.gov.ph');
    }

    public function test_login_without_remember_me_forgets_remembered_email_cookie(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@bula.gov.ph',
            'password' => bcrypt('password'),
        ]);

        $response = $this->withCookie('remembered_email', 'admin@bula.gov.ph')
            ->post('/login', [
                'email' => 'admin@bula.gov.ph',
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $response->assertCookieExpired('remembered_email');
    }

    public function test_login_page_prefills_remembered_email(): void
    {
        $response = $this->withCookie('remembered_email', 'admin@bula.gov.ph')
            ->get('/login');

        $response->assertOk();
        $response->assertSee('value="admin@bula.gov.ph"', false);
        $response->assertSee('checked', false);
    }
}
