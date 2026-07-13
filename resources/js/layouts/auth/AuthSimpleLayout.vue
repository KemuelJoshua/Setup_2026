<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Award,
    BookOpen,
    BrainCircuit,
    Building2,
    GraduationCap,
    NotebookTabs,
} from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="min-h-svh bg-background lg:grid lg:grid-cols-[minmax(25rem,0.9fr)_1.1fr]"
    >
        <aside
            class="relative hidden min-h-svh overflow-hidden border-r border-sidebar-border bg-sidebar p-10 text-sidebar-foreground lg:flex lg:flex-col xl:p-14"
        >
            <div
                class="absolute -top-24 -left-20 size-80 rounded-full bg-primary/8 blur-3xl"
            />
            <div
                class="absolute -right-32 -bottom-28 size-96 rounded-full bg-primary/10 blur-3xl"
            />

            <div
                class="pointer-events-none absolute inset-0 text-sidebar-foreground/[0.055]"
                aria-hidden="true"
            >
                <BookOpen
                    class="absolute top-[14%] right-[10%] size-24 -rotate-12"
                    :stroke-width="1.25"
                />
                <GraduationCap
                    class="absolute top-[38%] left-[8%] size-28 rotate-12"
                    :stroke-width="1.1"
                />
                <BrainCircuit
                    class="absolute top-[48%] right-[5%] size-32 -rotate-6"
                    :stroke-width="1"
                />
                <NotebookTabs
                    class="absolute bottom-[8%] left-[16%] size-24 -rotate-12"
                    :stroke-width="1.2"
                />
                <Award
                    class="absolute right-[16%] bottom-[13%] size-16 rotate-12"
                    :stroke-width="1.25"
                />
            </div>

            <div class="relative z-10 flex items-center justify-between gap-6">
                <Link
                    :href="home()"
                    class="flex w-fit items-center gap-3 rounded-lg focus-visible:ring-2 focus-visible:ring-sidebar-ring focus-visible:ring-offset-4 focus-visible:ring-offset-sidebar"
                >
                    <span
                        class="flex size-11 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground shadow-[var(--shadow-primary)] shadow-lg"
                    >
                        <GraduationCap class="size-6" :stroke-width="1.8" />
                    </span>
                    <span class="grid gap-0.5">
                        <span class="text-base leading-tight font-semibold">
                            {{ page.props.name }}
                        </span>
                        <span class="text-xs text-sidebar-foreground/50">
                            Learning management system
                        </span>
                    </span>
                </Link>

                <div
                    class="flex size-14 shrink-0 items-center justify-center rounded-xl border border-dashed border-sidebar-border bg-sidebar-accent/60 text-sidebar-foreground/45"
                    title="School logo"
                >
                    <Building2 class="size-6" :stroke-width="1.5" />
                    <span class="sr-only">School logo placeholder</span>
                </div>
            </div>

            <div class="relative z-10 my-auto max-w-lg py-16">
                <p
                    class="mb-4 text-xs font-semibold tracking-[0.2em] text-sidebar-primary uppercase"
                >
                    Your learning space
                </p>
                <h2
                    class="max-w-md text-4xl leading-[1.12] font-semibold tracking-tight xl:text-5xl"
                >
                    Learn, grow, and achieve at your own pace.
                </h2>
                <p
                    class="mt-5 max-w-md text-base leading-7 text-sidebar-foreground/60"
                >
                    Everything you need for your courses, progress, and learning
                    journey—all in one comfortable place.
                </p>
            </div>

            <div
                class="relative z-10 flex items-center justify-between gap-6 text-xs text-sidebar-foreground/40"
            >
                <p class="font-medium">
                    A focused space built for better learning.
                </p>
                <p class="shrink-0">
                    Powered by
                    <span class="font-semibold text-sidebar-foreground/65"
                        >BlitzDev</span
                    >
                </p>
            </div>
        </aside>

        <main
            class="relative flex min-h-svh items-center justify-center overflow-hidden px-5 py-10 sm:px-8 lg:px-12"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,var(--primary-glow),transparent_32%)] opacity-40 lg:opacity-25"
            />
            <BookOpen
                class="pointer-events-none absolute -top-5 -right-6 size-32 rotate-12 text-foreground/[0.025] lg:hidden"
                :stroke-width="1"
                aria-hidden="true"
            />

            <div class="relative z-10 w-full max-w-md">
                <Link
                    :href="home()"
                    class="mx-auto mb-8 flex w-fit items-center gap-2.5 rounded-lg font-semibold focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-4 lg:hidden"
                >
                    <span
                        class="flex size-10 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-[var(--shadow-primary)] shadow-md"
                    >
                        <AppLogoIcon class="size-5 fill-current" />
                    </span>
                    <span>{{ page.props.name }}</span>
                </Link>

                <section
                    class="rounded-2xl border border-border/80 bg-card p-6 text-card-foreground shadow-[0_24px_70px_-28px_var(--shadow-color)] sm:p-9"
                >
                    <div class="mb-8 space-y-2.5">
                        <div
                            class="bg-primary-soft mb-5 flex size-16 items-center justify-center rounded-xl border border-dashed border-border text-primary lg:hidden"
                            title="School logo"
                        >
                            <Building2 class="size-7" :stroke-width="1.5" />
                            <span class="sr-only">School logo placeholder</span>
                        </div>
                        <h1 class="text-2xl font-semibold tracking-tight">
                            {{ title }}
                        </h1>
                        <p class="text-sm leading-6 text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>

                    <slot />
                </section>

                <div
                    class="mt-6 grid gap-1 text-center text-xs leading-5 text-muted-foreground"
                >
                    <p>Secure access to your learning dashboard</p>
                    <p class="lg:hidden">
                        Powered by
                        <span class="font-semibold text-foreground/65"
                            >BlitzDev</span
                        >
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>
