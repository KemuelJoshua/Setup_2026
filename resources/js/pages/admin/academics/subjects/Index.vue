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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { destroy, index } from '@/routes/admin/academics/subject';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { Subject, SubjectFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Subjects',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    subjects: LengthAwarePaginator<Subject>;
    filters: SubjectFilters;
    educationalLevels: Array<{ id: number; name: string }>;
}>();

const searchQuery = ref(props.filters.search ?? '');
const educationalLevelFilter = ref(
    props.filters.educational_level_id?.toString() ?? 'all',
);
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedSubject = ref<Subject | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchSubjects = (
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
            only: ['subjects', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const handleSearch = (): void => {
    window.clearTimeout(searchTimer);

    searchTimer = window.setTimeout(() => {
        fetchSubjects();
    }, 300);
};

const openCreateDialog = (): void => {
    selectedSubject.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (subject: Subject): void => {
    selectedSubject.value = subject;
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (subject: Subject): void => {
    selectedSubject.value = subject;
    isDeleteDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditDialog,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('Subject deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the subject. Please try again.');
};

watch([searchQuery, educationalLevelFilter], handleSearch);

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedSubject.value = null;
    }
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
});
</script>

<template>
    <Head title="Subjects" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Subjects"
            description="Manage subjects."
            :count="subjects.total"
            item-label="Subject"
            search-placeholder="Search name..."
            search-label="Search subjects"
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
                    :model-value="subjects.per_page"
                    @update:model-value="fetchSubjects"
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
                :data="props.subjects.data"
                empty-message="No subjects found."
            />

            <DataTablePagination
                :from="subjects.from"
                :to="subjects.to"
                :total="subjects.total"
                :links="subjects.links"
                :previous-page-url="subjects.prev_page_url"
                :next-page-url="subjects.next_page_url"
            />
        </div>
    </div>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :subject="selectedSubject"
        :educational-levels="educationalLevels"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedSubject">
            <Form
                v-bind="destroy.form(selectedSubject.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete subject?</DialogTitle>
                    <DialogDescription>
                        {{ selectedSubject.name }} will be permanently deleted.
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
                        {{ processing ? 'Deleting...' : 'Delete subject' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
