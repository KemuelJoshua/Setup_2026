<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Moon, Search, Sun } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import MessageDropdown from '@/components/MessageDropdown.vue';
import NotificationDropdown from '@/components/NotificationDropdown.vue';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import UserAccountSheet from '@/components/UserAccountSheet.vue';
import { useAppearance } from '@/composables/useAppearance';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const auth = computed(() => page.props.auth);
const { resolvedAppearance, updateAppearance } = useAppearance();

const nextAppearance = computed(() =>
    resolvedAppearance.value === 'dark' ? 'light' : 'dark',
);

const themeIcon = computed(() =>
    resolvedAppearance.value === 'dark' ? Sun : Moon,
);

const themeLabel = computed(() =>
    resolvedAppearance.value === 'dark'
        ? 'Switch to light mode'
        : 'Switch to dark mode',
);
</script>

<template>
    <header
        data-test="app-navbar"
        class="sticky top-0 z-30 flex h-16 shrink-0 items-center border-b-2 border-primary/80 bg-sidebar text-sidebar-foreground shadow-sm shadow-black/5"
    >
        <div
            class="flex h-full min-w-0 items-center px-4 md:w-64"
        >
            <Link
                :href="dashboard()"
                class="min-w-0 flex-1"
                aria-label="Dashboard"
            >
                <AppLogo />
            </Link>
        </div>

        <div class="flex min-w-0 flex-1 items-center gap-3 px-3 sm:px-4">
            <SidebarTrigger class="shrink-0" />

            <div class="hidden min-w-0 flex-1 items-center gap-3 md:flex">
                <div class="hidden min-w-0 lg:block">
                    <template v-if="breadcrumbs && breadcrumbs.length > 0">
                        <Breadcrumbs :breadcrumbs="breadcrumbs" />
                    </template>
                </div>

                <label class="relative ml-auto block w-full max-w-md">
                    <span class="sr-only">Search the application</span>
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        :stroke-width="1.8"
                    />
                    <input
                        type="search"
                        placeholder="Search items, transactions, documents..."
                        class="h-9 w-full rounded-md border border-input bg-background pr-3 pl-9 text-sm text-foreground shadow-xs outline-none placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/15"
                    />
                </label>
            </div>

            <div class="ml-auto flex shrink-0 items-center gap-1 sm:gap-2">
                <TooltipProvider :delay-duration="0">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="group h-9 w-9 cursor-pointer rounded-full"
                                :aria-label="themeLabel"
                                @click="updateAppearance(nextAppearance)"
                            >
                                <component
                                    :is="themeIcon"
                                    class="size-5 opacity-80 transition-opacity group-hover:opacity-100"
                                />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{{ themeLabel }}</p>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>

                <MessageDropdown />

                <NotificationDropdown />

                <UserAccountSheet :user="auth.user" show-details />
            </div>
        </div>
    </header>
</template>
