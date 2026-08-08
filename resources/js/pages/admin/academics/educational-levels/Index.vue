<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus, School, Sparkles } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import DefaultContainer from '@/components/ui/containers/DefaultContainer.vue';

import DataTableContainer from '@/components/DataTableContainer.vue';
import PageHero from '@/components/PageHero.vue';
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
import { destroy, index } from '@/routes/admin/academics/educational-level';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { EducationalLevel, EducationalLevelFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Educational Levels', href: index() }],
    },
});

const props = defineProps<{
    educationalLevels: LengthAwarePaginator<EducationalLevel>;
    filters: EducationalLevelFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedEducationalLevel = ref<EducationalLevel | null>(null);
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchEducationalLevels = (
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
            only: ['educationalLevels', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const openCreateDialog = (): void => {
    selectedEducationalLevel.value = null;
    isFormDialogOpen.value = true;
};

const columns = createColumns({
    edit: (educationalLevel) => {
        selectedEducationalLevel.value = educationalLevel;
        isFormDialogOpen.value = true;
    },
    delete: (educationalLevel) => {
        selectedEducationalLevel.value = educationalLevel;
        isDeleteDialogOpen.value = true;
    },
});

const handleDeleteError = (errors: Record<string, string>): void => {
    toast.error(
        errors.educational_level ?? 'Unable to delete the educational level.',
    );
};

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(fetchEducationalLevels, 300);
});
watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedEducationalLevel.value = null;
    }
});
onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Educational Levels" />

    <PageHero>
        <template #icon>
            <School class="size-5" aria-hidden="true" />
        </template>
        <template #badge>
            <Sparkles class="size-3.5" aria-hidden="true" />
            Academic foundations
        </template>
        <template #title>Educational level workspace</template>
        <template #description>
            Define the learning stages used to organize programs, grade
            levels, subjects, and curriculum structures.
        </template>
        <template #actions>
            <Button type="button" @click="openCreateDialog">
                <Plus aria-hidden="true" />
                Create educational level
            </Button>
        </template>
    </PageHero>

    <DefaultContainer>
        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="educationalLevels.total"
                item-label="Educational Level"
                search-placeholder="Search name..."
                search-label="Search educational levels"
                class="border-b px-4 py-4 sm:px-5"
            >
                <template #filters>
                    <DataTablePageSizeSelect
                        :model-value="educationalLevels.per_page"
                        @update:model-value="fetchEducationalLevels"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="educationalLevels.data"
                empty-message="No educational levels found."
            />

            <template #footer>
                <DataTablePagination
                    :from="educationalLevels.from"
                    :to="educationalLevels.to"
                    :total="educationalLevels.total"
                    :links="educationalLevels.links"
                    :previous-page-url="educationalLevels.prev_page_url"
                    :next-page-url="educationalLevels.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :educational-level="selectedEducationalLevel"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedEducationalLevel">
            <Form
                v-bind="destroy.form(selectedEducationalLevel.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="
                    toast.success('Educational level deleted successfully.');
                    isDeleteDialogOpen = false;
                "
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete educational level?</DialogTitle>
                    <DialogDescription>
                        {{ selectedEducationalLevel.name }} will be permanently
                        deleted. Levels currently in use cannot be deleted.
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
                        {{
                            processing
                                ? 'Deleting...'
                                : 'Delete educational level'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
