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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    create,
    destroy,
    edit,
    index,
    updateStatus,
} from '@/routes/admin/academics/curriculum';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type {
    Curriculum,
    CurriculumFilters,
    ProgramOption,
    SelectOption,
} from './columns';

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
    educationalLevels: SelectOption[];
    programs: ProgramOption[];
}>();

const searchQuery = ref(props.filters.search ?? '');
const educationalLevelFilter = ref(
    props.filters.educational_level_id?.toString() ?? 'all',
);
const programFilter = ref(props.filters.program_id?.toString() ?? 'all');
const statusFilter = ref(props.filters.status ?? 'all');
const isDeleteDialogOpen = ref(false);
const isStatusDialogOpen = ref(false);
const selectedCurriculum = ref<Curriculum | null>(null);
const selectedStatus = ref('');
const filteredPrograms = computed(() => {
    if (educationalLevelFilter.value === 'all') {
        return props.programs;
    }

    return props.programs.filter(
        (program) =>
            program.educational_level_id ===
            Number(educationalLevelFilter.value),
    );
});
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
                educational_level_id:
                    educationalLevelFilter.value === 'all'
                        ? undefined
                        : educationalLevelFilter.value,
                program_id:
                    programFilter.value === 'all'
                        ? undefined
                        : programFilter.value,
                status:
                    statusFilter.value === 'all'
                        ? undefined
                        : statusFilter.value,
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

const openStatusDialog = (curriculum: Curriculum): void => {
    selectedCurriculum.value = curriculum;
    selectedStatus.value = curriculum.status;
    isStatusDialogOpen.value = true;
};

const columns = createColumns({
    manage: openEditPage,
    changeStatus: openStatusDialog,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('Curriculum deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the curriculum. Please try again.');
};

const handleStatusUpdated = (): void => {
    toast.success('Curriculum status updated successfully.');
    isStatusDialogOpen.value = false;
};

watch(educationalLevelFilter, () => {
    const selectedProgramIsAvailable = filteredPrograms.value.some(
        (program) => String(program.id) === programFilter.value,
    );

    if (!selectedProgramIsAvailable) {
        programFilter.value = 'all';
    }
});

watch(
    [searchQuery, educationalLevelFilter, programFilter, statusFilter],
    handleSearch,
);

watch([isDeleteDialogOpen, isStatusDialogOpen], ([deleteOpen, statusOpen]) => {
    if (!deleteOpen && !statusOpen) {
        selectedCurriculum.value = null;
        selectedStatus.value = '';
    }
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Curricula" />

    <DefaultContainer class="flex flex-1 flex-col gap-5 p-4 md:p-8">
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
                    <Select v-model="educationalLevelFilter">
                        <SelectTrigger aria-label="Filter by educational level">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">
                                All educational levels
                            </SelectItem>
                            <SelectItem
                                v-for="level in educationalLevels"
                                :key="level.id"
                                :value="String(level.id)"
                            >
                                {{ level.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="programFilter">
                        <SelectTrigger aria-label="Filter by program">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All programs</SelectItem>
                            <SelectItem
                                v-for="program in filteredPrograms"
                                :key="program.id"
                                :value="String(program.id)"
                            >
                                {{ program.code }} — {{ program.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="statusFilter">
                        <SelectTrigger aria-label="Filter by status">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All statuses</SelectItem>
                            <SelectItem value="Draft">Draft</SelectItem>
                            <SelectItem value="Active">Active</SelectItem>
                            <SelectItem value="Inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
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

    <Dialog v-model:open="isStatusDialogOpen">
        <DialogContent v-if="selectedCurriculum">
            <Form
                v-bind="updateStatus.form(selectedCurriculum.id)"
                v-slot="{ processing, errors }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleStatusUpdated"
                @error="
                    toast.error(
                        'Unable to update the curriculum status. Please try again.',
                    )
                "
            >
                <DialogHeader>
                    <DialogTitle>Change curriculum status</DialogTitle>
                    <DialogDescription>
                        Update the status of {{ selectedCurriculum.name }}.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Select v-model="selectedStatus" name="status">
                        <SelectTrigger class="w-full" aria-label="Curriculum status">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Draft">Draft</SelectItem>
                            <SelectItem value="Active">Active</SelectItem>
                            <SelectItem value="Inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="errors.status" class="text-sm text-destructive">
                        {{ errors.status }}
                    </p>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        :disabled="
                            processing ||
                            selectedStatus === selectedCurriculum.status
                        "
                    >
                        {{ processing ? 'Updating...' : 'Update status' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
