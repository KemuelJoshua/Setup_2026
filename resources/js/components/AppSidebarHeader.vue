<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Brush,
    LogOut,
    Moon,
    Shield,
    ShoppingCart,
    Sparkles,
    Sun,
    UserCircle2,
} from '@lucide/vue';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { SidebarTrigger } from '@/components/ui/sidebar';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useAppearance } from '@/composables/useAppearance';
import { getInitials } from '@/composables/useInitials';
import { logout } from '@/routes';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
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

const cartItemCount = 11;

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

const userSheetItems = [
    {
        title: 'Profile',
        href: editProfile(),
        icon: UserCircle2,
    },
    {
        title: 'Security',
        href: editSecurity(),
        icon: Shield,
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: Brush,
    },
];

const handleLogout = (): void => {
    router.flushAll();
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1 md:hidden" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="ml-auto flex items-center gap-1 sm:gap-2">
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

            <TooltipProvider :delay-duration="0">
                <Tooltip>
                    <TooltipTrigger as-child>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="group relative h-9 w-9 cursor-pointer rounded-full"
                            aria-label="Shopping cart"
                        >
                            <ShoppingCart
                                class="size-5 opacity-80 transition-opacity group-hover:opacity-100"
                            />
                            <span
                                class="absolute top-0 right-0 flex min-w-4 translate-x-0.5 -translate-y-0.5 items-center justify-center rounded-full bg-black px-1 text-[10px] leading-none font-semibold text-white dark:bg-white dark:text-black"
                            >
                                {{ cartItemCount }}
                            </span>
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>Shopping cart</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>

            <TooltipProvider :delay-duration="0">
                <Tooltip>
                    <TooltipTrigger as-child>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="group relative h-9 w-9 cursor-pointer rounded-full"
                            aria-label="Notifications"
                        >
                            <Bell
                                class="size-5 opacity-80 transition-opacity group-hover:opacity-100"
                            />
                            <span
                                class="absolute top-1 right-1 size-2 rounded-full bg-red-500"
                            ></span>
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>Notifications</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>

            <Sheet>
                <SheetTrigger :as-child="true">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="relative size-10 w-auto rounded-full p-1 focus-visible:ring-2 focus-visible:ring-primary"
                    >
                        <Avatar class="size-8 overflow-hidden rounded-full">
                            <AvatarImage
                                v-if="auth.user.avatar"
                                :src="auth.user.avatar"
                                :alt="auth.user.name"
                            />
                            <AvatarFallback
                                class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                            >
                                {{ getInitials(auth.user?.name) }}
                            </AvatarFallback>
                        </Avatar>
                    </Button>
                </SheetTrigger>

                <SheetContent
                    side="right"
                    class="w-full gap-0 border-l border-neutral-200 bg-white p-0 shadow-sm sm:max-w-md"
                >
                    <SheetTitle class="sr-only">Account menu</SheetTitle>

                    <div class="flex min-h-full flex-col bg-white">
                        <div
                            class="flex flex-col items-center border-b border-neutral-200 px-6 pt-14 pb-8 text-center"
                        >
                            <Avatar
                                class="mb-4 size-16 overflow-hidden rounded-full"
                            >
                                <AvatarImage
                                    v-if="auth.user.avatar"
                                    :src="auth.user.avatar"
                                    :alt="auth.user.name"
                                />
                                <AvatarFallback
                                    class="bg-neutral-100 text-base font-semibold text-neutral-950"
                                >
                                    {{ getInitials(auth.user?.name) }}
                                </AvatarFallback>
                            </Avatar>

                            <h2 class="text-xl font-semibold text-neutral-950">
                                {{ auth.user.name }}
                            </h2>
                            <p class="mt-2 text-sm text-neutral-500">
                                {{ auth.user.email }}
                            </p>
                        </div>

                        <nav class="flex flex-col gap-2 px-5 py-6">
                            <SheetClose
                                v-for="item in userSheetItems"
                                :key="item.title"
                                as-child
                            >
                                <Link
                                    :href="item.href"
                                    prefetch
                                    class="flex min-h-9 items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-neutral-950 transition-colors hover:bg-neutral-50"
                                >
                                    <component
                                        :is="item.icon"
                                        class="size-5 text-neutral-400"
                                    />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SheetClose>
                        </nav>

                        <div
                            class="mt-auto border-t border-neutral-200 px-5 py-8"
                        >
                            <div
                                class="mx-auto mb-4 flex size-14 items-center justify-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-700"
                            >
                                <Sparkles class="size-6" />
                            </div>
                            <div
                                class="rounded-lg border border-neutral-200 bg-white p-5 text-center"
                            >
                                <h3
                                    class="text-base font-semibold text-neutral-950"
                                >
                                    Need help setting things up?
                                </h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-neutral-500"
                                >
                                    I&apos;m available to help you update your
                                    profile, security, and appearance anytime.
                                </p>

                                <SheetClose as-child>
                                    <Link
                                        :href="logout()"
                                        method="post"
                                        as="button"
                                        class="mt-5 inline-flex h-9 items-center justify-center rounded-md bg-neutral-950 px-3 text-sm font-medium text-white transition-colors hover:bg-neutral-800"
                                        data-test="logout-button"
                                        @click="handleLogout"
                                    >
                                        <LogOut class="mr-2 size-4" />
                                        Log Out
                                    </Link>
                                </SheetClose>
                            </div>
                        </div>
                    </div>
                </SheetContent>
            </Sheet>
        </div>
    </header>
</template>
