<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { CalendarRange, Plus, Sparkles } from '@lucide/vue';
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
import { destroy, index } from '@/routes/admin/school-years';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { SchoolYear, SchoolYearFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'School Years',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    schoolYears: LengthAwarePaginator<SchoolYear>;
    filters: SchoolYearFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedSchoolYear = ref<SchoolYear | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchSchoolYears = (
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
            only: ['schoolYears', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const handleSearch = (): void => {
    window.clearTimeout(searchTimer);

    searchTimer = window.setTimeout(() => {
        fetchSchoolYears();
    }, 300);
};

const handlePageSizeChange = (perPage: string | number): void => {
    fetchSchoolYears(perPage);
};

const openCreateDialog = (): void => {
    selectedSchoolYear.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (schoolYear: SchoolYear): void => {
    selectedSchoolYear.value = schoolYear;
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (schoolYear: SchoolYear): void => {
    selectedSchoolYear.value = schoolYear;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('School year deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the school year. Please try again.');
};

watch(searchQuery, handleSearch);

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedSchoolYear.value = null;
    }
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="School Years" />

    <DefaultContainer>
        <PageHero>
            <template #icon>
                <CalendarRange class="size-5" aria-hidden="true" />
            </template>

            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Academic calendar
            </template>

            <template #title> School year workspace </template>

            <template #description>
                Create and manage the academic periods used throughout the
                school calendar.
            </template>

            <template #actions>
                <Button type="button" @click="openCreateDialog">
                    <Plus class="size-4" aria-hidden="true" />
                    Create school year
                </Button>
            </template>
        </PageHero>

        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="schoolYears.total"
                item-label="school year"
                search-placeholder="Search name, code, or status..."
                search-label="Search school years"
                class="border-b px-4 py-4 sm:px-5"
            >
                <template #filters>
                    <DataTablePageSizeSelect
                        :model-value="schoolYears.per_page"
                        @update:model-value="handlePageSizeChange"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="schoolYears.data"
                empty-message="No school years found."
            />

            <template #footer>
                <DataTablePagination
                    :from="schoolYears.from"
                    :to="schoolYears.to"
                    :total="schoolYears.total"
                    :links="schoolYears.links"
                    :previous-page-url="schoolYears.prev_page_url"
                    :next-page-url="schoolYears.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :school-year="selectedSchoolYear"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedSchoolYear">
            <Form
                v-bind="destroy.form(selectedSchoolYear.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete school year?</DialogTitle>

                    <DialogDescription>
                        <strong>{{ selectedSchoolYear.sc_name }}</strong>
                        ({{ selectedSchoolYear.sc_code }}) will be permanently
                        deleted. This action cannot be undone.
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
                        {{ processing ? 'Deleting...' : 'Delete school year' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
