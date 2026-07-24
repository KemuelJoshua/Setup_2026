<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { GraduationCap, Plus, Sparkles } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
    educationalLevels: Array<{ id: number; name: string }>;
}>();

const searchQuery = ref(props.filters.search ?? '');
const educationalLevelFilter = ref(
    props.filters.educational_level_id?.toString() ?? 'all',
);
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
                educational_level_id:
                    educationalLevelFilter.value === 'all'
                        ? undefined
                        : educationalLevelFilter.value,
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

watch([searchQuery, educationalLevelFilter], handleSearch);

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
        <PageHero>
            <template #icon>
                <GraduationCap class="size-5" aria-hidden="true" />
            </template>
            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Academic planning
            </template>
            <template #title>Program workspace</template>
            <template #description>
                Manage academic programs and connect each one to the correct
                educational level.
            </template>
            <template #actions>
                <Button type="button" @click="openCreateDialog">
                    <Plus aria-hidden="true" />
                    Create program
                </Button>
            </template>
        </PageHero>

        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="programs.total"
                item-label="Program"
                search-placeholder="Search code, name, or status..."
                search-label="Search programs"
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
                        :model-value="programs.per_page"
                        @update:model-value="fetchPrograms"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="props.programs.data"
                empty-message="No programs found."
            />

            <template #footer>
                <DataTablePagination
                    :from="programs.from"
                    :to="programs.to"
                    :total="programs.total"
                    :links="programs.links"
                    :previous-page-url="programs.prev_page_url"
                    :next-page-url="programs.next_page_url"
                />
            </template>
        </DataTableContainer>
    </div>

    <CreateUpdate
        v-model:open="isFormDialogOpen"
        :program="selectedProgram"
        :educational-levels="educationalLevels"
    />

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
