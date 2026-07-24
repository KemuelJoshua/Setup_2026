<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
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

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Educational Levels"
            description="Manage educational levels used across academics."
            :count="educationalLevels.total"
            item-label="Educational Level"
            search-placeholder="Search name..."
            search-label="Search educational levels"
        >
            <template #filters>
                <DataTablePageSizeSelect
                    :model-value="educationalLevels.per_page"
                    @update:model-value="fetchEducationalLevels"
                />
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
                :data="educationalLevels.data"
                empty-message="No educational levels found."
            />
            <DataTablePagination
                :from="educationalLevels.from"
                :to="educationalLevels.to"
                :total="educationalLevels.total"
                :links="educationalLevels.links"
                :previous-page-url="educationalLevels.prev_page_url"
                :next-page-url="educationalLevels.next_page_url"
            />
        </div>
    </div>

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
