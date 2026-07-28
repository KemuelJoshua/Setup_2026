<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2 } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import ProjectImpactDeleteDialog from '@/modules/project-impact/components/ProjectImpactDeleteDialog.vue';
import ProjectImpactFormSheet from '@/modules/project-impact/components/ProjectImpactFormSheet.vue';
import ProjectImpactImportDialog from '@/modules/project-impact/components/ProjectImpactImportDialog.vue';
import ProjectImpactViewDialog from '@/modules/project-impact/components/ProjectImpactViewDialog.vue';
import type { ProjectImpactRecord } from '@/modules/project-impact/types';
import { index } from '@/routes/admin/project-impact-tracking';
import type { LengthAwarePaginator } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Project Impact Tracking',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    records: LengthAwarePaginator<ProjectImpactRecord>;
    filters: {
        search?: string;
        source_sheet?: string;
        classification?: string;
        project_status?: string;
        per_page?: string | number;
    };
    options: {
        sourceSheets: string[];
        classifications: string[];
        statuses: string[];
    };
}>();

const searchQuery = ref(props.filters.search ?? '');
const sourceSheetFilter = ref(props.filters.source_sheet ?? '');
const classificationFilter = ref(props.filters.classification ?? '');
const statusFilter = ref(props.filters.project_status ?? '');
const isCreateOpen = ref(false);
const isViewOpen = ref(false);
const isEditOpen = ref(false);
const isDeleteOpen = ref(false);
const selectedRecord = ref<ProjectImpactRecord | null>(null);
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchRecords = (): void => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
                source_sheet: sourceSheetFilter.value || undefined,
                classification: classificationFilter.value || undefined,
                project_status: statusFilter.value || undefined,
                per_page: props.records.per_page,
            },
        }),
        {
            only: ['records', 'filters', 'options'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};
const openDialog = (
    record: ProjectImpactRecord,
    dialog: 'view' | 'edit' | 'delete',
): void => {
    selectedRecord.value = record;
    isViewOpen.value = dialog === 'view';
    isEditOpen.value = dialog === 'edit';
    isDeleteOpen.value = dialog === 'delete';
};
const formatAmount = (amount: string | null): string =>
    amount
        ? new Intl.NumberFormat('en-PH', {
              style: 'currency',
              currency: 'PHP',
          }).format(Number(amount))
        : '—';

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(fetchRecords, 300);
});
watch([sourceSheetFilter, classificationFilter, statusFilter], fetchRecords);
watch(
    [isViewOpen, isEditOpen, isDeleteOpen],
    ([isViewing, isEditing, isDeleting]) => {
        if (!isViewing && !isEditing && !isDeleting) {
            selectedRecord.value = null;
        }
    },
);
onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Project Impact Tracking" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Project Impact Tracking"
            description="Monitor interventions and measurable impact across 2021–2025 programs."
            :count="records.total"
            item-label="record"
            search-placeholder="Search project, proponent, intervention, or impact..."
            search-label="Search project impact records"
        >
            <template #actions>
                <ProjectImpactImportDialog />
                <ProjectImpactFormSheet
                    v-model:open="isCreateOpen"
                    mode="create"
                >
                    <template #trigger>
                        <Button type="button" size="sm">
                            <Plus aria-hidden="true" />
                            Create
                        </Button>
                    </template>
                </ProjectImpactFormSheet>
            </template>
        </DataTableToolbar>

        <div class="grid gap-3 sm:grid-cols-3">
            <select
                v-model="sourceSheetFilter"
                aria-label="Filter by source program"
                class="h-9 rounded-lg border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
            >
                <option value="">All source programs</option>
                <option
                    v-for="sheet in options.sourceSheets"
                    :key="sheet"
                    :value="sheet"
                >
                    {{ sheet }}
                </option>
            </select>
            <select
                v-model="classificationFilter"
                aria-label="Filter by classification"
                class="h-9 rounded-lg border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
            >
                <option value="">All classifications</option>
                <option
                    v-for="classification in options.classifications"
                    :key="classification"
                    :value="classification"
                >
                    {{ classification }}
                </option>
            </select>
            <select
                v-model="statusFilter"
                aria-label="Filter by project status"
                class="h-9 rounded-lg border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
            >
                <option value="">All statuses</option>
                <option
                    v-for="status in options.statuses"
                    :key="status"
                    :value="status"
                >
                    {{ status }}
                </option>
            </select>
        </div>

        <div class="flex flex-col gap-4">
            <div class="overflow-hidden rounded-lg border border-border">
                <div class="overflow-x-auto">
                    <Table class="w-460 table-fixed">
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-16 text-center"
                                    >No.</TableHead
                                >
                                <TableHead class="w-60"
                                    >Source Program</TableHead
                                >
                                <TableHead class="w-96">
                                    Project / Technology
                                </TableHead>
                                <TableHead class="w-64">Proponent</TableHead>
                                <TableHead class="w-44">
                                    Classification
                                </TableHead>
                                <TableHead class="w-64">
                                    Program / Intervention
                                </TableHead>
                                <TableHead class="w-40 text-right">
                                    Assistance
                                </TableHead>
                                <TableHead class="w-40">Status</TableHead>
                                <TableHead class="w-64">
                                    Impact Narrative
                                </TableHead>
                                <TableHead class="w-36 text-right">
                                    Actions
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableEmpty
                                v-if="records.data.length === 0"
                                :colspan="10"
                            >
                                No project impact records found.
                            </TableEmpty>
                            <TableRow
                                v-for="(record, recordIndex) in records.data"
                                :key="record.id"
                            >
                                <TableCell
                                    class="max-w-16 text-center align-top font-medium text-muted-foreground"
                                >
                                    {{
                                        records.from
                                            ? records.from + recordIndex
                                            : recordIndex + 1
                                    }}
                                </TableCell>
                                <TableCell
                                    class="max-w-60 align-top break-words whitespace-pre-line"
                                >
                                    <Badge variant="outline">
                                        {{ record.source_sheet }}
                                    </Badge>
                                </TableCell>
                                <TableCell
                                    class="max-w-96 align-top font-medium break-words whitespace-pre-line"
                                >
                                    {{ record.project_title }}
                                </TableCell>
                                <TableCell
                                    class="max-w-64 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.proponent || '—' }}
                                </TableCell>
                                <TableCell
                                    class="max-w-44 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.classification || '—' }}
                                </TableCell>
                                <TableCell
                                    class="max-w-64 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.program_intervention || '—' }}
                                </TableCell>
                                <TableCell
                                    class="max-w-40 text-right align-top break-words whitespace-normal tabular-nums"
                                >
                                    {{ formatAmount(record.amount_assistance) }}
                                </TableCell>
                                <TableCell
                                    class="max-w-40 align-top break-words whitespace-normal"
                                >
                                    <Badge
                                        v-if="record.project_status"
                                        variant="secondary"
                                    >
                                        {{ record.project_status }}
                                    </Badge>
                                    <span v-else>—</span>
                                </TableCell>
                                <TableCell
                                    class="max-w-64 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.impact_narrative || '—' }}
                                </TableCell>
                                <TableCell class="max-w-36 align-top">
                                    <div class="flex justify-end gap-1">
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            aria-label="View project impact record"
                                            @click="openDialog(record, 'view')"
                                        >
                                            <Eye aria-hidden="true" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            aria-label="Edit project impact record"
                                            @click="openDialog(record, 'edit')"
                                        >
                                            <Pencil aria-hidden="true" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            aria-label="Delete project impact record"
                                            @click="
                                                openDialog(record, 'delete')
                                            "
                                        >
                                            <Trash2 aria-hidden="true" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <DataTablePagination
                :from="records.from"
                :to="records.to"
                :total="records.total"
                :links="records.links"
                :previous-page-url="records.prev_page_url"
                :next-page-url="records.next_page_url"
            />
        </div>
    </div>

    <ProjectImpactViewDialog
        v-model:open="isViewOpen"
        :record="selectedRecord"
    />
    <ProjectImpactFormSheet
        v-model:open="isEditOpen"
        mode="edit"
        :record="selectedRecord"
    />
    <ProjectImpactDeleteDialog
        v-model:open="isDeleteOpen"
        :record="selectedRecord"
    />
</template>
