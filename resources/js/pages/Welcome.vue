<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BarChart3,
    Check,
    CheckCircle2,
    ClipboardCheck,
    FileChartColumn,
    FolderKanban,
    LogIn,
    Moon,
    MoveUpRight,
    Network,
    ShieldCheck,
    Sparkles,
    Sun,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { home, login } from '@/routes';
import { dashboard } from '@/routes/admin';

const page = usePage();
const { resolvedAppearance, updateAppearance } = useAppearance();

const isAuthenticated = computed(() => Boolean(page.props.auth.user));
const portalRoute = computed(() =>
    isAuthenticated.value ? dashboard() : login(),
);
const portalLabel = computed(() =>
    isAuthenticated.value ? 'Go to dashboard' : 'Sign in to PEMS',
);
const themeLabel = computed(() =>
    resolvedAppearance.value === 'dark'
        ? 'Switch to light mode'
        : 'Switch to dark mode',
);

function toggleAppearance(): void {
    updateAppearance(
        resolvedAppearance.value === 'dark' ? 'light' : 'dark',
    );
}

const workflow = [
    {
        step: '01',
        title: 'Capture',
        description: 'Centralize project records and supporting documents.',
    },
    {
        step: '02',
        title: 'Evaluate',
        description: 'Review progress, evidence, and measurable outcomes.',
    },
    {
        step: '03',
        title: 'Decide',
        description: 'Turn consistent findings into confident action.',
    },
];
</script>

<template>
    <Head title="Project Evaluation Management System">
        <meta
            head-key="description"
            name="description"
            content="PEMS brings project monitoring, evaluation, and reporting into one secure workspace."
        />
    </Head>

    <div class="min-h-svh bg-background text-foreground">
        <header
            class="sticky top-0 z-50 border-b border-border/70 bg-background/85 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-5 px-5 sm:px-8 lg:px-10"
            >
                <Link
                    :href="home()"
                    class="flex min-w-0 items-center gap-3 rounded-xl focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                >
                    <span
                        class="grid size-11 shrink-0 place-items-center rounded-xl bg-foreground text-sm font-black tracking-tighter text-background shadow-sm dark:bg-primary dark:text-primary-foreground"
                    >
                        PE
                    </span>
                    <span class="min-w-0">
                        <span
                            class="block truncate text-sm font-bold tracking-tight sm:text-base"
                        >
                            PEMS
                        </span>
                        <span
                            class="block truncate text-[10px] font-medium tracking-[0.08em] text-muted-foreground uppercase sm:text-[11px]"
                        >
                            Project Evaluation Management System
                        </span>
                    </span>
                </Link>

                <div class="flex shrink-0 items-center gap-2">
                    <button
                        type="button"
                        class="grid size-10 cursor-pointer place-items-center rounded-full text-muted-foreground transition hover:bg-accent hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        :aria-label="themeLabel"
                        :title="themeLabel"
                        @click="toggleAppearance"
                    >
                        <Sun
                            v-if="resolvedAppearance === 'dark'"
                            class="size-4.5"
                        />
                        <Moon v-else class="size-4.5" />
                    </button>
                    <Link
                        :href="portalRoute"
                        class="inline-flex h-10 items-center gap-2 rounded-full bg-foreground px-4 text-sm font-semibold text-background transition hover:-translate-y-0.5 hover:shadow-lg focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none dark:bg-primary dark:text-primary-foreground sm:px-5"
                    >
                        <span class="hidden sm:inline">{{ portalLabel }}</span>
                        <span class="sm:hidden">{{
                            isAuthenticated ? 'Dashboard' : 'Sign in'
                        }}</span>
                        <ArrowRight class="size-4" />
                    </Link>
                </div>
            </div>
        </header>

        <main>
            <section class="relative overflow-hidden">
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_78%_18%,var(--primary-glow),transparent_27%)]"
                />
                <div
                    class="pointer-events-none absolute inset-0 opacity-[0.035] [background-image:linear-gradient(to_right,currentColor_1px,transparent_1px),linear-gradient(to_bottom,currentColor_1px,transparent_1px)] [background-size:3.5rem_3.5rem]"
                />

                <div
                    class="relative mx-auto grid max-w-7xl items-center gap-14 px-5 py-16 sm:px-8 sm:py-24 lg:grid-cols-[1.02fr_0.98fr] lg:px-10 lg:py-28"
                >
                    <div class="max-w-2xl">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/8 px-3 py-1.5 text-xs font-semibold text-primary"
                        >
                            <Sparkles class="size-3.5" />
                            Evidence in. Better decisions out.
                        </div>
                        <h1
                            class="mt-6 text-5xl leading-[0.98] font-semibold tracking-[-0.055em] text-balance sm:text-6xl lg:text-7xl"
                        >
                            Make every project
                            <span class="relative inline-block text-primary">
                                count.
                                <span
                                    class="absolute right-0 -bottom-1 left-0 h-1 rounded-full bg-primary/25"
                                />
                            </span>
                        </h1>
                        <p
                            class="mt-7 max-w-xl text-base leading-7 text-muted-foreground sm:text-lg sm:leading-8"
                        >
                            PEMS gives DOST teams one reliable place to monitor
                            initiatives, evaluate results, and turn project data
                            into accountable decisions.
                        </p>

                        <div class="mt-9 flex flex-wrap items-center gap-3">
                            <Link
                                :href="portalRoute"
                                class="inline-flex h-12 items-center gap-2 rounded-xl bg-primary px-5 text-sm font-bold text-primary-foreground shadow-lg shadow-[var(--shadow-primary)] transition hover:-translate-y-0.5 hover:bg-[var(--primary-hover)] focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                            >
                                {{ portalLabel }}
                                <ArrowRight class="size-4" />
                            </Link>
                            <a
                                href="#how-it-works"
                                class="inline-flex h-12 items-center gap-2 rounded-xl border border-border bg-card px-5 text-sm font-semibold shadow-sm transition hover:border-foreground/20 hover:bg-accent"
                            >
                                See how it works
                                <MoveUpRight class="size-4" />
                            </a>
                        </div>

                        <div
                            class="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-xs font-medium text-muted-foreground"
                        >
                            <span class="flex items-center gap-2">
                                <ShieldCheck class="size-4 text-primary" />
                                Role-based secure access
                            </span>
                            <span class="flex items-center gap-2">
                                <CheckCircle2 class="size-4 text-primary" />
                                One source of project truth
                            </span>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-xl lg:ml-auto">
                        <div
                            class="absolute -inset-8 rounded-full bg-primary/12 blur-3xl"
                        />
                        <div
                            class="relative rotate-[-1.5deg] rounded-[1.75rem] border border-border/80 bg-card p-3 shadow-[0_35px_90px_-30px_var(--shadow-color)] transition duration-500 hover:rotate-0"
                        >
                            <div
                                class="overflow-hidden rounded-[1.15rem] border border-border bg-background"
                            >
                                <div
                                    class="flex items-center justify-between border-b border-border bg-card px-5 py-4"
                                >
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="grid size-9 place-items-center rounded-lg bg-foreground text-[10px] font-black text-background dark:bg-primary dark:text-primary-foreground"
                                        >
                                            PE
                                        </span>
                                        <div>
                                            <p class="text-xs font-semibold">
                                                Portfolio overview
                                            </p>
                                            <p
                                                class="text-[10px] text-muted-foreground"
                                            >
                                                Updated just now
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="rounded-full bg-primary/10 px-2.5 py-1 text-[10px] font-bold text-primary"
                                    >
                                        LIVE
                                    </span>
                                </div>

                                <div class="p-5 sm:p-6">
                                    <div
                                        class="grid grid-cols-2 gap-3 sm:grid-cols-3"
                                    >
                                        <div
                                            class="col-span-2 rounded-xl bg-foreground p-5 text-background dark:bg-primary dark:text-primary-foreground sm:col-span-1"
                                        >
                                            <FolderKanban class="size-5" />
                                            <p
                                                class="mt-6 text-3xl font-semibold tracking-tight"
                                            >
                                                24
                                            </p>
                                            <p
                                                class="mt-1 text-[11px] opacity-65"
                                            >
                                                Active projects
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-xl border border-border bg-card p-4"
                                        >
                                            <ClipboardCheck
                                                class="size-5 text-primary"
                                            />
                                            <p
                                                class="mt-6 text-2xl font-semibold"
                                            >
                                                18
                                            </p>
                                            <p
                                                class="mt-1 text-[11px] text-muted-foreground"
                                            >
                                                On track
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-xl border border-border bg-card p-4"
                                        >
                                            <FileChartColumn
                                                class="size-5 text-primary"
                                            />
                                            <p
                                                class="mt-6 text-2xl font-semibold"
                                            >
                                                6
                                            </p>
                                            <p
                                                class="mt-1 text-[11px] text-muted-foreground"
                                            >
                                                For review
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-4 rounded-xl border border-border bg-card p-4"
                                    >
                                        <div
                                            class="flex items-center justify-between gap-4"
                                        >
                                            <div>
                                                <p class="text-xs font-semibold">
                                                    Completion by quarter
                                                </p>
                                                <p
                                                    class="mt-0.5 text-[10px] text-muted-foreground"
                                                >
                                                    Portfolio performance
                                                </p>
                                            </div>
                                            <BarChart3
                                                class="size-4 text-primary"
                                            />
                                        </div>
                                        <div
                                            class="mt-5 flex h-24 items-end gap-2"
                                            aria-label="Illustrative quarterly completion chart"
                                        >
                                            <div
                                                class="h-[42%] flex-1 rounded-t bg-primary/20"
                                            />
                                            <div
                                                class="h-[58%] flex-1 rounded-t bg-primary/35"
                                            />
                                            <div
                                                class="h-[72%] flex-1 rounded-t bg-primary/55"
                                            />
                                            <div
                                                class="h-[88%] flex-1 rounded-t bg-primary"
                                            />
                                            <div
                                                class="h-[68%] flex-1 rounded-t bg-primary/45"
                                            />
                                            <div
                                                class="h-[92%] flex-1 rounded-t bg-primary/80"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="absolute -right-3 -bottom-5 flex items-center gap-3 rounded-xl border border-border bg-card p-3 shadow-xl sm:-right-6"
                        >
                            <span
                                class="grid size-9 place-items-center rounded-full bg-emerald-500/12 text-emerald-600 dark:text-emerald-400"
                            >
                                <Check class="size-4" :stroke-width="3" />
                            </span>
                            <div>
                                <p class="text-xs font-semibold">
                                    Report validated
                                </p>
                                <p class="text-[10px] text-muted-foreground">
                                    Ready for endorsement
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section
                id="how-it-works"
                class="border-y border-border/70 bg-card py-18 sm:py-22"
            >
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <div
                        class="grid gap-8 lg:grid-cols-[0.7fr_1.3fr] lg:items-end"
                    >
                        <div>
                            <p
                                class="text-xs font-bold tracking-[0.18em] text-primary uppercase"
                            >
                                A clearer workflow
                            </p>
                            <h2
                                class="mt-3 text-3xl font-semibold tracking-tight text-balance sm:text-4xl"
                            >
                                From field data to informed action.
                            </h2>
                        </div>
                        <p
                            class="max-w-2xl text-sm leading-7 text-muted-foreground lg:justify-self-end sm:text-base"
                        >
                            Replace disconnected records and scattered updates
                            with a consistent evaluation process everyone can
                            follow.
                        </p>
                    </div>

                    <div class="mt-11 grid gap-4 md:grid-cols-3">
                        <article
                            v-for="item in workflow"
                            :key="item.step"
                            class="group rounded-2xl border border-border bg-background p-6 transition hover:-translate-y-1 hover:border-primary/35 hover:shadow-lg"
                        >
                            <div
                                class="flex items-center justify-between gap-4"
                            >
                                <span
                                    class="font-mono text-xs font-bold text-primary"
                                >
                                    / {{ item.step }}
                                </span>
                                <ArrowRight
                                    class="size-4 text-muted-foreground transition group-hover:translate-x-1 group-hover:text-primary"
                                />
                            </div>
                            <h3 class="mt-10 text-xl font-semibold">
                                {{ item.title }}
                            </h3>
                            <p
                                class="mt-2 text-sm leading-6 text-muted-foreground"
                            >
                                {{ item.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="py-18 sm:py-22">
                <div
                    class="mx-auto grid max-w-7xl gap-5 px-5 sm:px-8 md:grid-cols-3 lg:px-10"
                >
                    <article
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <Network class="size-6 text-primary" />
                        <h3 class="mt-8 text-lg font-semibold">
                            Connected portfolio
                        </h3>
                        <p
                            class="mt-2 text-sm leading-6 text-muted-foreground"
                        >
                            See programs, projects, milestones, and evidence in
                            context—not across separate files.
                        </p>
                    </article>
                    <article
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <BarChart3 class="size-6 text-primary" />
                        <h3 class="mt-8 text-lg font-semibold">
                            Decision-ready insight
                        </h3>
                        <p
                            class="mt-2 text-sm leading-6 text-muted-foreground"
                        >
                            Surface progress and outcomes in a format built for
                            review, reporting, and action.
                        </p>
                    </article>
                    <article
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <Users class="size-6 text-primary" />
                        <h3 class="mt-8 text-lg font-semibold">
                            Accountable collaboration
                        </h3>
                        <p
                            class="mt-2 text-sm leading-6 text-muted-foreground"
                        >
                            Give each stakeholder the right view and keep every
                            evaluation moving.
                        </p>
                    </article>
                </div>
            </section>

            <section class="px-5 pb-18 sm:px-8 sm:pb-24 lg:px-10">
                <div
                    class="relative mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 overflow-hidden rounded-[1.75rem] bg-foreground px-6 py-10 text-background sm:px-10 sm:py-12 lg:flex-row lg:items-center dark:bg-card dark:text-card-foreground"
                >
                    <div
                        class="absolute -right-24 -bottom-36 size-80 rounded-full bg-primary/25 blur-3xl"
                    />
                    <div class="relative max-w-2xl">
                        <p
                            class="text-xs font-bold tracking-[0.18em] text-primary uppercase"
                        >
                            Secure PEMS access
                        </p>
                        <h2
                            class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl"
                        >
                            Ready to see the whole picture?
                        </h2>
                        <p class="mt-3 text-sm leading-6 opacity-65 sm:text-base">
                            Sign in to continue monitoring projects, reviewing
                            evidence, and reporting results.
                        </p>
                    </div>
                    <Link
                        :href="portalRoute"
                        class="relative inline-flex h-12 shrink-0 items-center gap-2 rounded-xl bg-primary px-5 text-sm font-bold text-primary-foreground transition hover:-translate-y-0.5 hover:bg-[var(--primary-hover)]"
                    >
                        <LogIn class="size-4" />
                        {{ portalLabel }}
                    </Link>
                </div>
            </section>
        </main>

        <footer class="border-t border-border bg-card">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-6 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10"
            >
                <p>
                    © {{ new Date().getFullYear() }} {{ page.props.name }}.
                    Project Evaluation Management System.
                </p>
                <p>Built for transparent, evidence-led evaluation.</p>
            </div>
        </footer>
    </div>
</template>
