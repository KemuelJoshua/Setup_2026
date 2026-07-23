<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import { Button } from '@/components/ui/button';
import {
    DataTable,
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
}>();

const searchQuery = ref(props.filters.search ?? '');
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

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Grade Levels"
            description="Manage grade levels."
            :count="gradeLevels.total"
            item-label="Grade Level"
            search-placeholder="Search name..."
            search-label="Search grade levels"
        >
            <template #filters>
                <label
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <span class="sr-only">Rows per page</span>
                    <select
                        :value="gradeLevels.per_page"
                        class="h-9 rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        aria-label="Rows per page"
                        @change="
                            fetchGradeLevels(
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
                <Button type="button" size="sm" @click="openCreateDialog">
                    <Plus aria-hidden="true" />
                    Create
                </Button>
            </template>
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <DataTable
                :columns="columns"
                :data="props.gradeLevels.data"
                empty-message="No grade levels found."
            />

            <DataTablePagination
                :from="gradeLevels.from"
                :to="gradeLevels.to"
                :total="gradeLevels.total"
                :links="gradeLevels.links"
                :previous-page-url="gradeLevels.prev_page_url"
                :next-page-url="gradeLevels.next_page_url"
            />
        </div>
    </div>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :grade-level="selectedGradeLevel"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedGradeLevel">
            <Form
                v-bind="destroy.form(selectedGradeLevel.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete grade level?</DialogTitle>
                    <DialogDescription>
                        {{ selectedGradeLevel.name }} will be permanently
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
                        {{ processing ? 'Deleting...' : 'Delete grade level' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
