<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
    previousPageUrl: string | null;
    nextPageUrl: string | null;
}>();
</script>

<template>
    <nav
        v-if="total > 0"
        aria-label="Table pagination"
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
    >
        <p class="text-sm text-muted-foreground">
            Showing
            <span class="font-medium text-foreground">{{ from }}</span>
            to
            <span class="font-medium text-foreground">{{ to }}</span>
            of
            <span class="font-medium text-foreground">{{ total }}</span>
            results
        </p>

        <div class="flex items-center gap-1">
            <Link
                v-if="previousPageUrl"
                :href="previousPageUrl"
                preserve-scroll
                preserve-state
                class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:outline-none"
                aria-label="Go to previous page"
            >
                <ChevronLeft aria-hidden="true" class="size-4" />
            </Link>
            <span
                v-else
                class="inline-flex size-9 cursor-not-allowed items-center justify-center rounded-lg text-muted-foreground/40"
                aria-hidden="true"
            >
                <ChevronLeft class="size-4" />
            </span>

            <template
                v-for="(link, linkIndex) in links.slice(1, -1)"
                :key="`${link.label}-${linkIndex}`"
            >
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    preserve-state
                    class="inline-flex size-9 items-center justify-center rounded-lg text-sm font-medium transition-colors focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:outline-none"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground shadow-xs'
                            : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                    "
                    :aria-current="link.active ? 'page' : undefined"
                    :aria-label="`Go to page ${link.label}`"
                >
                    {{ link.label }}
                </Link>
                <span
                    v-else
                    class="inline-flex size-9 items-center justify-center text-sm text-muted-foreground"
                >
                    {{ link.label }}
                </span>
            </template>

            <Link
                v-if="nextPageUrl"
                :href="nextPageUrl"
                preserve-scroll
                preserve-state
                class="inline-flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:outline-none"
                aria-label="Go to next page"
            >
                <ChevronRight aria-hidden="true" class="size-4" />
            </Link>
            <span
                v-else
                class="inline-flex size-9 cursor-not-allowed items-center justify-center rounded-lg text-muted-foreground/40"
                aria-hidden="true"
            >
                <ChevronRight class="size-4" />
            </span>
        </div>
    </nav>
</template>
