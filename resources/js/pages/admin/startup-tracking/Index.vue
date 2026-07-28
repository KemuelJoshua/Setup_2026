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
import StartupTrackingDeleteDialog from '@/modules/startup-tracking/components/StartupTrackingDeleteDialog.vue';
import StartupTrackingFormSheet from '@/modules/startup-tracking/components/StartupTrackingFormSheet.vue';
import StartupTrackingImportDialog from '@/modules/startup-tracking/components/StartupTrackingImportDialog.vue';
import StartupTrackingViewDialog from '@/modules/startup-tracking/components/StartupTrackingViewDialog.vue';
import type { StartupTrackingRecord } from '@/modules/startup-tracking/types';
import { index } from '@/routes/admin/startup-tracking';
import type { LengthAwarePaginator } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Startup Tracking',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    records: LengthAwarePaginator<StartupTrackingRecord>;
    filters: {
        search?: string;
        type?: string;
        program?: string;
        status?: string;
        per_page?: string | number;
    };
    options: {
        types: string[];
        programs: string[];
        statuses: string[];
    };
}>();

const searchQuery = ref(props.filters.search ?? '');
const typeFilter = ref(props.filters.type ?? '');
const programFilter = ref(props.filters.program ?? '');
const statusFilter = ref(props.filters.status ?? '');
const isCreateOpen = ref(false);
const isViewOpen = ref(false);
const isEditOpen = ref(false);
const isDeleteOpen = ref(false);
const selectedRecord = ref<StartupTrackingRecord | null>(null);
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchRecords = (): void => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
                type: typeFilter.value || undefined,
                program: programFilter.value || undefined,
                status: statusFilter.value || undefined,
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

const editRecord = (record: StartupTrackingRecord): void => {
    selectedRecord.value = record;
    isEditOpen.value = true;
};

const viewRecord = (record: StartupTrackingRecord): void => {
    selectedRecord.value = record;
    isViewOpen.value = true;
};

const deleteRecord = (record: StartupTrackingRecord): void => {
    selectedRecord.value = record;
    isDeleteOpen.value = true;
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
watch([typeFilter, programFilter, statusFilter], fetchRecords);
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
    <Head title="Startup Tracking" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="Startup Tracking"
            description="Track startup assistance, growth, investments, market reach, and social impact."
            :count="records.total"
            item-label="record"
            search-placeholder="Search project, proponent, or contact..."
            search-label="Search startup records"
        >
            <template #actions>
                <StartupTrackingImportDialog />
                <StartupTrackingFormSheet
                    v-model:open="isCreateOpen"
                    mode="create"
                >
                    <template #trigger>
                        <Button type="button" size="sm">
                            <Plus aria-hidden="true" />
                            Create
                        </Button>
                    </template>
                </StartupTrackingFormSheet>
            </template>
        </DataTableToolbar>

        <div class="grid gap-3 sm:grid-cols-3">
            <select
                v-model="typeFilter"
                aria-label="Filter by type"
                class="h-9 rounded-lg border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
            >
                <option value="">All types</option>
                <option v-for="type in options.types" :key="type" :value="type">
                    {{ type }}
                </option>
            </select>
            <select
                v-model="programFilter"
                aria-label="Filter by program"
                class="h-9 rounded-lg border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
            >
                <option value="">All programs</option>
                <option
                    v-for="program in options.programs"
                    :key="program"
                    :value="program"
                >
                    {{ program }}
                </option>
            </select>
            <select
                v-model="statusFilter"
                aria-label="Filter by status"
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
                    <Table class="w-440 table-fixed">
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-16 text-center"
                                    >No.</TableHead
                                >
                                <TableHead class="w-28">Type</TableHead>
                                <TableHead class="w-40">Program</TableHead>
                                <TableHead class="w-80">
                                    Project Title
                                </TableHead>
                                <TableHead class="w-56">Proponent</TableHead>
                                <TableHead class="w-56">Contact</TableHead>
                                <TableHead class="w-40 text-right">
                                    Amount
                                </TableHead>
                                <TableHead class="w-32">Class</TableHead>
                                <TableHead class="w-36">Status</TableHead>
                                <TableHead class="w-64">
                                    Promotional Assistance
                                </TableHead>
                                <TableHead class="w-36 text-right">
                                    Actions
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableEmpty
                                v-if="records.data.length === 0"
                                :colspan="11"
                            >
                                No startup tracking records found.
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
                                    class="max-w-28 align-top break-words whitespace-normal"
                                >
                                    <Badge variant="outline">
                                        {{ record.type }}
                                    </Badge>
                                </TableCell>
                                <TableCell
                                    class="max-w-40 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.program }}
                                </TableCell>
                                <TableCell
                                    class="max-w-80 align-top font-medium break-words whitespace-pre-line"
                                >
                                    {{ record.project_title }}
                                </TableCell>
                                <TableCell
                                    class="max-w-56 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.proponent_name || '—' }}
                                </TableCell>
                                <TableCell
                                    class="max-w-56 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.contact_details || '—' }}
                                </TableCell>
                                <TableCell
                                    class="max-w-40 text-right align-top break-words whitespace-normal tabular-nums"
                                >
                                    {{ formatAmount(record.amount) }}
                                </TableCell>
                                <TableCell
                                    class="max-w-32 align-top break-words whitespace-normal"
                                >
                                    {{ record.class }}
                                </TableCell>
                                <TableCell
                                    class="max-w-36 align-top break-words whitespace-normal"
                                >
                                    <Badge
                                        v-if="record.status"
                                        variant="secondary"
                                    >
                                        {{ record.status }}
                                    </Badge>
                                    <span v-else>—</span>
                                </TableCell>
                                <TableCell
                                    class="max-w-64 align-top break-words whitespace-pre-line"
                                >
                                    {{ record.promotional_assistance || '—' }}
                                </TableCell>
                                <TableCell class="max-w-36 align-top">
                                    <div class="flex justify-end gap-1">
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            aria-label="View startup record"
                                            @click="viewRecord(record)"
                                        >
                                            <Eye aria-hidden="true" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            aria-label="Edit startup record"
                                            @click="editRecord(record)"
                                        >
                                            <Pencil aria-hidden="true" />
                                        </Button>
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            aria-label="Delete startup record"
                                            @click="deleteRecord(record)"
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

    <StartupTrackingViewDialog
        v-model:open="isViewOpen"
        :record="selectedRecord"
    />
    <StartupTrackingFormSheet
        v-model:open="isEditOpen"
        mode="edit"
        :record="selectedRecord"
    />
    <StartupTrackingDeleteDialog
        v-model:open="isDeleteOpen"
        :record="selectedRecord"
    />
</template>
