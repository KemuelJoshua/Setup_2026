<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'tenant' => tenancy()->initialized ? [
                'id' => tenant('id'),
                'name' => tenant('school_name'),
            ] : null,
            'auth' => [
                'user' => $request->user(),
            ],
            'notifications' => fn (): array => $this->notifications($request),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Get the authenticated user's notification summary.
     *
     * @return array{items: array<int, array{id: string, title: string, message: string, createdAt: string, isUnread: bool}>, unreadCount: int}
     */
    private function notifications(Request $request): array
    {
        $user = $request->user();

        if ($user === null) {
            return ['items' => [], 'unreadCount' => 0];
        }

        $items = $user->notifications()
            ->latest()
            ->limit(5)
            ->get(['id', 'type', 'data', 'read_at', 'created_at'])
            ->map(fn (DatabaseNotification $notification): array => [
                'id' => $notification->id,
                'title' => (string) ($notification->data['title'] ?? $this->notificationTitle($notification)),
                'message' => (string) ($notification->data['message'] ?? $notification->data['body'] ?? ''),
                'createdAt' => $notification->created_at->toIso8601String(),
                'isUnread' => $notification->unread(),
            ])
            ->all();

        return [
            'items' => $items,
            'unreadCount' => $user->unreadNotifications()->count(),
        ];
    }

    private function notificationTitle(DatabaseNotification $notification): string
    {
        return Str::of(class_basename($notification->type))
            ->beforeLast('Notification')
            ->headline()
            ->toString();
    }
}
