<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const pageKey = computed(() => page.url);
</script>

<template>
    <AppShell variant="sidebar">
        <div class="flex min-h-svh w-full flex-col">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />

            <div data-test="app-body" class="flex min-h-0 flex-1">
                <AppSidebar />
                <AppContent
                    variant="sidebar"
                    class="min-h-[calc(100svh-4rem)] overflow-x-hidden border-0"
                >
                    <div class="relative flex flex-1 flex-col">
                        <div :key="pageKey" class="flex flex-1 flex-col">
                            <slot />
                        </div>
                    </div>
                </AppContent>
            </div>
        </div>
        <Toaster />
    </AppShell>
</template>
