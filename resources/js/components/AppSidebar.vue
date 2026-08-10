<script setup lang="ts">
import { BookOpenCheck, User2 } from '@lucide/vue';
import type { NavGroup } from '@/components/NavMain.vue';
import NavMain from '@/components/NavMain.vue';
import { Sidebar, SidebarContent, SidebarRail } from '@/components/ui/sidebar';
import { dashboard } from '@/routes/admin';
import { index as userIndex } from '@/routes/admin/users';
import type { NavItem } from '@/types';

const primaryNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: BookOpenCheck,
    },
    {
        title: 'Users',
        href: userIndex(),
        icon: User2,
    },
];

const navigationGroups: NavGroup[] = [
    {
        title: 'Learning',
        items: primaryNavItems,
    },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="sidebar"
        class="app-sidebar top-16 h-[calc(100svh-4rem)] border-t-0 bg-sidebar"
    >
        <SidebarContent
            class="gap-3 px-3 py-4 group-data-[collapsible=icon]:px-1 group-data-[collapsible=icon]:py-4"
        >
            <NavMain :groups="navigationGroups" />
        </SidebarContent>

        <SidebarRail />
    </Sidebar>
    <slot />
</template>
