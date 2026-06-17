<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from '@lucide/vue';
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import UserInfo from '@/components/UserInfo.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const { isMobile, state } = useSidebar();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="h-12 rounded-sm border border-sidebar-border/70 bg-sidebar px-2.5 text-sidebar-primary shadow-none transition-[opacity,transform,background-color,color] duration-200 ease-out group-data-[collapsible=icon]:size-12! group-data-[collapsible=icon]:p-0! hover:bg-sidebar-accent hover:text-sidebar-primary data-[state=open]:bg-sidebar-accent/90 data-[state=open]:text-sidebar-primary"
                        data-test="sidebar-menu-button"
                    >
                        <UserInfo :user="user" show-email />
                        <ChevronsUpDown
                            class="ml-auto size-4 transition-transform duration-200 ease-out group-hover/menu-button:scale-105"
                        />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                    :side="
                        isMobile
                            ? 'bottom'
                            : state === 'collapsed'
                              ? 'left'
                              : 'bottom'
                    "
                    align="end"
                    :side-offset="4"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
