<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { GraduationCap } from '@lucide/vue';

const page = usePage();
</script>

<template>
    <div
        class="flex aspect-square size-9 shrink-0 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground shadow-sm shadow-sidebar-primary/20"
    >
        <GraduationCap class="size-5 text-white" :stroke-width="1.8" />
    </div>
    <div
        class="grid min-w-0 flex-1 gap-0.5 text-left group-data-[collapsible=icon]:hidden"
    >
        <span class="truncate text-sm leading-tight font-semibold">
            {{ page.props.name }}
        </span>
        <span
            class="truncate text-[11px] leading-tight font-medium text-sidebar-foreground/50"
        >
            Project Evaluation Management System
        </span>
    </div>
</template>
