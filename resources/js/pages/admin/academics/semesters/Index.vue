<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import {
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
import { destroy, index } from '@/routes/admin/academics/semester';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { Semester, SemesterFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Semester',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    semesters: LengthAwarePaginator<Semester>;
    filters: SemesterFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);

// Currently selected semester for edit or delete actions.
const selectedSemester = ref<Semester | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

// Reload the semester list using the current filters.
const fetchSemester = (
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
            only: ['semesters', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

// Debounce search requests while the user is typing.
const handleSearch = (): void => {
    window.clearTimeout(searchTimer);

    searchTimer = window.setTimeout(() => {
        fetchSemester();
    }, 300);
};

// Open the dialog in create mode.
const openCreateDialog = (): void => {
    selectedSemester.value = null;
    isFormDialogOpen.value = true;
};

// Open the dialog in edit mode.
const openEditDialog = (semester: Semester): void => {
    selectedSemester.value = semester;
    isFormDialogOpen.value = true;
};

// Open the delete confirmation dialog.
const openDeleteDialog = (semester: Semester): void => {
    selectedSemester.value = semester;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

// Show a success message after deleting a semester.
const handleDeleted = (): void => {
    toast.success('Semester deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the semester. Please try again.');
};

// Refresh the table when the search query changes.
watch(searchQuery, handleSearch);

// Clear the selected semester after all dialogs are closed.
watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedSemester.value = null;
    }
});

// Prevent pending search requests when leaving the page.
onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Semester" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Semesters"
            description="Manage academic periods, dates, and statuses."
            :count="semesters.total"
            item-label="Semester"
            search-placeholder="Search name, code, or status..."
            search-label="Search semester"
        >
            <!-- Table filters -->
            <template #filters>
                <label
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <span class="sr-only">Rows per page</span>
                    <select
                        :value="semesters.per_page"
                        class="h-9 rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        aria-label="Rows per page"
                        @change="
                            fetchSemester(
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

            <!-- Table actions -->
            <template #actions>
                <Button type="button" size="sm" @click="openCreateDialog">
                    <Plus aria-hidden="true" />
                    Create
                </Button>
            </template>
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <!-- Semester list -->
            <DataTable
                :columns="columns"
                :data="props.semesters.data"
                empty-message="No Semesters found."
            />

            <!-- Pagination -->
            <DataTablePagination
                :from="semesters.from"
                :to="semesters.to"
                :total="semesters.total"
                :links="semesters.links"
                :previous-page-url="semesters.prev_page_url"
                :next-page-url="semesters.next_page_url"
            />
        </div>
    </div>

    <!-- Create / Edit semester dialog -->
    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :semester="selectedSemester"
    />

    <!-- Delete confirmation dialog -->
    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedSemester">
            <Form
                v-bind="destroy.form(selectedSemester.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete semester?</DialogTitle>
                    <DialogDescription>
                        {{ selectedSemester.name }} ({{
                            selectedSemester.code
                        }}) will be permanently deleted.
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
                        {{ processing ? 'Deleting...' : 'Delete semester' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
