<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
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

const props = defineProps<{
    groups: NavGroup[];
}>();

const { isCurrentUrl } = useCurrentUrl();

const menuButtonClass =
    'h-10.5 rounded-md px-3 text-sm font-medium text-sidebar-foreground/65 transition-[background-color,color,box-shadow] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] hover:bg-sidebar-accent/70 hover:text-sidebar-primary data-[active=true]:bg-sidebar-primary data-[active=true]:font-semibold data-[active=true]:text-sidebar-primary-foreground data-[active=true]:shadow-sm data-[active=true]:shadow-sidebar-primary/20 data-[active=true]:hover:bg-sidebar-primary data-[active=true]:hover:text-sidebar-primary-foreground group-data-[collapsible=icon]:size-10! group-data-[collapsible=icon]:p-0!';

const expandedItems = ref<Record<string, boolean>>({});

const getItemKey = (item: NavItem): string => item.title;

const hasChildren = (item: NavItem): boolean =>
    (item.children?.length ?? 0) > 0;

const activeParentKeys = computed(() =>
    props.groups.flatMap((group) =>
        group.items
            .filter((item) => hasChildren(item) && hasActiveChild(item))
            .map((item) => getItemKey(item)),
    ),
);

const toggleItem = (item: NavItem): void => {
    const itemKey = getItemKey(item);
    const shouldExpand = !isItemExpanded(item);

    expandedItems.value = shouldExpand ? { [itemKey]: true } : {};
};

const isItemExpanded = (item: NavItem): boolean => {
    const itemKey = getItemKey(item);

    return expandedItems.value[itemKey] ?? hasActiveChild(item);
};

const hasActiveChild = (item: NavItem): boolean =>
    item.children?.some((child) => isCurrentUrl(child.href)) ?? false;

const isItemActive = (item: NavItem): boolean =>
    isCurrentUrl(item.href) || hasActiveChild(item);

watch(
    activeParentKeys,
    (itemKeys) => {
        expandedItems.value = Object.fromEntries(
            itemKeys.map((itemKey) => [itemKey, true]),
        );
    },
    { immediate: true },
);
</script>

<template>
    <SidebarGroup
        v-for="group in groups"
        :key="group.title"
        class="gap-1.5 p-0 shadow-none"
    >
        <SidebarGroupLabel
            class="h-7 px-3 text-[11px] font-semibold tracking-[0.1em] text-sidebar-foreground/40 uppercase"
        >
            {{ group.title }}
        </SidebarGroupLabel>
        <SidebarMenu class="gap-2">
            <SidebarMenuItem v-for="item in group.items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isItemActive(item)"
                    :tooltip="item.title"
                    :class="menuButtonClass"
                >
                    <button
                        v-if="hasChildren(item)"
                        type="button"
                        @click="toggleItem(item)"
                    >
                        <component
                            v-if="item.icon"
                            :is="item.icon"
                            class="size-[18px] transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/menu-button:scale-105"
                            :stroke-width="1.8"
                        />
                        <span>{{ item.title }}</span>
                        <ChevronRight
                            class="ml-auto size-4 opacity-50 transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                            :class="isItemExpanded(item) ? 'rotate-90' : ''"
                        />
                    </button>
                    <Link v-else :href="item.href">
                        <component
                            v-if="item.icon"
                            :is="item.icon"
                            class="size-[18px] transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/menu-button:scale-105"
                            :stroke-width="1.8"
                        />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>

                <Transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="-translate-y-1 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition-all duration-150 ease-out"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="-translate-y-1 opacity-0"
                >
                    <SidebarMenuSub
                        v-if="hasChildren(item) && isItemExpanded(item)"
                        class="mt-1 mr-0 ml-5 translate-x-0 gap-1 border-sidebar-border/70 py-1 pr-0 pl-4"
                    >
                        <SidebarMenuSubItem
                            v-for="child in item.children"
                            :key="child.title"
                            class=""
                        >
                            <SidebarMenuSubButton
                                as-child
                                size="sm"
                                :is-active="isCurrentUrl(child.href)"
                                class="h-8.5 rounded-md px-2.5 text-[13px] text-sidebar-foreground/60 transition-colors duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] hover:bg-sidebar-accent/60 hover:text-sidebar-foreground data-[active=true]:bg-transparent data-[active=true]:font-semibold data-[active=true]:text-sidebar-primary"
                            >
                                <Link :href="child.href">
                                    <span>{{ child.title }}</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                    </SidebarMenuSub>
                </Transition>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
