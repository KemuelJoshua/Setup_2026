<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import {
    CalendarRange,
    ChevronRight,
    CirclePlus,
    Pencil,
    Plus,
    Trash2,
    Network
} from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import DefaultContainer from '@/components/ui/containers/DefaultContainer.vue';

import {
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

import PeriodFormDialog from './PeriodFormDialog.vue';
import StructureFormDialog from './StructureFormDialog.vue';
import type {
    AcademicPeriod,
    AcademicTermStructure,
    AcademicTermStructureFilters,
    EducationalLevelOption,
} from './types';

import PageHero from '@/components/PageHero.vue';

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

    <PageHero>
        <template #icon>
            <Network class="size-5" aria-hidden="true" />
        </template>
        <template #badge>
            <Sparkles class="size-3.5" aria-hidden="true" />
            Academic planning
        </template>
        <template #title>Academic Term Structures workspace</template>
        <template #description>
            Manage flexible semesters, quarters, terms, and grading periods.
        </template>
        <template #actions>
            <Button type="button" @click="openStructureForm()">
                <Plus aria-hidden="true" />
                Add structure
            </Button>
        </template>
    </PageHero>
    
    <DefaultContainer>
        <DataTableToolbar
            v-model="searchQuery"
            :count="academicTermStructures.total"
            item-label="Structure"
            search-placeholder="Search name or code..."
            search-label="Search academic term structures"
            class="p-4 bg-card mb-3 border rounded-lg shadow-sm"
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

        <div
            v-if="academicTermStructures.data.length"
            class="grid gap-5 xl:grid-cols-2"
        >
            <section
                v-for="structure in academicTermStructures.data"
                :key="structure.id"
                class="flex min-w-0 flex-col overflow-hidden rounded-xl border bg-card shadow-sm"
            >
                <header
                    class="flex flex-col gap-4 border-b bg-muted/20 p-5 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div class="flex min-w-0 items-start gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <CalendarRange class="size-5" aria-hidden="true" />
                        </span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate font-semibold">
                                    {{ structure.name }}
                                </h2>
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                    :class="
                                        structure.status === 'active'
                                            ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                            : 'bg-muted text-muted-foreground'
                                    "
                                >
                                    {{ structure.status }}
                                </span>
                                <span
                                    class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary capitalize"
                                >
                                    {{ structure.type }}
                                </span>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                {{ structure.code }}
                                <template v-if="structure.educational_level">
                                    · {{ structure.educational_level.name }}
                                </template>
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 gap-1">
                        <Button
                            type="button"
                            size="icon"
                            variant="ghost"
                            :aria-label="`Edit ${structure.name}`"
                            @click="openStructureForm(structure)"
                        >
                            <Pencil aria-hidden="true" />
                        </Button>
                        <Button
                            type="button"
                            size="icon"
                            variant="ghost"
                            class="text-muted-foreground hover:text-destructive"
                            :aria-label="`Delete ${structure.name}`"
                            @click="openStructureDelete(structure)"
                        >
                            <Trash2 aria-hidden="true" />
                        </Button>
                    </div>
                </header>

                <div class="flex flex-1 flex-col gap-4 p-5">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-sm font-medium">Academic periods</h3>
                        <Button
                            type="button"
                            size="sm"
                            variant="outline"
                            @click="openPeriodForm(structure)"
                        >
                            <CirclePlus aria-hidden="true" />
                            Add root period
                        </Button>
                    </div>

                    <div
                        v-if="structure.root_periods.length"
                        class="flex flex-col gap-3"
                    >
                        <article
                            v-for="rootPeriod in structure.root_periods"
                            :key="rootPeriod.id"
                            class="rounded-lg border bg-background"
                        >
                            <div
                                class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="min-w-0">
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <span class="font-medium">
                                            {{ rootPeriod.name }}
                                        </span>
                                        <span
                                            v-if="
                                                rootPeriod.status === 'inactive'
                                            "
                                            class="text-xs text-muted-foreground"
                                        >
                                            Inactive
                                        </span>
                                    </div>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        Sequence {{ rootPeriod.sequence
                                        }}<template v-if="rootPeriod.code">
                                            · {{ rootPeriod.code }}</template
                                        >
                                    </p>
                                </div>

                                <div class="flex shrink-0 gap-1">
                                    <Button
                                        v-if="
                                            structure.type !== 'quarterly' &&
                                            rootPeriod.children.length < 4
                                        "
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        @click="
                                            openPeriodForm(
                                                structure,
                                                null,
                                                rootPeriod,
                                            )
                                        "
                                    >
                                        <Plus aria-hidden="true" />
                                        Add grading period
                                    </Button>
                                    <Button
                                        type="button"
                                        size="icon"
                                        variant="ghost"
                                        :aria-label="`Edit ${rootPeriod.name}`"
                                        @click="
                                            openPeriodForm(
                                                structure,
                                                rootPeriod,
                                            )
                                        "
                                    >
                                        <Pencil aria-hidden="true" />
                                    </Button>
                                    <Button
                                        type="button"
                                        size="icon"
                                        variant="ghost"
                                        class="text-muted-foreground hover:text-destructive"
                                        :aria-label="`Delete ${rootPeriod.name}`"
                                        @click="
                                            openPeriodDelete(
                                                structure,
                                                rootPeriod,
                                            )
                                        "
                                    >
                                        <Trash2 aria-hidden="true" />
                                    </Button>
                                </div>
                            </div>

                            <div
                                v-if="rootPeriod.children.length"
                                class="flex flex-col border-t bg-muted/10"
                            >
                                <div
                                    v-for="child in rootPeriod.children"
                                    :key="child.id"
                                    class="flex items-center justify-between gap-3 border-b px-4 py-3 last:border-b-0"
                                >
                                    <div
                                        class="flex min-w-0 items-center gap-2 pl-2"
                                    >
                                        <ChevronRight
                                            class="size-4 shrink-0 text-muted-foreground"
                                            aria-hidden="true"
                                        />
                                        <span class="truncate text-sm">
                                            {{ child.name }}
                                        </span>
                                        <span
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ child.sequence }}
                                        </span>
                                        <span
                                            v-if="child.status === 'inactive'"
                                            class="text-xs text-muted-foreground"
                                        >
                                            Inactive
                                        </span>
                                    </div>
                                    <div class="flex shrink-0 gap-1">
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            :aria-label="`Edit ${child.name}`"
                                            @click="
                                                openPeriodForm(
                                                    structure,
                                                    child,
                                                    rootPeriod,
                                                )
                                            "
                                        >
                                            <Pencil aria-hidden="true" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            class="text-muted-foreground hover:text-destructive"
                                            :aria-label="`Delete ${child.name}`"
                                            @click="
                                                openPeriodDelete(
                                                    structure,
                                                    child,
                                                )
                                            "
                                        >
                                            <Trash2 aria-hidden="true" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <p
                        v-else
                        class="rounded-lg border border-dashed px-4 py-8 text-center text-sm text-muted-foreground"
                    >
                        No academic periods configured.
                    </p>
                </div>
            </section>
        </div>

        <p
            v-else
            class="rounded-xl border border-dashed px-5 py-16 text-center text-sm text-muted-foreground"
        >
            No academic term structures found.
        </p>

        <DataTablePagination
            :from="academicTermStructures.from"
            :to="academicTermStructures.to"
            :total="academicTermStructures.total"
            :links="academicTermStructures.links"
            :previous-page-url="academicTermStructures.prev_page_url"
            :next-page-url="academicTermStructures.next_page_url"
            class="mt-4 bg-card border rounded-xl p-2 md:p-4"
        />
    </DefaultContainer>

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
