# Notifications Guide

## 1. Create Notification

```bash
php artisan make:notification CoursePublishedNotification
```

## 2. Use This Template

```php
<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoursePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Course $course
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Course Published',
            'message' => "{$this->course->title} is now available.",
            'course_id' => $this->course->id,
            'url' => route('courses.show', $this->course),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
```

## 3. Add Email If Needed

Change:

```php
public function via(object $notifiable): array
{
    return ['database'];
}
```

To:

```php
public function via(object $notifiable): array
{
    return ['database', 'mail'];
}
```

Add:

```php
public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('Course Published')
        ->greeting("Hello {$notifiable->name},")
        ->line("{$this->course->title} has been published.")
        ->action('View Course', route('courses.show', $this->course));
}
```

## 4. Send To One User

```php
use App\Notifications\CoursePublishedNotification;

$user->notify(new CoursePublishedNotification($course));
```

## 5. Send To Many Users

```php
use App\Models\User;
use App\Notifications\CoursePublishedNotification;

User::role('Teacher')
    ->get()
    ->each(function (User $user) use ($course): void {
        $user->notify(new CoursePublishedNotification($course));
    });
```

## 6. Send Immediately

```php
$user->notifyNow(new CoursePublishedNotification($course));
```

## 7. Required Dropdown Data

```php
return [
    'title' => 'Notification title',
    'message' => 'Notification message.',
];
```

## 8. Existing Working Example

```text
app/Notifications/RoleCreatedNotification.php
app/Actions/Roles/CreateRole.php
```

## 9. Test Delivery

```php
use App\Notifications\CoursePublishedNotification;
use Illuminate\Support\Facades\Notification;

Notification::fake();

// Run feature.

Notification::assertSentTo($user, CoursePublishedNotification::class);
```

## 10. Test Database Notification

```php
use App\Models\User;
use App\Notifications\CoursePublishedNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('it stores a course published notification', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['title' => 'Laravel Basics']);

    $user->notifyNow(new CoursePublishedNotification($course));

    $notification = $user->notifications()->sole();

    expect($notification)
        ->type->toBe(CoursePublishedNotification::class)
        ->data->toMatchArray([
            'title' => 'Course Published',
            'message' => 'Laravel Basics is now available.',
            'course_id' => $course->id,
        ])
        ->read_at->toBeNull();
});
```

## 11. Run Tests

```bash
php artisan test --compact tests/Feature/TestNotificationTest.php
php artisan test --compact tests/Feature/NotificationDropdownTest.php
```
