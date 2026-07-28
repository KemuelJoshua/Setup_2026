<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    BarChart3,
    CheckCircle2,
    ClipboardCheck,
    FileChartColumn,
    ShieldCheck,
} from '@lucide/vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="grid min-h-svh bg-background text-foreground lg:grid-cols-[minmax(28rem,1.08fr)_minmax(28rem,0.92fr)]"
    >
        <aside
            class="relative hidden min-h-svh overflow-hidden bg-foreground p-10 text-background lg:flex lg:flex-col xl:p-14 dark:bg-card dark:text-card-foreground"
        >
            <div
                class="pointer-events-none absolute inset-0 opacity-[0.045] [background-image:linear-gradient(to_right,currentColor_1px,transparent_1px),linear-gradient(to_bottom,currentColor_1px,transparent_1px)] [background-size:3.75rem_3.75rem]"
            />
            <div
                class="pointer-events-none absolute -right-28 -bottom-36 size-[30rem] rounded-full bg-primary/25 blur-3xl"
            />

            <Link
                :href="home()"
                class="relative z-10 flex w-fit items-center gap-3 rounded-xl focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-4 focus-visible:ring-offset-foreground focus-visible:outline-none"
            >
                <span
                    class="grid size-11 place-items-center rounded-xl bg-primary text-sm font-black tracking-tighter text-primary-foreground shadow-lg shadow-primary/20"
                >
                    PE
                </span>
                <span>
                    <span class="block text-base font-bold tracking-tight">
                        PEMS
                    </span>
                    <span
                        class="block text-[10px] font-medium tracking-[0.08em] opacity-50 uppercase"
                    >
                        Project Evaluation Management System
                    </span>
                </span>
            </Link>

            <div class="relative z-10 my-auto max-w-xl py-14">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3 py-1.5 text-xs font-semibold text-primary"
                >
                    <ShieldCheck class="size-3.5" />
                    Authorized personnel only
                </div>
                <h2
                    class="mt-6 max-w-lg text-4xl leading-[1.06] font-semibold tracking-[-0.04em] text-balance xl:text-5xl"
                >
                    Every project tells a story.
                    <span class="text-primary">PEMS makes it measurable.</span>
                </h2>
                <p class="mt-5 max-w-lg text-base leading-7 opacity-55">
                    Access one secure workspace for project monitoring,
                    evaluation evidence, and decision-ready reporting.
                </p>

                <div
                    class="mt-10 grid max-w-lg grid-cols-3 gap-3"
                    aria-hidden="true"
                >
                    <div
                        class="rounded-xl border border-background/10 bg-background/5 p-4 dark:border-border dark:bg-background/40"
                    >
                        <ClipboardCheck class="size-5 text-primary" />
                        <p class="mt-5 text-2xl font-semibold">24</p>
                        <p class="mt-1 text-[10px] opacity-45">
                            Active projects
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-background/10 bg-background/5 p-4 dark:border-border dark:bg-background/40"
                    >
                        <FileChartColumn class="size-5 text-primary" />
                        <p class="mt-5 text-2xl font-semibold">18</p>
                        <p class="mt-1 text-[10px] opacity-45">On track</p>
                    </div>
                    <div
                        class="rounded-xl border border-background/10 bg-background/5 p-4 dark:border-border dark:bg-background/40"
                    >
                        <BarChart3 class="size-5 text-primary" />
                        <p class="mt-5 text-2xl font-semibold">75%</p>
                        <p class="mt-1 text-[10px] opacity-45">Reviewed</p>
                    </div>
                </div>
            </div>

            <div
                class="relative z-10 flex items-center gap-2 text-xs opacity-45"
            >
                <CheckCircle2 class="size-4 text-primary opacity-100" />
                Secure access. Clear accountability. Better decisions.
            </div>
        </aside>

        <main
            class="relative flex min-h-svh items-center justify-center overflow-hidden px-5 py-10 sm:px-8 lg:px-12"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,var(--primary-glow),transparent_28%)] opacity-60"
            />
            <div
                class="relative z-10 w-full max-w-[29rem] rounded-[1.75rem] border border-border/80 bg-card p-6 shadow-[0_32px_90px_-35px_var(--shadow-color)] sm:p-9"
            >
                <div class="mb-8 flex items-center justify-between gap-4">
                    <Link
                        :href="home()"
                        class="inline-flex items-center gap-2 text-xs font-semibold text-muted-foreground transition hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none lg:hidden"
                    >
                        <ArrowLeft class="size-4" />
                        Back to home
                    </Link>
                    <div class="ml-auto flex items-center gap-2 lg:hidden">
                        <span
                            class="grid size-9 place-items-center rounded-lg bg-foreground text-[10px] font-black tracking-tighter text-background dark:bg-primary dark:text-primary-foreground"
                        >
                            PE
                        </span>
                        <span class="text-sm font-bold">PEMS</span>
                    </div>
                </div>

                <div class="mb-8">
                    <p
                        class="mb-3 text-[11px] font-bold tracking-[0.18em] text-primary uppercase"
                    >
                        Secure portal
                    </p>
                    <h1
                        v-if="title"
                        class="text-3xl font-semibold tracking-[-0.035em]"
                    >
                        {{ title }}
                    </h1>
                    <p
                        v-if="description"
                        class="mt-2 text-sm leading-6 text-muted-foreground"
                    >
                        {{ description }}
                    </p>
                </div>

                <slot />

                <p
                    class="mt-8 border-t border-border pt-6 text-center text-[11px] leading-5 text-muted-foreground"
                >
                    This system is restricted to authorized users. Activity may
                    be monitored for security and accountability.
                </p>
            </div>
        </main>
    </div>
</template>
