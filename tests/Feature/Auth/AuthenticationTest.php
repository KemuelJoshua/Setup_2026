<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Features;

uses(RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('auth/Login')
        ->has('canResetPassword')
    );
});

test('login screen uses the SIMS visual assets and application color variables', function () {
    $loginPage = file_get_contents(resource_path('js/pages/auth/Login.vue'));

    expect($loginPage)
        ->toContain('/img/sims-logo.png')
        ->toContain('/img/sims-logo-dark.png')
        ->toContain('/img/DOST-TAPI.png')
        ->toContain('/img/inventory-warehouse-transparent.png')
        ->toContain('xl:grid-cols')
        ->toContain('max-w-120')
        ->toContain('backdrop-blur-xl')
        ->toContain('max-w-md')
        ->toContain('text-[clamp(2rem,2.8vw,3.15rem)]')
        ->toContain('toggleAppearance')
        ->toContain('bg-background')
        ->toContain('bg-card')
        ->toContain('bg-primary')
        ->toContain('text-muted-foreground')
        ->not->toContain('bg-red-')
        ->not->toContain('text-red-')
        ->not->toContain('bg-slate-')
        ->not->toContain('text-slate-')
        ->not->toContain('Continue with Google')
        ->not->toContain('Continue with Microsoft');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('administration.dashboard', absolute: false));
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $response->assertSessionHas('login.id', $user->id);
    $this->assertGuest();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

test('users are rate limited', function () {
    $user = User::factory()->create();

    RateLimiter::increment(md5('login'.implode('|', [$user->email, '127.0.0.1'])), amount: 5);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertTooManyRequests();
});
