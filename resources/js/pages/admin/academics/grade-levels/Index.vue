<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Layers3, Plus, Sparkles } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import DataTableContainer from '@/components/DataTableContainer.vue';
import PageHero from '@/components/PageHero.vue';
import { Button } from '@/components/ui/button';
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
import { destroy, index } from '@/routes/admin/academics/grade-level';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { GradeLevel, GradeLevelFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Grade Levels',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    gradeLevels: LengthAwarePaginator<GradeLevel>;
    filters: GradeLevelFilters;
    educationalLevels: Array<{
        id: number;
        name: string;
    }>;
}>();

const searchQuery = ref(props.filters.search ?? '');

const educationalLevelFilter = ref(
    props.filters.educational_level_id?.toString() ?? 'all',
);

const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);

const selectedGradeLevel = ref<GradeLevel | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchGradeLevels = (
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
                per_page: perPage,
            },
        }),
        {
            only: ['gradeLevels', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const handleSearch = (): void => {
    window.clearTimeout(searchTimer);

    searchTimer = window.setTimeout(() => {
        fetchGradeLevels();
    }, 300);
};

const handleEducationalLevelFilter = (): void => {
    window.clearTimeout(searchTimer);
    fetchGradeLevels();
};

const handlePageSizeChange = (perPage: string | number): void => {
    fetchGradeLevels(perPage);
};

const openCreateDialog = (): void => {
    selectedGradeLevel.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (gradeLevel: GradeLevel): void => {
    selectedGradeLevel.value = gradeLevel;
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (gradeLevel: GradeLevel): void => {
    selectedGradeLevel.value = gradeLevel;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('Grade level deleted successfully.');

    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the grade level. Please try again.');
};

watch(searchQuery, handleSearch);

watch(educationalLevelFilter, handleEducationalLevelFilter);

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedGradeLevel.value = null;
    }
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Grade Levels" />

    <DefaultContainer>
        <PageHero>
            <template #icon>
                <Layers3 class="size-5" aria-hidden="true" />
            </template>

            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Academic planning
            </template>

            <template #title> Grade level workspace </template>

            <template #description>
                Create and organize the grade levels available under each
                educational level.
            </template>

            <template #actions>
                <Button type="button" @click="openCreateDialog">
                    <Plus class="size-4" aria-hidden="true" />
                    Create grade level
                </Button>
            </template>
        </PageHero>

        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="gradeLevels.total"
                item-label="grade level"
                search-placeholder="Search grade levels..."
                search-label="Search grade levels"
                class="border-b px-4 py-4 sm:px-5"
            >
                <template #filters>
                    <Select v-model="educationalLevelFilter">
                        <SelectTrigger
                            class="w-full sm:w-56"
                            aria-label="Filter by educational level"
                        >
                            <SelectValue placeholder="Educational level" />
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

                    <DataTablePageSizeSelect
                        :model-value="gradeLevels.per_page"
                        @update:model-value="handlePageSizeChange"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="gradeLevels.data"
                empty-message="No grade levels found."
            />

            <template #footer>
                <DataTablePagination
                    :from="gradeLevels.from"
                    :to="gradeLevels.to"
                    :total="gradeLevels.total"
                    :links="gradeLevels.links"
                    :previous-page-url="gradeLevels.prev_page_url"
                    :next-page-url="gradeLevels.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :grade-level="selectedGradeLevel"
        :educational-levels="educationalLevels"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedGradeLevel">
            <Form
                v-bind="destroy.form(selectedGradeLevel.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{
                    preserveScroll: true,
                }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete grade level?</DialogTitle>

                    <DialogDescription>
                        <strong>{{ selectedGradeLevel.name }}</strong>
                        will be permanently deleted. This action cannot be
                        undone.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="processing"
                        >
                            Cancel
                        </Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Deleting...' : 'Delete grade level' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
