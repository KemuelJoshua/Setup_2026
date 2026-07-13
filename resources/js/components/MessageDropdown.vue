<script setup lang="ts">
import { MessageCircle } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type DummyMessage = {
    id: number;
    sender: string;
    initials: string;
    preview: string;
    time: string;
    isUnread: boolean;
};

const messages: DummyMessage[] = [
    {
        id: 1,
        sender: 'Alyssa Santos',
        initials: 'AS',
        preview: 'Hi! Are the course materials available now?',
        time: '2m ago',
        isUnread: true,
    },
    {
        id: 2,
        sender: 'Marco Reyes',
        initials: 'MR',
        preview: 'Thank you for reviewing my assignment.',
        time: '1h ago',
        isUnread: true,
    },
    {
        id: 3,
        sender: 'Jamie Cruz',
        initials: 'JC',
        preview: 'See you in tomorrow’s session!',
        time: 'Yesterday',
        isUnread: false,
    },
];

const unreadCount = messages.filter((message) => message.isUnread).length;
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger :as-child="true">
            <Button
                variant="ghost"
                size="icon"
                class="group relative h-9 w-9 cursor-pointer rounded-full"
                aria-label="Messages"
                data-test="message-trigger"
            >
                <MessageCircle
                    class="size-5 opacity-80 transition-opacity group-hover:opacity-100"
                />
                <span
                    v-if="unreadCount > 0"
                    class="absolute top-1 right-1 size-2 rounded-full bg-red-500 ring-2 ring-background"
                ></span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            align="end"
            :side-offset="12"
            class="w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-lg border border-sidebar-border bg-sidebar p-0 text-sidebar-foreground shadow-lg shadow-black/10"
            data-test="message-panel"
        >
            <div
                class="flex items-center justify-between gap-4 border-b border-sidebar-border/70 px-4 py-3.5"
            >
                <div>
                    <h3 class="text-sm font-semibold text-sidebar-foreground">
                        Messages
                    </h3>
                    <p class="mt-0.5 text-xs text-sidebar-foreground/50">
                        Your latest conversations
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
                    v-for="message in messages"
                    :key="message.id"
                    type="button"
                    class="group flex w-full items-start gap-3 rounded-md px-3 py-3 text-left transition-[background-color,color] duration-200 ease-out hover:bg-sidebar-accent/70 focus-visible:bg-sidebar-accent/70 focus-visible:ring-2 focus-visible:ring-sidebar-ring focus-visible:outline-none"
                    :class="message.isUnread ? 'bg-sidebar-accent/80' : ''"
                >
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold transition-transform duration-200 ease-out group-hover:scale-105"
                        :class="
                            message.isUnread
                                ? 'bg-sidebar-primary text-sidebar-primary-foreground shadow-sm shadow-sidebar-primary/20'
                                : 'bg-sidebar-accent text-sidebar-foreground/70'
                        "
                    >
                        {{ message.initials }}
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="flex items-start justify-between gap-4">
                            <span
                                class="truncate text-[13px] font-semibold text-sidebar-foreground"
                            >
                                {{ message.sender }}
                            </span>
                            <span
                                class="shrink-0 text-[11px] text-sidebar-foreground/45"
                            >
                                {{ message.time }}
                            </span>
                        </span>
                        <span
                            class="mt-1 block truncate text-xs leading-5 text-sidebar-foreground/55"
                        >
                            {{ message.preview }}
                        </span>
                    </span>
                </button>
            </div>

            <div class="border-t border-sidebar-border/70 p-3">
                <button
                    type="button"
                    class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-md bg-sidebar-primary px-4 text-sm font-semibold text-sidebar-primary-foreground shadow-sm shadow-sidebar-primary/20 transition-[background-color,box-shadow] duration-200 hover:bg-sidebar-primary/90 focus-visible:ring-2 focus-visible:ring-sidebar-ring focus-visible:ring-offset-2 focus-visible:ring-offset-sidebar focus-visible:outline-none"
                >
                    <MessageCircle class="size-4" :stroke-width="1.8" />
                    See All Messages
                </button>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
