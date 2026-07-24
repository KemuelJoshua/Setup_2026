```vue
<script setup lang="ts">
import { ListFilter, Search, X } from '@lucide/vue';
import { computed, ref, useSlots, type HTMLAttributes } from 'vue';

import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { cn } from '@/lib/utils';

type Props = {
    count?: number;
    itemLabel?: string;
    searchPlaceholder?: string;
    searchLabel?: string;
    showSearch?: boolean;
    filterDialogTitle?: string;
    filterDialogDescription?: string;
    filterButtonLabel?: string;
    class?: HTMLAttributes['class'];
};

const props = withDefaults(defineProps<Props>(), {
    count: undefined,
    itemLabel: 'item',
    searchPlaceholder: 'Search...',
    searchLabel: 'Search table',
    showSearch: true,
    filterDialogTitle: 'More Filters',
    filterDialogDescription:
        'Apply additional filters to narrow down the table results.',
    filterButtonLabel: 'Filters',
    class: undefined,
});

const slots = useSlots();

const searchQuery = defineModel<string>({ default: '' });
const isFilterDialogOpen = ref(false);

const hasOtherFilters = computed(() => Boolean(slots['other-filters']));
</script>

<template>
    <section
        data-slot="data-table-toolbar"
        :class="
            cn(
                'flex flex-col gap-5 border-b border-border/70 pb-5 sm:flex-row sm:items-end sm:justify-between',
                props.class,
            )
        "
    >
        <div
            v-if="
                props.showSearch ||
                props.count !== undefined ||
                hasOtherFilters
            "
            class="flex min-w-0 flex-col gap-2 sm:flex-row sm:items-center"
        >
            <label
                v-if="props.showSearch"
                class="relative block w-full sm:w-64"
            >
                <span class="sr-only">{{ props.searchLabel }}</span>

                <Search
                    aria-hidden="true"
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />

                <input
                    v-model="searchQuery"
                    type="search"
                    :placeholder="props.searchPlaceholder"
                    class="h-9 w-full rounded-lg border border-input bg-background pr-9 pl-9 text-sm shadow-xs outline-none transition-[border-color,box-shadow,background-color] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                />

                <button
                    v-if="searchQuery"
                    type="button"
                    :aria-label="`Clear ${props.searchLabel.toLowerCase()}`"
                    class="absolute top-1/2 right-1.5 flex size-6 -translate-y-1/2 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40"
                    @click="searchQuery = ''"
                >
                    <X aria-hidden="true" class="size-3.5" />
                </button>
            </label>

            <span
                v-if="props.count !== undefined"
                class="inline-flex w-fit shrink-0 rounded-md bg-muted px-2 py-1 text-xs font-medium text-muted-foreground"
            >
                {{ props.count }}
                {{ props.itemLabel }}{{ props.count === 1 ? '' : 's' }}
            </span>

            <Dialog
                v-if="hasOtherFilters"
                v-model:open="isFilterDialogOpen"
            >
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    class="w-full gap-2 sm:w-auto"
                    @click="isFilterDialogOpen = true"
                >
                    <ListFilter aria-hidden="true" class="size-4" />

                    {{ props.filterButtonLabel }}
                </Button>

                <DialogContent class="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>
                            {{ props.filterDialogTitle }}
                        </DialogTitle>

                        <DialogDescription>
                            {{ props.filterDialogDescription }}
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-4 py-2">
                        <slot
                            name="other-filters"
                            :close="
                                () => {
                                    isFilterDialogOpen = false;
                                }
                            "
                        />
                    </div>

                    <DialogFooter>
                        <slot
                            name="filter-actions"
                            :close="
                                () => {
                                    isFilterDialogOpen = false;
                                }
                            "
                        >
                            <DialogClose as-child>
                                <Button type="button" variant="outline">
                                    Close
                                </Button>
                            </DialogClose>
                        </slot>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>

        <div
            v-if="$slots.filters || $slots.actions"
            class="flex w-full flex-col gap-2.5 sm:w-auto sm:flex-row sm:items-center"
        >
            <slot name="filters" />

            <slot name="actions" />
        </div>
    </section>
</template>
```
