<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Building2,
    GraduationCap,
    MapPin,
    Moon,
    Search,
    School,
    Sparkles,
    Sun,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';

import { useAppearance } from '@/composables/useAppearance';

type SchoolDirectoryItem = {
    id: string;
    code: string;
    name: string;
    address: string | null;
    motto: string | null;
    url: string;
};

const props = defineProps<{
    schools: SchoolDirectoryItem[];
}>();

const page = usePage();
const searchQuery = ref('');
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

const filteredSchools = computed(() => {
    const query = searchQuery.value.trim().toLocaleLowerCase();

    if (!query) {
        return props.schools;
    }

    return props.schools.filter((school) =>
        [school.name, school.code, school.address]
            .filter(Boolean)
            .some((value) => value?.toLocaleLowerCase().includes(query)),
    );
});

const schoolInitials = (name: string): string =>
    name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word.charAt(0))
        .join('')
        .toLocaleUpperCase();
</script>

<template>
    <Head title="Find your school" />

    <div
        class="relative min-h-svh overflow-hidden bg-background text-foreground"
    >
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-[34rem] bg-[radial-gradient(circle_at_50%_-10%,var(--primary-glow),transparent_58%)] opacity-70"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute top-52 -left-20 size-72 rounded-full border border-primary/10"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute top-64 -left-8 size-44 rounded-full border border-primary/10"
            aria-hidden="true"
        />

        <header
            class="relative z-20 border-b border-border/70 bg-background/80 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-4 px-5 sm:px-8 lg:px-10"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-[var(--shadow-primary)] shadow-md"
                    >
                        <GraduationCap class="size-5" :stroke-width="1.8" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold sm:text-base">
                            {{ page.props.name }}
                        </p>
                        <p class="truncate text-[11px] text-muted-foreground">
                            Student learning portal
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="flex size-10 cursor-pointer items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-accent hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                    :aria-label="themeLabel"
                    :title="themeLabel"
                    @click="updateAppearance(nextAppearance)"
                >
                    <component
                        :is="themeIcon"
                        class="size-5"
                        :stroke-width="1.8"
                    />
                </button>
            </div>
        </header>

        <main class="relative z-10">
            <section
                class="mx-auto max-w-7xl px-5 pt-14 pb-8 sm:px-8 sm:pt-20 lg:px-10"
            >
                <div class="mx-auto max-w-3xl text-center">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-[var(--primary-soft)] px-3 py-1.5 text-xs font-semibold text-primary"
                    >
                        <Sparkles class="size-3.5" />
                        Your classroom is one step away
                    </div>

                    <h1
                        class="mt-6 text-4xl leading-[1.08] font-semibold tracking-[-0.04em] sm:text-5xl lg:text-6xl"
                    >
                        Find your school.
                        <span class="text-primary">Start learning.</span>
                    </h1>

                    <p
                        class="mx-auto mt-5 max-w-2xl text-base leading-7 text-muted-foreground sm:text-lg"
                    >
                        Search for your school below to open its learning portal
                        and sign in to your classes.
                    </p>

                    <div class="relative mx-auto mt-9 max-w-2xl text-left">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <input
                            v-model="searchQuery"
                            type="search"
                            name="school-search"
                            autocomplete="off"
                            placeholder="Search by school name, code, or location"
                            aria-label="Search schools"
                            class="h-14 w-full rounded-2xl border border-border bg-card pr-12 pl-12 text-sm shadow-[0_20px_50px_-28px_var(--shadow-color)] transition placeholder:text-muted-foreground focus:border-primary/50 focus:ring-4 focus:ring-primary/10 focus:outline-none sm:text-base"
                        />
                        <button
                            v-if="searchQuery"
                            type="button"
                            class="absolute top-1/2 right-3 flex size-9 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            aria-label="Clear school search"
                            @click="searchQuery = ''"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                </div>
            </section>

            <section
                class="mx-auto max-w-7xl px-5 pt-6 pb-20 sm:px-8 lg:px-10 lg:pb-28"
            >
                <div class="mb-5 flex items-end justify-between gap-4">
                    <div>
                        <p
                            class="text-xs font-semibold tracking-[0.16em] text-primary uppercase"
                        >
                            School directory
                        </p>
                        <h2
                            class="mt-1 text-xl font-semibold tracking-tight sm:text-2xl"
                        >
                            Choose your campus
                        </h2>
                    </div>
                    <p
                        class="shrink-0 text-xs text-muted-foreground sm:text-sm"
                    >
                        {{ filteredSchools.length }}
                        {{
                            filteredSchools.length === 1 ? 'school' : 'schools'
                        }}
                    </p>
                </div>

                <div
                    v-if="filteredSchools.length"
                    class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                >
                    <a
                        v-for="school in filteredSchools"
                        :key="school.id"
                        :href="school.url"
                        class="group relative flex min-h-60 flex-col overflow-hidden rounded-2xl border border-border bg-card p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-primary/30 hover:shadow-[0_24px_55px_-30px_var(--shadow-color)] focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none sm:p-6"
                    >
                        <div
                            class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 bg-primary transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100"
                            aria-hidden="true"
                        />

                        <div class="flex items-start justify-between gap-4">
                            <span
                                class="flex size-13 shrink-0 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-sm font-bold tracking-wide text-primary ring-1 ring-primary/10"
                                aria-hidden="true"
                            >
                                {{ schoolInitials(school.name) }}
                            </span>
                            <span
                                class="rounded-full bg-muted px-2.5 py-1 text-[10px] font-semibold tracking-wider text-muted-foreground uppercase"
                            >
                                {{ school.code }}
                            </span>
                        </div>

                        <div class="mt-5 flex-1">
                            <h3
                                class="text-lg leading-snug font-semibold tracking-tight sm:text-xl"
                            >
                                {{ school.name }}
                            </h3>
                            <p
                                v-if="school.motto"
                                class="mt-2 line-clamp-2 text-sm text-muted-foreground italic"
                            >
                                “{{ school.motto }}”
                            </p>
                            <p
                                v-if="school.address"
                                class="mt-3 flex items-start gap-2 text-xs leading-5 text-muted-foreground"
                            >
                                <MapPin
                                    class="mt-0.5 size-3.5 shrink-0 text-primary"
                                />
                                <span class="line-clamp-2">{{
                                    school.address
                                }}</span>
                            </p>
                        </div>

                        <div
                            class="mt-6 flex items-center justify-between border-t border-border/70 pt-4 text-sm font-semibold text-primary"
                        >
                            Open school portal
                            <ArrowUpRight
                                class="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                            />
                        </div>
                    </a>
                </div>

                <div
                    v-else
                    class="rounded-2xl border border-dashed border-border bg-card/70 px-6 py-14 text-center"
                >
                    <span
                        class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-muted text-muted-foreground"
                    >
                        <School class="size-6" />
                    </span>
                    <h3 class="mt-4 font-semibold">
                        {{
                            schools.length
                                ? 'No matching school found'
                                : 'No schools available yet'
                        }}
                    </h3>
                    <p
                        class="mx-auto mt-2 max-w-md text-sm leading-6 text-muted-foreground"
                    >
                        {{
                            schools.length
                                ? 'Check the spelling or try searching with your school code or location.'
                                : 'Please check back later or contact your school administrator for help.'
                        }}
                    </p>
                    <button
                        v-if="schools.length"
                        type="button"
                        class="mt-5 cursor-pointer text-sm font-semibold text-primary hover:underline"
                        @click="searchQuery = ''"
                    >
                        View all schools
                    </button>
                </div>
            </section>
        </main>

        <footer class="relative z-10 border-t border-border/70 bg-card/60">
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-5 py-6 text-center text-xs text-muted-foreground sm:flex-row sm:px-8 sm:text-left lg:px-10"
            >
                <span class="flex items-center gap-2">
                    <Building2 class="size-4 text-primary" />
                    {{ page.props.name }} · Learning Management System
                </span>
                <span>Need help? Contact your school administrator.</span>
            </div>
        </footer>
    </div>
</template>
