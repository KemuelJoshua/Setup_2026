<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Bell, Check } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

const page = usePage();

const notifications = computed(() => page.props.notifications.items);
const unreadCount = computed(() => page.props.notifications.unreadCount);

const formatNotificationTime = (createdAt: string): string => {
    const createdDate = new Date(createdAt);
    const elapsedSeconds = Math.max(
        0,
        Math.floor((Date.now() - createdDate.getTime()) / 1000),
    );

    if (elapsedSeconds < 60) {
        return 'Just now';
    }

    if (elapsedSeconds < 3600) {
        return `${Math.floor(elapsedSeconds / 60)}m ago`;
    }

    if (elapsedSeconds < 86400) {
        return `${Math.floor(elapsedSeconds / 3600)}h ago`;
    }

    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
    }).format(createdDate);
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger :as-child="true">
            <Button
                variant="ghost"
                size="icon"
                class="group relative h-9 w-9 cursor-pointer rounded-md text-sidebar-foreground/70 hover:bg-sidebar-accent/70 hover:text-sidebar-primary"
                aria-label="Notifications"
                data-test="notification-trigger"
            >
                <Bell
                    class="size-5 opacity-80 transition-opacity group-hover:opacity-100"
                />
                <span
                    v-if="unreadCount > 0"
                    class="absolute top-1 right-1 size-2 rounded-full bg-sidebar-primary ring-2 ring-background"
                ></span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            align="end"
            :side-offset="12"
            class="w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-lg border border-sidebar-border bg-sidebar p-0 text-sidebar-foreground shadow-lg shadow-black/10"
            data-test="notification-panel"
        >
            <div
                class="flex items-center justify-between gap-4 border-b border-sidebar-border/70 px-4 py-3.5"
            >
                <div>
                    <h3 class="text-sm font-semibold text-sidebar-foreground">
                        Notifications
                    </h3>
                    <p class="mt-0.5 text-xs text-sidebar-foreground/50">
                        Your latest account activity
                    </p>
                </div>
                <span
                    class="rounded-md bg-sidebar-primary px-2 py-1 text-[11px] font-semibold text-sidebar-primary-foreground shadow-sm shadow-sidebar-primary/20"
                >
                    {{ unreadCount }} new
                </span>
            </div>

            <div class="max-h-[22rem] overflow-y-auto p-2">
                <button
                    v-for="notification in notifications"
                    :key="notification.id"
                    type="button"
                    class="group flex w-full items-start gap-3 rounded-md px-3 py-3 text-left transition-[background-color,color] duration-200 ease-out hover:bg-sidebar-accent/70 focus-visible:bg-sidebar-accent/70 focus-visible:ring-2 focus-visible:ring-sidebar-ring focus-visible:outline-none"
                    :class="notification.isUnread ? 'bg-sidebar-accent/80' : ''"
                >
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-md transition-transform duration-200 ease-out group-hover:scale-105"
                        :class="
                            notification.isUnread
                                ? 'bg-sidebar-primary text-sidebar-primary-foreground shadow-sm shadow-sidebar-primary/20'
                                : 'bg-sidebar-accent text-sidebar-foreground/70'
                        "
                    >
                        <Bell class="size-[18px]" :stroke-width="1.8" />
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="flex items-start justify-between gap-4">
                            <span
                                class="truncate text-[13px] font-semibold text-sidebar-foreground"
                            >
                                {{ notification.title }}
                            </span>
                            <span
                                class="shrink-0 text-[11px] text-sidebar-foreground/45"
                            >
                                {{
                                    formatNotificationTime(
                                        notification.createdAt,
                                    )
                                }}
                            </span>
                        </span>
                        <span
                            class="mt-1 block truncate text-xs leading-5 text-sidebar-foreground/55"
                        >
                            {{ notification.message }}
                        </span>
                    </span>
                </button>

                <div
                    v-if="notifications.length === 0"
                    class="flex flex-col items-center px-6 py-10 text-center"
                >
                    <span
                        class="flex size-10 items-center justify-center rounded-md bg-sidebar-accent text-sidebar-foreground/50"
                    >
                        <Bell class="size-5" :stroke-width="1.8" />
                    </span>
                    <p
                        class="mt-3 text-sm font-semibold text-sidebar-foreground"
                    >
                        You're all caught up
                    </p>
                    <p class="mt-1 text-xs text-sidebar-foreground/50">
                        New notifications will appear here.
                    </p>
                </div>
            </div>

            <div class="border-t border-sidebar-border/70 p-3">
                <button
                    type="button"
                    class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-md bg-sidebar-primary px-4 text-sm font-semibold text-sidebar-primary-foreground shadow-sm shadow-sidebar-primary/20 transition-[background-color,box-shadow] duration-200 hover:bg-sidebar-primary/90 focus-visible:ring-2 focus-visible:ring-sidebar-ring focus-visible:ring-offset-2 focus-visible:ring-offset-sidebar focus-visible:outline-none"
                >
                    <Check class="size-4" :stroke-width="1.8" />
                    See All Notifications
                </button>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
