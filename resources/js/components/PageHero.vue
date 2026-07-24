<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

type Props = {
    class?: HTMLAttributes['class'];
};

const props = defineProps<Props>();
</script>

<template>
    <section
        :class="
            cn(
                'relative overflow-hidden rounded-2xl border bg-card px-5 py-6 shadow-sm sm:px-7',
                props.class,
            )
        "
    >
        <div
            class="pointer-events-none absolute -top-24 right-0 size-72 rounded-full bg-primary/10 blur-3xl"
        />

        <div
            class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-start gap-4">
                <div
                    v-if="$slots.icon"
                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm"
                >
                    <slot name="icon" />
                </div>

                <div class="max-w-2xl">
                    <div
                        v-if="$slots.badge"
                        class="flex items-center gap-2 text-xs font-semibold tracking-wider text-primary uppercase"
                    >
                        <slot name="badge" />
                    </div>

                    <h1
                        v-if="$slots.title"
                        class="mt-1 text-2xl font-semibold tracking-tight sm:text-3xl"
                    >
                        <slot name="title" />
                    </h1>

                    <p
                        v-if="$slots.description"
                        class="mt-2 text-sm leading-6 text-muted-foreground"
                    >
                        <slot name="description" />
                    </p>
                </div>
            </div>

            <div v-if="$slots.actions" class="shrink-0">
                <slot name="actions" />
            </div>
        </div>
    </section>
</template>
