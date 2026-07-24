<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { LayoutGrid, Plus, Sparkles } from '@lucide/vue';
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
import { destroy, index } from '@/routes/admin/academics/section';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { Section, SectionFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Sections',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    sections: LengthAwarePaginator<Section>;
    filters: SectionFilters;
    educationalLevels: Array<{ id: number; name: string }>;
}>();

const searchQuery = ref(props.filters.search ?? '');
const educationalLevelFilter = ref(
    props.filters.educational_level_id?.toString() ?? 'all',
);
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedSection = ref<Section | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchSections = (
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
            only: ['sections', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const handleSearch = (): void => {
    window.clearTimeout(searchTimer);

    searchTimer = window.setTimeout(() => {
        fetchSections();
    }, 300);
};

const openCreateDialog = (): void => {
    selectedSection.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (section: Section): void => {
    selectedSection.value = section;
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (section: Section): void => {
    selectedSection.value = section;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('Section deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the section. Please try again.');
};

watch([searchQuery, educationalLevelFilter], handleSearch);

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedSection.value = null;
    }
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Sections" />

    <DefaultContainer>
        <PageHero>
            <template #icon>
                <LayoutGrid class="size-5" aria-hidden="true" />
            </template>
            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Class organization
            </template>
            <template #title>Section workspace</template>
            <template #description>
                Create and organize class sections for each educational level.
            </template>
            <template #actions>
                <Button type="button" @click="openCreateDialog">
                    <Plus aria-hidden="true" />
                    Create section
                </Button>
            </template>
        </PageHero>

        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="sections.total"
                item-label="Section"
                search-placeholder="Search name..."
                search-label="Search sections"
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
                    <DataTablePageSizeSelect
                        :model-value="sections.per_page"
                        @update:model-value="fetchSections"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="props.sections.data"
                empty-message="No sections found."
            />

            <template #footer>
                <DataTablePagination
                    :from="sections.from"
                    :to="sections.to"
                    :total="sections.total"
                    :links="sections.links"
                    :previous-page-url="sections.prev_page_url"
                    :next-page-url="sections.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :section="selectedSection"
        :educational-levels="educationalLevels"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedSection">
            <Form
                v-bind="destroy.form(selectedSection.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete section?</DialogTitle>
                    <DialogDescription>
                        {{ selectedSection.name }} will be permanently deleted.
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
                        {{ processing ? 'Deleting...' : 'Delete section' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
