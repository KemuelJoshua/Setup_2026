<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
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
import { destroy, index } from '@/routes/admin/academics/academic-term';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { AcademicTerm, AcademicTermFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Academic Terms',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    academicTerms: LengthAwarePaginator<AcademicTerm>;
    filters: AcademicTermFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);

const selectedAcademicTerm = ref<AcademicTerm | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchAcademicTerms = (
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
            only: ['academicTerms', 'filters'],
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
        fetchAcademicTerms();
    }, 300);
};

// Open the dialog in create mode.
const openCreateDialog = (): void => {
    selectedAcademicTerm.value = null;
    isFormDialogOpen.value = true;
};

// Open the dialog in edit mode.
const openEditDialog = (academicTerm: AcademicTerm): void => {
    selectedAcademicTerm.value = academicTerm;
    isFormDialogOpen.value = true;
};

// Open the delete confirmation dialog.
const openDeleteDialog = (academicTerm: AcademicTerm): void => {
    selectedAcademicTerm.value = academicTerm;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('Academic term deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (errors: Record<string, string>): void => {
    toast.error(
        errors.academic_term ??
            'Unable to delete the academic term. Please try again.',
    );
};

// Refresh the table when the search query changes.
watch(searchQuery, handleSearch);

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedAcademicTerm.value = null;
    }
});

// Prevent pending search requests when leaving the page.
onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Academic Terms" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Academic Terms"
            description="Manage terms and their grading periods in one place."
            :count="academicTerms.total"
            item-label="Academic Term"
            search-placeholder="Search name, code, or type..."
            search-label="Search academic terms"
        >
            <!-- Table filters -->
            <template #filters>
                <label
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <span class="sr-only">Rows per page</span>
                    <select
                        :value="academicTerms.per_page"
                        class="h-9 rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        aria-label="Rows per page"
                        @change="
                            fetchAcademicTerms(
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
                <Button type="button" size="sm" @click="openCreateDialog()">
                    <Plus aria-hidden="true" />
                    Add term
                </Button>
            </template>
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <DataTable
                :columns="columns"
                :data="props.academicTerms.data"
                empty-message="No academic terms found."
            />

            <!-- Pagination -->
            <DataTablePagination
                :from="academicTerms.from"
                :to="academicTerms.to"
                :total="academicTerms.total"
                :links="academicTerms.links"
                :previous-page-url="academicTerms.prev_page_url"
                :next-page-url="academicTerms.next_page_url"
            />
        </div>
    </div>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :academic-term="selectedAcademicTerm"
    />

    <!-- Delete confirmation dialog -->
    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedAcademicTerm">
            <Form
                v-bind="destroy.form(selectedAcademicTerm.id)"
                v-slot="{ errors, processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete academic term?</DialogTitle>
                    <DialogDescription>
                        {{ selectedAcademicTerm.name }} ({{
                            selectedAcademicTerm.code
                        }}) will be permanently deleted.
                    </DialogDescription>
                </DialogHeader>

                <InputError :message="errors.academic_term" />

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{
                            processing ? 'Deleting...' : 'Delete academic term'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
