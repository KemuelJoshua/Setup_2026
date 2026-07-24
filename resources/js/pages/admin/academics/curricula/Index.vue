<script setup lang="ts">
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import { Button } from '@/components/ui/button';
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
    edit: openEditPage,
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

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Curricula"
            description="Manage curricula and their subjects."
            :count="curricula.total"
            item-label="Curriculum"
            search-placeholder="Search code, name, year, or status..."
            search-label="Search curricula"
        >
            <template #filters>
                <DataTablePageSizeSelect
                    :model-value="curricula.per_page"
                    @update:model-value="fetchCurricula"
                />
            </template>

            <template #actions>
                <Button as-child size="sm">
                    <Link :href="create()">
                        <Plus aria-hidden="true" />
                        Create
                    </Link>
                </Button>
            </template>
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <DataTable
                :columns="columns"
                :data="props.curricula.data"
                empty-message="No curricula found."
            />

            <DataTablePagination
                :from="curricula.from"
                :to="curricula.to"
                :total="curricula.total"
                :links="curricula.links"
                :previous-page-url="curricula.prev_page_url"
                :next-page-url="curricula.next_page_url"
            />
        </div>
    </div>

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
