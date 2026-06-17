<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

export type NavGroup = {
    title: string;
    items: NavItem[];
};

defineProps<{
    groups: NavGroup[];
}>();

const { isCurrentUrl } = useCurrentUrl();

const menuButtonClass =
    'h-9 rounded-lg px-3 text-sm font-medium text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-primary data-[active=true]:bg-sidebar-primary data-[active=true]:text-sidebar-primary-foreground data-[active=true]:hover:bg-sidebar-primary data-[active=true]:hover:text-sidebar-primary-foreground group-data-[collapsible=icon]:size-9! group-data-[collapsible=icon]:p-0!';

const hasActiveChild = (item: NavItem): boolean =>
    item.children?.some((child) => isCurrentUrl(child.href)) ?? false;

const isItemActive = (item: NavItem): boolean =>
    isCurrentUrl(item.href) || hasActiveChild(item);
</script>

<template>
    <SidebarGroup
        v-for="group in groups"
        :key="group.title"
        class="rounded-xl shadow-none"
    >
        <SidebarGroupLabel
            class="px-3 text-[11px] font-semibold tracking-[0.14em] text-sidebar-foreground/50 uppercase"
        >
            {{ group.title }}
        </SidebarGroupLabel>
        <SidebarMenu class="gap-1">
            <SidebarMenuItem v-for="item in group.items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isItemActive(item)"
                    :tooltip="item.title"
                    :class="menuButtonClass"
                >
                    <Link :href="item.href">
                        <component
                            v-if="item.icon"
                            :is="item.icon"
                            class="size-5"
                        />
                        <span>{{ item.title }}</span>
                        <ChevronRight
                            v-if="item.children?.length"
                            class="ml-auto size-4 opacity-70"
                        />
                    </Link>
                </SidebarMenuButton>

                <SidebarMenuSub
                    v-if="item.children?.length"
                    class="mx-2.5 mt-1 gap-1 border-sidebar-border/80 px-3"
                >
                    <SidebarMenuSubItem
                        v-for="child in item.children"
                        :key="child.title"
                        class=""
                    >
                        <SidebarMenuSubButton
                            as-child
                            :is-active="isCurrentUrl(child.href)"
                            class="h-8 rounded-lg px-3 text-sm text-sidebar-primary"
                        >
                            <Link :href="child.href">
                                <span>{{ child.title }}</span>
                            </Link>
                        </SidebarMenuSubButton>
                    </SidebarMenuSubItem>
                </SidebarMenuSub>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
