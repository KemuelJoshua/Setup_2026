<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { CalendarRange, Plus, Sparkles } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import DataTableContainer from '@/components/DataTableContainer.vue';
import InputError from '@/components/InputError.vue';
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
import { destroy as destroyPeriod } from '@/routes/admin/academics/academic-periods';
import {
    destroy as destroyStructure,
    index,
} from '@/routes/admin/academics/academic-term-structures';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import PeriodFormDialog from './PeriodFormDialog.vue';
import StructureFormDialog from './StructureFormDialog.vue';
import type {
    AcademicPeriod,
    AcademicTermStructure,
    AcademicTermStructureFilters,
    EducationalLevelOption,
} from './types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Academic Term Structures',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    academicTermStructures: LengthAwarePaginator<AcademicTermStructure>;
    filters: AcademicTermStructureFilters;
    educationalLevels: EducationalLevelOption[];
}>();

const searchQuery = ref(props.filters.search ?? '');
const educationalLevelFilter = ref(
    props.filters.educational_level_id?.toString() ?? 'all',
);
const structureDialogOpen = ref(false);
const periodDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedStructure = ref<AcademicTermStructure | null>(null);
const selectedPeriod = ref<AcademicPeriod | null>(null);
const selectedParent = ref<AcademicPeriod | null>(null);
const deleteTarget = ref<'structure' | 'period'>('structure');
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchStructures = (
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
            only: ['academicTermStructures', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const openStructureForm = (
    structure: AcademicTermStructure | null = null,
): void => {
    selectedStructure.value = structure;
    structureDialogOpen.value = true;
};

const openPeriodForm = (
    structure: AcademicTermStructure,
    period: AcademicPeriod | null = null,
    parent: AcademicPeriod | null = null,
): void => {
    selectedStructure.value = structure;
    selectedPeriod.value = period;
    selectedParent.value = parent;
    periodDialogOpen.value = true;
};

const openStructureDelete = (structure: AcademicTermStructure): void => {
    selectedStructure.value = structure;
    selectedPeriod.value = null;
    deleteTarget.value = 'structure';
    deleteDialogOpen.value = true;
};

const openPeriodDelete = (
    structure: AcademicTermStructure,
    period: AcademicPeriod,
): void => {
    selectedStructure.value = structure;
    selectedPeriod.value = period;
    deleteTarget.value = 'period';
    deleteDialogOpen.value = true;
};

const columns = createColumns({
    addPeriod: (structure, parent) =>
        openPeriodForm(structure, null, parent ?? null),
    editPeriod: (structure, period, parent) =>
        openPeriodForm(structure, period, parent ?? null),
    deletePeriod: openPeriodDelete,
    editStructure: openStructureForm,
    deleteStructure: openStructureDelete,
});

const clearSelection = (): void => {
    selectedStructure.value = null;
    selectedPeriod.value = null;
    selectedParent.value = null;
};

watch([searchQuery, educationalLevelFilter], () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(fetchStructures, 300);
});

watch(
    [structureDialogOpen, periodDialogOpen, deleteDialogOpen],
    ([structureOpen, periodOpen, deleteOpen]) => {
        if (!structureOpen && !periodOpen && !deleteOpen) {
            clearSelection();
        }
    },
);

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Academic Term Structures" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <PageHero>
            <template #icon>
                <CalendarRange class="size-5" aria-hidden="true" />
            </template>
            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Academic calendar
            </template>
            <template #title>Academic structure workspace</template>
            <template #description>
                Design reusable semester, trimester, and quarterly structures
                with their grading periods.
            </template>
            <template #actions>
                <Button type="button" @click="openStructureForm()">
                    <Plus aria-hidden="true" />
                    Add structure
                </Button>
            </template>
        </PageHero>

        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="academicTermStructures.total"
                item-label="Structure"
                search-placeholder="Search name or code..."
                search-label="Search academic term structures"
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
                        :model-value="academicTermStructures.per_page"
                        @update:model-value="fetchStructures"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="academicTermStructures.data"
                empty-message="No academic term structures found."
            />

            <template #footer>
                <DataTablePagination
                    :from="academicTermStructures.from"
                    :to="academicTermStructures.to"
                    :total="academicTermStructures.total"
                    :links="academicTermStructures.links"
                    :previous-page-url="academicTermStructures.prev_page_url"
                    :next-page-url="academicTermStructures.next_page_url"
                />
            </template>
        </DataTableContainer>
    </div>

    <StructureFormDialog
        v-model:open="structureDialogOpen"
        :structure="selectedStructure"
        :educational-levels="educationalLevels"
    />
    <PeriodFormDialog
        v-model:open="periodDialogOpen"
        :structure="selectedStructure"
        :period="selectedPeriod"
        :parent="selectedParent"
    />

    <Dialog v-model:open="deleteDialogOpen">
        <DialogContent v-if="selectedStructure">
            <Form
                v-bind="
                    deleteTarget === 'structure'
                        ? destroyStructure.form(selectedStructure.id)
                        : destroyPeriod.form(selectedPeriod!.id)
                "
                v-slot="{ errors, processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="
                    deleteDialogOpen = false;
                    toast.success(
                        deleteTarget === 'structure'
                            ? 'Academic term structure deleted.'
                            : 'Academic period deleted.',
                    );
                "
                @error="toast.error('Unable to delete this record.')"
            >
                <DialogHeader>
                    <DialogTitle>
                        Delete
                        {{
                            deleteTarget === 'structure'
                                ? 'academic term structure'
                                : 'academic period'
                        }}?
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            deleteTarget === 'structure'
                                ? `${selectedStructure.name} and all of its periods will be permanently deleted.`
                                : `${selectedPeriod?.name} and any child periods will be permanently deleted.`
                        }}
                    </DialogDescription>
                </DialogHeader>

                <InputError
                    :message="
                        errors.academic_term_structure ?? errors.academic_period
                    "
                />

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Deleting...' : 'Delete' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
