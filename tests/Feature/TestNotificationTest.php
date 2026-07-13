<?php

use App\Models\User;
use App\Notifications\TestNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('it stores a test notification for the recipient', function () {
    $user = User::factory()->create();

    $user->notifyNow(new TestNotification);

    $notification = $user->notifications()->sole();

    $this->assertModelExists($notification);

    expect($notification)
        ->type->toBe(TestNotification::class)
        ->data->toBe([
            'title' => 'Test notification',
            'message' => 'Your notification bell is connected and working.',
        ])
        ->read_at->toBeNull();
});
