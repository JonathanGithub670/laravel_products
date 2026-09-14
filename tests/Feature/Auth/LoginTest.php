<?php

use App\Models\User;

it('redirects to the dashboard when login succeeds', function (): void {
    $user = User::factory()->create(['password' => 'password']);

    $response = $this->post(route('login.store'), [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('shows an error message when the credentials are wrong', function (): void {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

it('redirects guests to the login page', function (): void {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

it('logs the user out and redirects to the login page', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});
