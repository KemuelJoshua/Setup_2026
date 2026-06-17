<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

type Props = {
    items: NavItem[];
    class?: string;
};

defineProps<Props>();

const menuButtonClass =
    'h-9 rounded-sm px-3 text-sm font-medium text-sidebar-foreground/70 transition-[opacity,transform,background-color,color] duration-200 ease-out hover:bg-sidebar-accent hover:text-sidebar-primary group-data-[collapsible=icon]:size-9! group-data-[collapsible=icon]:p-0!';
</script>

<template>
    <SidebarGroup
        :class="`rounded-lg border border-sidebar-border/70 p-2 shadow-none group-data-[collapsible=icon]:p-2 ${$props.class || ''}`"
    >
        <SidebarGroupContent class="flex flex-col gap-1">
            <div
                class="px-3 pb-1 text-[11px] font-semibold tracking-[0.14em] text-sidebar-foreground/50 uppercase group-data-[collapsible=icon]:hidden"
            >
                Resources
            </div>
        </SidebarGroupContent>
        <SidebarGroupContent>
            <SidebarMenu class="gap-1">
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton
                        :class="menuButtonClass"
                        as-child
                        :tooltip="item.title"
                    >
                        <a
                            :href="toUrl(item.href)"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <component
                                :is="item.icon"
                                class="size-5 transition-transform duration-200 ease-out group-hover/menu-button:scale-105"
                            />
                            <span>{{ item.title }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
