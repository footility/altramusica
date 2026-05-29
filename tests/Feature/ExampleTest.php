<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La home reindirizza alla dashboard admin; un ospite finisce sul login.
     */
    public function test_the_application_redirects_guests_to_login(): void
    {
        $this->get('/')->assertRedirect(route('admin.dashboard'));

        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }
}
