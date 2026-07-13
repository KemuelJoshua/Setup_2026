<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Spatie\Permission\Models\Role;

class RoleCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Role $role
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Role Created')
            ->greeting("Hello {$notifiable->name},")
            ->line("A new role named \"{$this->role->name}\" has been created.")
            ->line("Guard: {$this->role->guard_name}")
            ->action(
                'View Roles',
                route('administration.roles.index')
            )
            ->line('No action is required if this change was expected.');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Role Created',
            'message' => "The role \"{$this->role->name}\" was created.",
            'role_id' => $this->role->id,
            'role_name' => $this->role->name,
            'guard_name' => $this->role->guard_name,
            'url' => route('administration.roles.index'),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}