<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus, Sparkles, Tags } from '@lucide/vue';
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
import { destroy, index } from '@/routes/central/categories';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { Category, CategoryFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Categories', href: index() }],
    },
});

const props = defineProps<{
    categories: LengthAwarePaginator<Category>;
    filters: CategoryFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedCategory = ref<Category | null>(null);
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchCategories = (
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
            only: ['categories', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const openCreateDialog = (): void => {
    selectedCategory.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (category: Category): void => {
    selectedCategory.value = category;
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (category: Category): void => {
    selectedCategory.value = category;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => fetchCategories(), 300);
});

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedCategory.value = null;
    }
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Categories" />

    <PageHero>
        <template #icon>
            <Tags class="size-5" aria-hidden="true" />
        </template>
        <template #badge>
            <Sparkles class="size-3.5" aria-hidden="true" />
            School organization
        </template>
        <template #title> Categories </template>
        <template #description>
            Organize schools into reusable categories for the public welcome
            page and central administration.
        </template>
        <template #actions>
            <Button type="button" @click="openCreateDialog">
                <Plus class="size-4" aria-hidden="true" />
                Create category
            </Button>
        </template>
    </PageHero>

    <DefaultContainer>
        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="categories.total"
                item-label="category"
                search-placeholder="Search categories..."
                search-label="Search categories"
                class="border-b px-4 py-4 sm:px-5"
            >
                <template #filters>
                    <DataTablePageSizeSelect
                        :model-value="categories.per_page"
                        @update:model-value="fetchCategories"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="categories.data"
                empty-message="No categories found."
            />

            <template #footer>
                <DataTablePagination
                    :from="categories.from"
                    :to="categories.to"
                    :total="categories.total"
                    :links="categories.links"
                    :previous-page-url="categories.prev_page_url"
                    :next-page-url="categories.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :category="selectedCategory"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedCategory">
            <Form
                :action="destroy(selectedCategory.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="
                    () => {
                        toast.success('Category deleted successfully.');
                        isDeleteDialogOpen = false;
                    }
                "
                @error="
                    (errors) =>
                        toast.error(
                            errors.category ?? 'Unable to delete the category.',
                        )
                "
            >
                <DialogHeader>
                    <DialogTitle>Delete category?</DialogTitle>
                    <DialogDescription>
                        <strong>{{ selectedCategory.name }}</strong> will be
                        permanently deleted. Categories assigned to schools
                        cannot be deleted.
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
                        {{ processing ? 'Deleting...' : 'Delete category' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
