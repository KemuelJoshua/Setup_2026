<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    Award,
    BarChart3,
    Bell,
    BookMarked,
    BookOpenCheck,
    Building2,
    CalendarDays,
    CheckCircle2,
    Clock3,
    FileText,
    GraduationCap,
    LibraryBig,
    Moon,
    Play,
    School,
    ShieldCheck,
    Sparkles,
    Sun,
    UsersRound,
} from '@lucide/vue';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { dashboard, login } from '@/routes';

const page = usePage();
const { resolvedAppearance, updateAppearance } = useAppearance();

const nextAppearance = computed(() =>
    resolvedAppearance.value === 'dark' ? 'light' : 'dark',
);

const themeIcon = computed(() =>
    resolvedAppearance.value === 'dark' ? Sun : Moon,
);

const themeLabel = computed(() =>
    resolvedAppearance.value === 'dark'
        ? 'Switch to light mode'
        : 'Switch to dark mode',
);
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-svh overflow-hidden bg-background text-foreground">
        <header
            class="relative z-30 border-b border-border/70 bg-background/90 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-5 px-5 sm:px-8 lg:px-10"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-dashed border-border bg-card text-primary shadow-sm"
                        title="School logo"
                    >
                        <Building2 class="size-6" :stroke-width="1.6" />
                        <span class="sr-only">School logo placeholder</span>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold sm:text-base">
                            {{ page.props.name }}
                        </p>
                        <p class="truncate text-[11px] text-muted-foreground">
                            Learning Management System
                        </p>
                    </div>
                </div>

                <nav class="flex shrink-0 items-center gap-1.5 sm:gap-2.5">
                    <button
                        type="button"
                        class="group flex size-10 cursor-pointer items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-accent hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        :aria-label="themeLabel"
                        :title="themeLabel"
                        @click="updateAppearance(nextAppearance)"
                    >
                        <component
                            :is="themeIcon"
                            class="size-5 opacity-80 transition-all group-hover:rotate-6 group-hover:opacity-100"
                            :stroke-width="1.8"
                        />
                    </button>

                    <Link
                        :href="$page.props.auth.user ? dashboard() : login()"
                        class="inline-flex h-10 shrink-0 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-semibold text-primary-foreground shadow-[var(--shadow-primary)] shadow-md transition-all hover:-translate-y-0.5 hover:bg-[var(--primary-hover)] sm:px-5"
                    >
                        {{
                            $page.props.auth.user
                                ? 'Dashboard'
                                : 'Student login'
                        }}
                        <ArrowRight class="hidden size-4 sm:block" />
                    </Link>
                </nav>
            </div>
        </header>

        <main>
            <section class="relative border-b border-border/70">
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_78%_18%,var(--primary-glow),transparent_28%)] opacity-55"
                />
                <GraduationCap
                    class="pointer-events-none absolute top-16 left-[2%] size-36 -rotate-12 text-foreground/[0.025]"
                    :stroke-width="0.8"
                    aria-hidden="true"
                />
                <BookMarked
                    class="pointer-events-none absolute right-[2%] bottom-10 size-28 rotate-12 text-foreground/[0.025]"
                    :stroke-width="0.8"
                    aria-hidden="true"
                />

                <div
                    class="relative mx-auto grid max-w-7xl items-center gap-14 px-5 py-16 sm:px-8 sm:py-20 lg:grid-cols-[0.9fr_1.1fr] lg:px-10 lg:py-24"
                >
                    <div class="max-w-2xl">
                        <div
                            class="bg-primary-soft mb-6 inline-flex items-center gap-2 rounded-full border border-primary/15 px-3 py-1.5 text-xs font-semibold text-primary"
                        >
                            <Sparkles class="size-3.5" />
                            Your digital campus, always open
                        </div>
                        <h1
                            class="text-4xl leading-[1.08] font-semibold tracking-[-0.035em] sm:text-5xl lg:text-6xl"
                        >
                            Learning that feels
                            <span class="text-primary">closer to school.</span>
                        </h1>
                        <p
                            class="mt-6 max-w-xl text-base leading-7 text-muted-foreground sm:text-lg sm:leading-8"
                        >
                            Access classes, lessons, assignments, and school
                            updates in one welcoming space built for students
                            and educators.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <Link
                                :href="
                                    $page.props.auth.user
                                        ? dashboard()
                                        : login()
                                "
                                class="inline-flex h-12 items-center gap-2 rounded-lg bg-primary px-5 text-sm font-semibold text-primary-foreground shadow-[var(--shadow-primary)] shadow-lg transition-all hover:-translate-y-0.5 hover:bg-[var(--primary-hover)]"
                            >
                                {{
                                    $page.props.auth.user
                                        ? 'Open my dashboard'
                                        : 'Enter the LMS'
                                }}
                                <ArrowRight class="size-4" />
                            </Link>
                            <a
                                href="#lms-features"
                                class="inline-flex h-12 items-center gap-2 rounded-lg border border-border bg-card px-5 text-sm font-semibold shadow-sm transition-colors hover:bg-accent"
                            >
                                <Play class="size-4 fill-current" />
                                Explore features
                            </a>
                        </div>
                        <div
                            class="mt-9 flex flex-wrap gap-x-6 gap-y-3 text-xs font-medium text-muted-foreground"
                        >
                            <span class="flex items-center gap-2">
                                <CheckCircle2 class="size-4 text-primary" />
                                Courses in one place
                            </span>
                            <span class="flex items-center gap-2">
                                <CheckCircle2 class="size-4 text-primary" />
                                Easy progress tracking
                            </span>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-2xl">
                        <div
                            class="absolute -inset-5 rounded-[2rem] bg-primary/8 blur-2xl"
                        />
                        <div
                            class="relative overflow-hidden rounded-2xl border border-border bg-card shadow-[0_30px_90px_-35px_var(--shadow-color)]"
                        >
                            <div
                                class="flex items-center justify-between border-b border-border bg-sidebar px-5 py-4"
                            >
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex size-9 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
                                    >
                                        <GraduationCap class="size-5" />
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold">
                                            Student dashboard
                                        </p>
                                        <p
                                            class="text-[10px] text-muted-foreground"
                                        >
                                            Welcome back, learner
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="flex size-8 items-center justify-center rounded-full border border-border bg-background text-muted-foreground"
                                >
                                    <Bell class="size-4" />
                                </span>
                            </div>

                            <div class="grid sm:grid-cols-[1fr_13rem]">
                                <div class="grid gap-5 p-5 sm:p-6">
                                    <div
                                        class="rounded-xl bg-primary p-5 text-primary-foreground shadow-[var(--shadow-primary)] shadow-md"
                                    >
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >
                                            <div>
                                                <p
                                                    class="text-[10px] font-semibold tracking-wider uppercase opacity-70"
                                                >
                                                    Continue learning
                                                </p>
                                                <h2
                                                    class="mt-2 text-lg font-semibold"
                                                >
                                                    Introduction to Biology
                                                </h2>
                                                <p
                                                    class="mt-1 text-xs opacity-75"
                                                >
                                                    Module 4 · Cell structure
                                                </p>
                                            </div>
                                            <BookOpenCheck
                                                class="size-8 opacity-80"
                                                :stroke-width="1.5"
                                            />
                                        </div>
                                        <div
                                            class="mt-5 h-1.5 overflow-hidden rounded-full bg-white/20"
                                        >
                                            <div
                                                class="h-full w-[68%] rounded-full bg-white"
                                            />
                                        </div>
                                        <p class="mt-2 text-[10px] opacity-70">
                                            68% complete
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-3">
                                        <div
                                            class="rounded-xl border border-border bg-background p-3"
                                        >
                                            <BookMarked
                                                class="size-4 text-primary"
                                            />
                                            <p
                                                class="mt-3 text-lg font-semibold"
                                            >
                                                6
                                            </p>
                                            <p
                                                class="text-[10px] text-muted-foreground"
                                            >
                                                Courses
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-xl border border-border bg-background p-3"
                                        >
                                            <FileText
                                                class="size-4 text-primary"
                                            />
                                            <p
                                                class="mt-3 text-lg font-semibold"
                                            >
                                                3
                                            </p>
                                            <p
                                                class="text-[10px] text-muted-foreground"
                                            >
                                                Due soon
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-xl border border-border bg-background p-3"
                                        >
                                            <Award
                                                class="size-4 text-primary"
                                            />
                                            <p
                                                class="mt-3 text-lg font-semibold"
                                            >
                                                92%
                                            </p>
                                            <p
                                                class="text-[10px] text-muted-foreground"
                                            >
                                                Average
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="border-t border-border bg-muted/40 p-5 sm:border-t-0 sm:border-l"
                                >
                                    <p
                                        class="text-[10px] font-semibold tracking-wider text-muted-foreground uppercase"
                                    >
                                        Upcoming
                                    </p>
                                    <div class="mt-4 grid gap-4">
                                        <div class="flex gap-3">
                                            <span
                                                class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-card text-primary shadow-sm"
                                            >
                                                <CalendarDays class="size-4" />
                                            </span>
                                            <div>
                                                <p class="text-xs font-medium">
                                                    Science quiz
                                                </p>
                                                <p
                                                    class="mt-0.5 text-[10px] text-muted-foreground"
                                                >
                                                    Tomorrow · 9:00 AM
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <span
                                                class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-card text-primary shadow-sm"
                                            >
                                                <Clock3 class="size-4" />
                                            </span>
                                            <div>
                                                <p class="text-xs font-medium">
                                                    History essay
                                                </p>
                                                <p
                                                    class="mt-0.5 text-[10px] text-muted-foreground"
                                                >
                                                    Friday · 5:00 PM
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-card py-12">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <p
                        class="text-center text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase"
                    >
                        School identity and partner logos
                    </p>
                    <div
                        class="mx-auto mt-7 grid max-w-4xl grid-cols-2 gap-3 sm:grid-cols-4"
                    >
                        <div
                            class="flex h-20 items-center justify-center gap-2 rounded-xl border border-dashed border-border bg-background text-muted-foreground"
                        >
                            <School class="size-5" />
                            <span class="text-xs font-medium">School logo</span>
                        </div>
                        <div
                            class="flex h-20 items-center justify-center gap-2 rounded-xl border border-dashed border-border bg-background text-muted-foreground"
                        >
                            <ShieldCheck class="size-5" />
                            <span class="text-xs font-medium"
                                >Division logo</span
                            >
                        </div>
                        <div
                            class="flex h-20 items-center justify-center gap-2 rounded-xl border border-dashed border-border bg-background text-muted-foreground"
                        >
                            <BookMarked class="size-5" />
                            <span class="text-xs font-medium"
                                >Program logo</span
                            >
                        </div>
                        <div
                            class="flex h-20 items-center justify-center gap-2 rounded-xl border border-dashed border-border bg-background text-muted-foreground"
                        >
                            <Award class="size-5" />
                            <span class="text-xs font-medium"
                                >Partner logo</span
                            >
                        </div>
                    </div>
                </div>
            </section>

            <section id="lms-features" class="border-y border-border/70 py-20">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <div class="mx-auto max-w-2xl text-center">
                        <p
                            class="text-xs font-semibold tracking-[0.18em] text-primary uppercase"
                        >
                            Made for the whole school
                        </p>
                        <h2
                            class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl"
                        >
                            One campus. One learning space.
                        </h2>
                        <p class="mt-4 leading-7 text-muted-foreground">
                            A familiar digital classroom where students stay on
                            track and teachers keep learning moving forward.
                        </p>
                    </div>

                    <div class="mt-12 grid gap-5 md:grid-cols-3">
                        <article
                            class="rounded-2xl border border-border bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg"
                        >
                            <div
                                class="bg-primary-soft flex size-11 items-center justify-center rounded-xl text-primary"
                            >
                                <LibraryBig class="size-5" />
                            </div>
                            <h3 class="mt-5 text-lg font-semibold">
                                Organized learning
                            </h3>
                            <p
                                class="mt-2 text-sm leading-6 text-muted-foreground"
                            >
                                Find modules, readings, videos, and assignments
                                neatly arranged for every subject.
                            </p>
                        </article>
                        <article
                            class="rounded-2xl border border-border bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg"
                        >
                            <div
                                class="bg-primary-soft flex size-11 items-center justify-center rounded-xl text-primary"
                            >
                                <BarChart3 class="size-5" />
                            </div>
                            <h3 class="mt-5 text-lg font-semibold">
                                Visible progress
                            </h3>
                            <p
                                class="mt-2 text-sm leading-6 text-muted-foreground"
                            >
                                Keep an eye on course completion, grades, and
                                upcoming requirements at a glance.
                            </p>
                        </article>
                        <article
                            class="rounded-2xl border border-border bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg"
                        >
                            <div
                                class="bg-primary-soft flex size-11 items-center justify-center rounded-xl text-primary"
                            >
                                <UsersRound class="size-5" />
                            </div>
                            <h3 class="mt-5 text-lg font-semibold">
                                Connected community
                            </h3>
                            <p
                                class="mt-2 text-sm leading-6 text-muted-foreground"
                            >
                                Receive school announcements and stay connected
                                with teachers and classmates.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="relative overflow-hidden bg-sidebar py-18">
                <GraduationCap
                    class="pointer-events-none absolute -right-8 -bottom-12 size-56 rotate-12 text-sidebar-foreground/[0.035]"
                    :stroke-width="0.8"
                    aria-hidden="true"
                />
                <div
                    class="relative mx-auto flex max-w-5xl flex-col items-center gap-7 px-5 text-center sm:px-8"
                >
                    <div
                        class="flex size-14 items-center justify-center rounded-2xl bg-sidebar-primary text-sidebar-primary-foreground shadow-[var(--shadow-primary)] shadow-lg"
                    >
                        <GraduationCap class="size-7" />
                    </div>
                    <div>
                        <h2
                            class="text-3xl font-semibold tracking-tight text-sidebar-foreground sm:text-4xl"
                        >
                            Ready for class?
                        </h2>
                        <p
                            class="mx-auto mt-3 max-w-xl leading-7 text-sidebar-foreground/60"
                        >
                            Sign in to continue your lessons, check your
                            progress, and see what is happening around campus.
                        </p>
                    </div>
                    <Link
                        :href="$page.props.auth.user ? dashboard() : login()"
                        class="inline-flex h-12 items-center gap-2 rounded-lg bg-sidebar-primary px-6 text-sm font-semibold text-sidebar-primary-foreground shadow-[var(--shadow-primary)] shadow-md transition-all hover:-translate-y-0.5"
                    >
                        {{
                            $page.props.auth.user
                                ? 'Continue learning'
                                : 'Sign in to your account'
                        }}
                        <ArrowRight class="size-4" />
                    </Link>
                </div>
            </section>
        </main>

        <footer class="border-t border-sidebar-border bg-sidebar">
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-5 py-6 text-xs text-sidebar-foreground/45 sm:flex-row sm:px-8 lg:px-10"
            >
                <p>© {{ new Date().getFullYear() }} {{ page.props.name }}</p>
                <p>
                    Powered by
                    <span class="font-semibold text-sidebar-foreground/70">
                        BlitzDev
                    </span>
                </p>
            </div>
        </footer>
    </div>
</template>
