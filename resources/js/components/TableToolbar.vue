<script setup lang="ts">
import { Search, X } from '@lucide/vue';

type Props = {
    title: string;
    description?: string;
    count?: number;
    itemLabel?: string;
    searchPlaceholder?: string;
    searchLabel?: string;
    showSearch?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    description: undefined,
    count: undefined,
    itemLabel: 'item',
    searchPlaceholder: 'Search…',
    searchLabel: 'Search table',
    showSearch: true,
});

const searchQuery = defineModel<string>({ default: '' });
</script>

<template>
    <section
        data-slot="table-toolbar"
        class="flex flex-col gap-5 border-b border-border/70 pb-5 sm:flex-row sm:items-end sm:justify-between"
    >
        <div class="min-w-0 space-y-1.5">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                <h2
                    class="text-lg font-semibold tracking-tight text-foreground"
                >
                    {{ props.title }}
                </h2>
            </div>
            <p
                v-if="props.description"
                class="max-w-2xl text-sm leading-5 text-muted-foreground"
            >
                {{ props.description }}
            </p>
        </div>

        <div
            class="flex w-full flex-col gap-2.5 sm:w-auto sm:flex-row sm:items-center"
        >
            <slot name="filters" />

            <label v-if="props.showSearch" class="relative block sm:w-64">
                <span class="sr-only">{{ props.searchLabel }}</span>
                <Search
                    aria-hidden="true"
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <input
                    v-model="searchQuery"
                    type="search"
                    :placeholder="props.searchPlaceholder"
                    class="h-9 w-full rounded-lg border border-input bg-background pr-9 pl-9 text-sm shadow-xs transition-[border-color,box-shadow,background-color] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                />
                <button
                    v-if="searchQuery"
                    type="button"
                    class="absolute top-1/2 right-1.5 flex size-6 -translate-y-1/2 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:outline-none"
                    :aria-label="`Clear ${props.searchLabel.toLowerCase()}`"
                    @click="searchQuery = ''"
                >
                    <X aria-hidden="true" class="size-3.5" />
                </button>
            </label>

            <slot name="actions" />
        </div>
    </section>
</template>
