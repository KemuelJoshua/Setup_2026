<?php

use Inertia\Testing\AssertableInertia as Assert;

test('welcome page renders the PEMS entry experience', function () {
    $response = $this->get(route('home'));

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('name')
        ->where('auth.user', null)
    );
});
