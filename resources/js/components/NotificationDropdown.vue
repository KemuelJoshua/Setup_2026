<script setup lang="ts">
import { Bell, CalendarDays, Command, LayoutGrid, Settings } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';

const notifications = [
    {
        title: 'Settings',
        message: 'You can customize this template settings from the admin panel.',
        time: '4:36 PM',
        icon: Settings,
        iconWrapperClass: 'bg-orange-50 text-orange-500 dark:bg-orange-500/12 dark:text-orange-300',
    },
    {
        title: 'Launch Admin',
        message: 'Just see the my new admin!',
        time: '9:30 AM',
        icon: LayoutGrid,
        iconWrapperClass: 'bg-cyan-50 text-cyan-500 dark:bg-cyan-500/12 dark:text-cyan-300',
    },
    {
        title: 'Launch Admin',
        message: 'Just see the my new admin!',
        time: '9:30 AM',
        icon: Command,
        iconWrapperClass: 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300',
    },
    {
        title: 'Event Today',
        message: 'Just a reminder that you have an event today.',
        time: '9:15 AM',
        icon: CalendarDays,
        iconWrapperClass: 'bg-amber-50 text-amber-500 dark:bg-amber-500/12 dark:text-amber-300',
        highlight: true,
    },
    {
        title: 'Settings',
        message: 'You can customize this template settings from the admin panel.',
        time: '4:36 PM',
        icon: Settings,
        iconWrapperClass: 'bg-orange-50 text-orange-500 dark:bg-orange-500/12 dark:text-orange-300',
    },
];
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger :as-child="true">
            <Button
                variant="ghost"
                size="icon"
                class="group relative h-9 w-9 cursor-pointer rounded-full"
                aria-label="Notifications"
                data-test="notification-trigger"
            >
                <Bell
                    class="size-5 opacity-80 transition-opacity group-hover:opacity-100"
                />
                <span
                    class="absolute top-1 right-1 size-2 rounded-full bg-red-500"
                ></span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            align="end"
            :side-offset="12"
            class="w-[22rem] overflow-hidden rounded-2xl border border-neutral-200 bg-white p-0 shadow-[0_24px_60px_-24px_rgba(15,23,42,0.35)] dark:border-neutral-800 dark:bg-neutral-950"
            data-test="notification-panel"
        >
            <div class="flex items-center justify-between px-6 pt-6 pb-4">
                <div>
                    <h3 class="text-[1.7rem] font-semibold tracking-[-0.02em] text-neutral-950 dark:text-white">
                        Notifications
                    </h3>
                </div>
                <span class="rounded-full bg-neutral-950 px-3 py-1 text-xs font-semibold text-white dark:bg-white dark:text-neutral-950">
                    5 new
                </span>
            </div>

            <div class="max-h-[22rem] overflow-y-auto pb-3">
                <button
                    v-for="(notification, index) in notifications"
                    :key="`${notification.title}-${index}`"
                    type="button"
                    class="flex w-full items-start gap-4 px-6 py-4 text-left transition-colors hover:bg-neutral-50 focus:bg-neutral-50 focus:outline-hidden dark:hover:bg-neutral-900 dark:focus:bg-neutral-900"
                    :class="notification.highlight ? 'bg-neutral-100/90 dark:bg-neutral-900/80' : ''"
                >
                    <span
                        class="flex size-11 shrink-0 items-center justify-center rounded-full"
                        :class="notification.iconWrapperClass"
                    >
                        <component :is="notification.icon" class="size-5" />
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="flex items-start justify-between gap-4">
                            <span class="truncate text-base font-semibold text-neutral-950 dark:text-white">
                                {{ notification.title }}
                            </span>
                            <span class="shrink-0 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ notification.time }}
                            </span>
                        </span>
                        <span class="mt-1 block truncate text-sm text-neutral-500 dark:text-neutral-400">
                            {{ notification.message }}
                        </span>
                    </span>
                </button>
            </div>

            <div class="border-t border-neutral-200 px-6 py-5 dark:border-neutral-800">
                <button
                    type="button"
                    class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-neutral-950 px-4 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 focus:outline-hidden focus:ring-2 focus:ring-neutral-400 dark:bg-white dark:text-neutral-950 dark:hover:bg-neutral-200"
                >
                    See All Notifications
                </button>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
