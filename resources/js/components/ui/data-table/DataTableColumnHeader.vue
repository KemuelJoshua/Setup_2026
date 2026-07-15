<script
    setup
    lang="ts"
    generic="TData, TValue"
>
import { ArrowDown, ArrowUp, ChevronsUpDown } from '@lucide/vue';
import type { Column } from '@tanstack/vue-table';
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

const props = defineProps<{
    column: Column<TData, TValue>;
    title: string;
    class?: HTMLAttributes['class'];
}>();

const sorted = computed(() => props.column.getIsSorted());
const ariaSort = computed(() => {
    if (sorted.value === 'asc') {
        return 'ascending';
    }

    if (sorted.value === 'desc') {
        return 'descending';
    }

    return 'none';
});
</script>

<template>
    <div
        v-if="props.column.getCanSort()"
        :class="cn('flex items-center gap-2', props.class)"
        :aria-sort="ariaSort"
    >
        <Button
            type="button"
            variant="ghost"
            size="sm"
            class="-ml-2 h-8 px-2"
            @click="props.column.toggleSorting(sorted === 'asc')"
        >
            <span>{{ props.title }}</span>
            <ArrowUp v-if="sorted === 'asc'" aria-hidden="true" />
            <ArrowDown v-else-if="sorted === 'desc'" aria-hidden="true" />
            <ChevronsUpDown v-else aria-hidden="true" />
        </Button>
    </div>

    <div v-else :class="cn('flex items-center gap-2', props.class)">
        <span>{{ props.title }}</span>
    </div>
</template>
