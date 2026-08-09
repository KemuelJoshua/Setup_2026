<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ArrowDown, ChevronDown } from '@lucide/vue';
import { ref } from 'vue';

type SchoolDirectoryItem = {
    id: string;
    categoryId: number | null;
    code: string;
    name: string;
    address: string | null;
    motto: string | null;
    url: string;
};

type SchoolCategory = {
    id: number;
    name: string;
    schools: SchoolDirectoryItem[];
};

defineProps<{
    categories: SchoolCategory[];
    schools: SchoolDirectoryItem[];
}>();

type DropdownId = number | 'all';

const openDropdown = ref<DropdownId | null>(null);

const handleDropdownToggle = (dropdownId: DropdownId, event: Event): void => {
    const dropdown = event.currentTarget as HTMLDetailsElement;

    if (dropdown.open) {
        openDropdown.value = dropdownId;
    } else if (openDropdown.value === dropdownId) {
        openDropdown.value = null;
    }
};
</script>

<template>
    <Head title="DepEd Makati Learning Management System" />

    <div class="min-h-svh bg-slate-50 text-slate-950">
        <header
            class="relative z-50 border-t-4 border-amber-400 bg-primary text-white shadow-lg"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-3 lg:flex-row lg:items-center lg:justify-between lg:gap-8 lg:px-10 lg:py-0"
            >
                <a href="#top" class="flex shrink-0 items-center gap-3 lg:h-18">
                    <img
                        src="/img/makati-deped.png"
                        alt="DepEd Makati logo"
                        class="size-11 rounded-full bg-white object-contain ring-2 ring-white/20"
                    />
                    <div>
                        <p class="text-base font-bold tracking-tight">
                            DepEd Makati LMS
                        </p>
                        <p
                            class="text-[10px] tracking-[0.14em] text-blue-100 uppercase"
                        >
                            Schools Division Office
                        </p>
                    </div>
                </a>

                <nav
                    aria-label="School categories"
                    class="flex flex-wrap gap-1 pb-1 lg:h-18 lg:flex-nowrap lg:items-stretch lg:pb-0"
                >
                    <details
                        v-for="category in categories"
                        :key="category.id"
                        :open="openDropdown === category.id"
                        class="group relative shrink-0 lg:flex"
                        @toggle="handleDropdownToggle(category.id, $event)"
                    >
                        <summary
                            class="flex h-11 cursor-pointer list-none items-center gap-1.5 rounded-lg px-3 text-xs font-semibold text-blue-50 transition hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-amber-300 focus-visible:outline-none lg:h-full lg:rounded-none lg:px-3.5"
                        >
                            {{ category.name }}
                            <ChevronDown
                                class="size-3.5 transition-transform group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="relative z-50 mt-1 w-72 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 text-slate-800 shadow-2xl lg:absolute lg:top-full lg:left-0 lg:mt-0"
                        >
                            <a
                                v-for="school in category.schools"
                                :key="school.id"
                                :href="school.url"
                                class="flex items-center justify-between gap-4 px-4 py-3 text-sm transition hover:bg-blue-50 hover:text-primary focus:bg-blue-50 focus:outline-none"
                            >
                                <span class="line-clamp-2 font-medium">{{
                                    school.name
                                }}</span>
                                <span
                                    class="shrink-0 text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    {{ school.code }}
                                </span>
                            </a>
                            <p
                                v-if="category.schools.length === 0"
                                class="px-4 py-3 text-sm text-muted-foreground"
                            >
                                No tenants in this category yet.
                            </p>
                        </div>
                    </details>

                    <details
                        :open="openDropdown === 'all'"
                        class="group relative shrink-0 lg:flex"
                        @toggle="handleDropdownToggle('all', $event)"
                    >
                        <summary
                            class="flex h-11 cursor-pointer list-none items-center gap-1.5 rounded-lg px-3 text-xs font-semibold text-blue-50 transition hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-amber-300 focus-visible:outline-none lg:h-full lg:rounded-none lg:px-3.5"
                        >
                            All Schools
                            <ChevronDown
                                class="size-3.5 transition-transform group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="relative z-50 mt-1 max-h-96 w-72 overflow-y-auto rounded-xl border border-slate-200 bg-white py-2 text-slate-800 shadow-2xl lg:absolute lg:top-full lg:right-0 lg:mt-0"
                        >
                            <a
                                v-for="school in schools"
                                :key="school.id"
                                :href="school.url"
                                class="flex items-center justify-between gap-4 px-4 py-3 text-sm transition hover:bg-blue-50 hover:text-primary focus:bg-blue-50 focus:outline-none"
                            >
                                <span class="line-clamp-2 font-medium">{{
                                    school.name
                                }}</span>
                                <span
                                    class="shrink-0 text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    {{ school.code }}
                                </span>
                            </a>
                            <p
                                v-if="schools.length === 0"
                                class="px-4 py-3 text-sm text-slate-500"
                            >
                                No schools available yet.
                            </p>
                        </div>
                    </details>
                </nav>
            </div>
        </header>

        <main>
            <section
                id="top"
                class="relative isolate flex min-h-[calc(100svh-8.75rem)] items-center justify-center overflow-hidden bg-primary px-5 py-16 text-white sm:min-h-[calc(100svh-8rem)] lg:min-h-[calc(100svh-4.75rem)] lg:px-10"
            >
                <img
                    src="/img/background_optimized.jpg"
                    alt=""
                    class="absolute inset-0 -z-20 size-full object-cover object-center"
                />
                <div
                    class="absolute inset-0 -z-10 bg-linear-to-b from-primary/18 to-primary/32"
                    aria-hidden="true"
                />

                <div
                    class="w-full max-w-4xl rounded-3xl border border-white/25 bg-primary/60 px-6 py-9 text-center shadow-2xl backdrop-blur-md sm:px-12 sm:py-12"
                >
                    <div
                        class="flex items-center justify-center gap-5 sm:gap-8"
                    >
                        <img
                            src="/img/makati-deped.png"
                            alt="Department of Education Makati logo"
                            class="size-24 rounded-full bg-white object-contain shadow-xl ring-4 ring-white/25 sm:size-32"
                        />
                        <span
                            class="h-20 w-px bg-white/35 sm:h-28"
                            aria-hidden="true"
                        />
                        <img
                            src="/img/makati-logo.png"
                            alt="City of Makati logo"
                            class="size-24 rounded-full bg-white object-contain shadow-xl ring-4 ring-white/25 sm:size-32"
                        />
                    </div>

                    <p
                        class="mt-8 text-xs font-bold tracking-[0.3em] text-amber-300 uppercase sm:text-sm"
                    >
                        Department of Education · Makati City
                    </p>
                    <h1
                        class="mx-auto mt-3 max-w-3xl text-4xl leading-tight font-bold tracking-tight text-balance sm:text-5xl lg:text-6xl"
                    >
                        Learning without limits
                    </h1>
                    <p
                        class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-blue-50 sm:text-lg"
                    >
                        Your gateway to Makati City's public school learning
                        communities—where every learner can connect, grow, and
                        succeed.
                    </p>
                </div>
            </section>
        </main>

        <footer class="bg-primary px-5 py-6 text-center text-xs text-blue-100">
            Department of Education – Schools Division Office of Makati City ·
            Learning Management System
        </footer>
    </div>
</template>
