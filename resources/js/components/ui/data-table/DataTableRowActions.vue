<script setup lang="ts">
import { MoreHorizontal } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

withDefaults(
    defineProps<{
        label?: string;
        align?: 'start' | 'center' | 'end';
    }>(),
    {
        label: 'Open row actions',
        align: 'end',
    },
);
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :aria-label="label"
            >
                <MoreHorizontal aria-hidden="true" />
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent :align="align" class="w-40">
            <slot />
        </DropdownMenuContent>
    </DropdownMenu>
</template>
