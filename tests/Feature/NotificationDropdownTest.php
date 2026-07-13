<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('notification dropdown contains only the authenticated users latest notifications', function () {
    Carbon::setTestNow('2026-07-13 12:00:00');

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    foreach (range(1, 6) as $number) {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\CoursePublishedNotification',
            'data' => [
                'title' => "Course update {$number}",
                'message' => "Message {$number}",
            ],
            'read_at' => $number === 1 ? now() : null,
            'created_at' => now()->subMinutes(6 - $number),
            'updated_at' => now()->subMinutes(6 - $number),
        ]);
    }

    $otherUser->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'App\\Notifications\\PrivateNotification',
        'data' => ['title' => 'Not yours', 'message' => 'Private'],
        'created_at' => now()->addMinute(),
        'updated_at' => now()->addMinute(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('notifications.unreadCount', 5)
            ->has('notifications.items', 5)
            ->where('notifications.items.0.title', 'Course update 6')
            ->where('notifications.items.0.message', 'Message 6')
            ->where('notifications.items.0.isUnread', true)
            ->where('notifications.items.4.title', 'Course update 2')
            ->missing('notifications.items.5')
        );
});

test('guests do not receive notification data', function () {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('notifications.items', [])
            ->where('notifications.unreadCount', 0)
        );
});
