<script setup lang="ts">
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { BookOpen, Layers3, Plus, Sparkles } from '@lucide/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import DataTableContainer from '@/components/DataTableContainer.vue';
import PageHero from '@/components/PageHero.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import DefaultContainer from '@/components/ui/containers/DefaultContainer.vue';

import {
    DataTable,
    DataTablePageSizeSelect,
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    create,
    destroy,
    edit,
    index,
} from '@/routes/admin/academics/curriculum';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { Curriculum, CurriculumFilters } from './columns';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Curricula',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    curricula: LengthAwarePaginator<Curriculum>;
    filters: CurriculumFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isDeleteDialogOpen = ref(false);
const selectedCurriculum = ref<Curriculum | null>(null);
const visibleSubjectCount = computed(() =>
    props.curricula.data.reduce(
        (total, curriculum) => total + curriculum.curriculum_subjects_count,
        0,
    ),
);
const visibleActiveCount = computed(
    () =>
        props.curricula.data.filter(
            (curriculum) => curriculum.status === 'Active',
        ).length,
);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchCurricula = (
    perPage: string | number | undefined = props.filters.per_page,
): void => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
                per_page: perPage,
            },
        }),
        {
            only: ['curricula', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const handleSearch = (): void => {
    window.clearTimeout(searchTimer);

    searchTimer = window.setTimeout(() => {
        fetchCurricula();
    }, 300);
};

const openEditPage = (curriculum: Curriculum): void => {
    router.visit(edit(curriculum.id));
};

const openDeleteDialog = (curriculum: Curriculum): void => {
    selectedCurriculum.value = curriculum;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    manage: openEditPage,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('Curriculum deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the curriculum. Please try again.');
};

watch(searchQuery, handleSearch);

watch(isDeleteDialogOpen, (open) => {
    if (!open) {
        selectedCurriculum.value = null;
    }
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Curricula" />

    <DefaultContainer
        class="flex flex-1 flex-col gap-5 p-4 md:p-8"
    >
        <PageHero>
            <template #icon>
                <Layers3 class="size-5" aria-hidden="true" />
            </template>
            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Academic planning
            </template>
            <template #title>Curriculum workspace</template>
            <template #description>
                Build, organize, and maintain every program's subjects from one
                clear workspace.
            </template>
            <template #actions>
                <Button as-child>
                    <Link :href="create()">
                        <Plus aria-hidden="true" />
                        New curriculum
                    </Link>
                </Button>
            </template>
        </PageHero>

        <div class="grid gap-3 sm:grid-cols-3">
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    >
                        <Layers3 class="size-4" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ curricula.total }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Total curricula
                        </p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                    >
                        <Sparkles class="size-4" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ visibleActiveCount }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Active on this page
                        </p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400"
                    >
                        <BookOpen class="size-4" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ visibleSubjectCount }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Subjects on this page
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="curricula.total"
                item-label="Curriculum"
                search-placeholder="Search code, name, year, or status..."
                search-label="Search curricula"
                class="border-b px-4 py-4 sm:px-5"
            >
                <template #filters>
                    <DataTablePageSizeSelect
                        :model-value="curricula.per_page"
                        @update:model-value="fetchCurricula"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="props.curricula.data"
                empty-message="No curricula match your search."
                class="rounded-none border-0"
            />

            <template #footer>
                <DataTablePagination
                    :from="curricula.from"
                    :to="curricula.to"
                    :total="curricula.total"
                    :links="curricula.links"
                    :previous-page-url="curricula.prev_page_url"
                    :next-page-url="curricula.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedCurriculum">
            <Form
                v-bind="destroy.form(selectedCurriculum.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete curriculum?</DialogTitle>
                    <DialogDescription>
                        {{ selectedCurriculum.name }} ({{
                            selectedCurriculum.code
                        }}) and its subject assignments will be permanently
                        deleted.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Deleting...' : 'Delete curriculum' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
