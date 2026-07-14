<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Brush, LogOut, Shield, Sparkles, UserCircle2 } from '@lucide/vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { getInitials } from '@/composables/useInitials';
import { logout } from '@/routes';
import { edit as editAppearance } from '@/routes/admin/settings/appearance';
import { edit as editProfile } from '@/routes/admin/settings/profile';
import { edit as editSecurity } from '@/routes/admin/settings/security';
import type { User } from '@/types';

defineProps<{
    user: User;
}>();

const accountItems = [
    {
        title: 'Profile',
        description: 'Update your personal information',
        href: editProfile(),
        icon: UserCircle2,
    },
    {
        title: 'Security',
        description: 'Manage your password and sign-in',
        href: editSecurity(),
        icon: Shield,
    },
    {
        title: 'Appearance',
        description: 'Personalize your interface',
        href: editAppearance(),
        icon: Brush,
    },
];

const handleLogout = (): void => {
    router.flushAll();
};
</script>

<template>
    <Sheet>
        <SheetTrigger :as-child="true">
            <Button
                variant="ghost"
                size="icon"
                class="relative size-10 w-auto cursor-pointer rounded-md p-1 text-sidebar-foreground hover:bg-sidebar-accent/70 hover:text-sidebar-primary focus-visible:ring-sidebar-ring"
                aria-label="Open account menu"
            >
                <Avatar class="size-8 overflow-hidden rounded-full">
                    <AvatarImage
                        v-if="user.avatar"
                        :src="user.avatar"
                        :alt="user.name"
                    />
                    <AvatarFallback
                        class="rounded-full bg-sidebar-primary font-semibold text-sidebar-primary-foreground"
                    >
                        {{ getInitials(user.name) }}
                    </AvatarFallback>
                </Avatar>
            </Button>
        </SheetTrigger>

        <SheetContent
            side="right"
            class="w-full gap-0 border-sidebar-border bg-sidebar p-0 text-sidebar-foreground shadow-xl shadow-black/10 sm:max-w-md"
        >
            <SheetTitle class="sr-only">Account menu</SheetTitle>

            <div class="flex min-h-full flex-col">
                <div class="border-b border-sidebar-border/70 px-6 pt-12 pb-7">
                    <div class="flex items-center gap-4">
                        <Avatar
                            class="size-14 shrink-0 overflow-hidden rounded-md ring-1 ring-sidebar-border"
                        >
                            <AvatarImage
                                v-if="user.avatar"
                                :src="user.avatar"
                                :alt="user.name"
                            />
                            <AvatarFallback
                                class="rounded-md bg-sidebar-primary text-base font-semibold text-sidebar-primary-foreground"
                            >
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>

                        <div class="min-w-0">
                            <h2
                                class="truncate text-base font-semibold text-sidebar-foreground"
                            >
                                {{ user.name }}
                            </h2>
                            <p
                                class="mt-1 truncate text-sm text-sidebar-foreground/55"
                            >
                                {{ user.email }}
                            </p>
                        </div>
                    </div>
                </div>

                <nav class="flex flex-col gap-1.5 p-4">
                    <SheetClose
                        v-for="item in accountItems"
                        :key="item.title"
                        as-child
                    >
                        <Link
                            :href="item.href"
                            prefetch
                            class="group flex items-center gap-3 rounded-md px-3 py-3 text-sidebar-foreground transition-colors duration-200 hover:bg-sidebar-accent/70 focus-visible:ring-2 focus-visible:ring-sidebar-ring focus-visible:outline-none"
                        >
                            <span
                                class="flex size-9 shrink-0 items-center justify-center rounded-md bg-sidebar-accent text-sidebar-foreground/70 transition-colors group-hover:bg-sidebar-primary group-hover:text-sidebar-primary-foreground"
                            >
                                <component
                                    :is="item.icon"
                                    class="size-[18px]"
                                    :stroke-width="1.8"
                                />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold">
                                    {{ item.title }}
                                </span>
                                <span
                                    class="mt-0.5 block truncate text-xs text-sidebar-foreground/50"
                                >
                                    {{ item.description }}
                                </span>
                            </span>
                        </Link>
                    </SheetClose>
                </nav>

                <div class="mt-auto border-t border-sidebar-border/70 p-4">
                    <div
                        class="rounded-md border border-sidebar-border/70 bg-sidebar-accent/40 p-4"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="flex size-9 shrink-0 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground shadow-sm shadow-sidebar-primary/20"
                            >
                                <Sparkles class="size-[18px]" />
                            </span>
                            <div>
                                <h3 class="text-sm font-semibold">
                                    Need help setting things up?
                                </h3>
                                <p
                                    class="mt-1 text-xs leading-5 text-sidebar-foreground/55"
                                >
                                    Update your profile, security, and
                                    appearance preferences anytime.
                                </p>
                            </div>
                        </div>
                    </div>

                    <SheetClose as-child>
                        <Link
                            :href="logout()"
                            method="post"
                            as="button"
                            class="mt-3 inline-flex h-10 w-full cursor-pointer items-center justify-center gap-2 rounded-md border border-sidebar-border bg-sidebar px-4 text-sm font-semibold text-sidebar-foreground transition-colors hover:bg-sidebar-accent focus-visible:ring-2 focus-visible:ring-sidebar-ring focus-visible:outline-none"
                            data-test="logout-button"
                            @click="handleLogout"
                        >
                            <LogOut class="size-4" />
                            Log out
                        </Link>
                    </SheetClose>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
