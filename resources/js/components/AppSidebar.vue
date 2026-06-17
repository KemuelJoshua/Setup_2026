<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    FolderGit2,
    LayoutGrid,
    Palette,
    Settings2,
    Shield,
    UserRound,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import type { NavGroup } from '@/components/NavMain.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
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
        icon: LayoutGrid,
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
        title: 'Workspace',
        items: primaryNavItems,
    },
    {
        title: 'Preferences',
        items: settingsNavItems,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar" class="bg-transparent p-3">
        <SidebarHeader class="gap-3 p-0">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="h-auto rounded-xl border border-sidebar-border/70 px-3 py-3 text-sidebar-primary shadow-none group-data-[collapsible=icon]:size-12! group-data-[collapsible=icon]:p-0! hover:bg-sidebar-accent hover:text-sidebar-primary"
                    >
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="gap-0">
            <NavMain :groups="navigationGroups" />
        </SidebarContent>

        <SidebarFooter class="gap-3 p-0">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
