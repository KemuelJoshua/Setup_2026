<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import type { SchoolYear } from '@/modules/academics/school-year/components/columns';
import SchoolYearDeleteDialog from '@/modules/academics/school-year/components/SchoolYearDeleteDialog.vue';
import SchoolYearFormDialog from '@/modules/academics/school-year/components/SchoolYearFormDialog.vue';
import SchoolYearTable from '@/modules/academics/school-year/components/SchoolYearTable.vue';
import { index } from '@/routes/admin/school-years';
import type { LengthAwarePaginator } from '@/types';

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
    filters: {
        search?: string;
        per_page?: string | number;
    };
}>();

const searchQuery = ref(props.filters.search ?? '');
const isCreateDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedSchoolYear = ref<SchoolYear | null>(null);
const schoolYearPendingDeletion = ref<SchoolYear | null>(null);
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchSchoolYears = (perPage = props.schoolYears.per_page): void => {
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

const openEditDialog = (schoolYear: SchoolYear): void => {
    selectedSchoolYear.value = schoolYear;
    isEditDialogOpen.value = true;
};

const openDeleteDialog = (schoolYear: SchoolYear): void => {
    schoolYearPendingDeletion.value = schoolYear;
    isDeleteDialogOpen.value = true;
};

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => fetchSchoolYears(), 300);
});

watch(isEditDialogOpen, (isOpen) => {
    if (!isOpen) {
        selectedSchoolYear.value = null;
    }
});

watch(isDeleteDialogOpen, (isOpen) => {
    if (!isOpen) {
        schoolYearPendingDeletion.value = null;
    }
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="School Years" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            :count="schoolYears.total"
            item-label="school year"
            search-placeholder="Search name, code, or status..."
            search-label="Search school years"
        >
            <template #filters>
                <label
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <span class="sr-only">Rows per page</span>
                    <select
                        :value="schoolYears.per_page"
                        class="h-9 rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        aria-label="Rows per page"
                        @change="
                            fetchSchoolYears(
                                Number(
                                    ($event.target as HTMLSelectElement).value,
                                ),
                            )
                        "
                    >
                        <option
                            v-for="size in [10, 15, 25, 50]"
                            :key="size"
                            :value="size"
                        >
                            {{ size }} rows
                        </option>
                    </select>
                </label>
            </template>

            <template #actions>
                <SchoolYearFormDialog
                    v-model:open="isCreateDialogOpen"
                    mode="create"
                >
                    <template #trigger>
                        <Button type="button" size="sm">
                            <Plus aria-hidden="true" />
                            Create
                        </Button>
                    </template>
                </SchoolYearFormDialog>
            </template>
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <SchoolYearTable
                :school-years="schoolYears.data"
                @edit="openEditDialog"
                @delete="openDeleteDialog"
            />

            <DataTablePagination
                :from="schoolYears.from"
                :to="schoolYears.to"
                :total="schoolYears.total"
                :links="schoolYears.links"
                :previous-page-url="schoolYears.prev_page_url"
                :next-page-url="schoolYears.next_page_url"
            />
        </div>
    </div>

    <SchoolYearFormDialog
        v-model:open="isEditDialogOpen"
        mode="edit"
        :school-year="selectedSchoolYear"
    />

    <SchoolYearDeleteDialog
        v-model:open="isDeleteDialogOpen"
        :school-year="schoolYearPendingDeletion"
    />
</template>
