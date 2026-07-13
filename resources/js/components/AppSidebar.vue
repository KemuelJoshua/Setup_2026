<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpenCheck,
    Palette,
    Settings2,
    Shield,
    UserRound,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import type { NavGroup } from '@/components/NavMain.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarRail,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const primaryNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: BookOpenCheck,
    },
];

const settingsNavItems: NavItem[] = [
    {
        title: 'Settings',
        href: editAppearance(),
        icon: Settings2,
        children: [
            {
                title: 'Profile',
                href: editProfile(),
                icon: UserRound,
            },
            {
                title: 'Security',
                href: editSecurity(),
                icon: Shield,
            },
            {
                title: 'Appearance',
                href: editAppearance(),
                icon: Palette,
            },
        ],
    },
];

const navigationGroups: NavGroup[] = [
    {
        title: 'Learning',
        items: primaryNavItems,
    },
    {
        title: 'Account',
        items: settingsNavItems,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar" class="bg-transparent">
        <SidebarHeader
            class="border-b border-sidebar-border/70 px-4 py-4 group-data-[collapsible=icon]:px-1"
        >
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="h-13 rounded-md px-2.5 text-sidebar-foreground shadow-none transition-colors duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] group-data-[collapsible=icon]:size-10! group-data-[collapsible=icon]:p-0! hover:bg-sidebar-accent/70 hover:text-sidebar-foreground"
                    >
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent
            class="gap-3 px-4 py-5 group-data-[collapsible=icon]:px-1 group-data-[collapsible=icon]:py-4"
        >
            <NavMain :groups="navigationGroups" />
        </SidebarContent>

        <SidebarRail />
    </Sidebar>
    <slot />
</template>
