<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, LayoutGrid, Menu, Moon, Sun } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import MessageDropdown from '@/components/MessageDropdown.vue';
import NotificationDropdown from '@/components/NotificationDropdown.vue';
import UserAccountSheet from '@/components/UserAccountSheet.vue';
import { Button } from '@/components/ui/button';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useAppearance } from '@/composables/useAppearance';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem, NavItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const { resolvedAppearance, updateAppearance } = useAppearance();
const { isCurrentUrl, whenCurrentUrl } = useCurrentUrl();

const activeItemStyles =
    'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

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
    <div>
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-14 items-center px-4 md:max-w-7xl">
              
                <Link :href="dashboard()" class="flex items-center gap-x-2">
                    <component :is="ArrowLeft" class="h-4 w-4" />Dashboard
                </Link>

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

                    <MessageDropdown />

                    <NotificationDropdown />

                    <UserAccountSheet :user="auth.user" />
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-b border-sidebar-border/70"
        >
            <div
                class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
