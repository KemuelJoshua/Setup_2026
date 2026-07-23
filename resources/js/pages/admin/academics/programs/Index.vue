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
import { destroy, index } from '@/routes/admin/academics/program';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { Program, ProgramFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Programs',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    programs: LengthAwarePaginator<Program>;
    filters: ProgramFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedProgram = ref<Program | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchPrograms = (
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
            only: ['programs', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const handleSearch = (): void => {
    window.clearTimeout(searchTimer);

    searchTimer = window.setTimeout(() => {
        fetchPrograms();
    }, 300);
};

const openCreateDialog = (): void => {
    selectedProgram.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (program: Program): void => {
    selectedProgram.value = program;
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (program: Program): void => {
    selectedProgram.value = program;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('Program deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the program. Please try again.');
};

watch(searchQuery, handleSearch);

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedProgram.value = null;
    }
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Programs" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Programs"
            description="Manage academic programs."
            :count="programs.total"
            item-label="Program"
            search-placeholder="Search code, name, or status..."
            search-label="Search programs"
        >
            <template #filters>
                <label
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <span class="sr-only">Rows per page</span>
                    <select
                        :value="programs.per_page"
                        class="h-9 rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        aria-label="Rows per page"
                        @change="
                            fetchPrograms(
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
                :data="props.programs.data"
                empty-message="No programs found."
            />

            <DataTablePagination
                :from="programs.from"
                :to="programs.to"
                :total="programs.total"
                :links="programs.links"
                :previous-page-url="programs.prev_page_url"
                :next-page-url="programs.next_page_url"
            />
        </div>
    </div>

    <CreateUpdate v-model:open="isFormDialogOpen" :program="selectedProgram" />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedProgram">
            <Form
                v-bind="destroy.form(selectedProgram.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete program?</DialogTitle>
                    <DialogDescription>
                        {{ selectedProgram.name }} ({{ selectedProgram.code }})
                        will be permanently deleted.
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
                        {{ processing ? 'Deleting...' : 'Delete program' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
