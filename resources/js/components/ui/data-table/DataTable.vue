<script
    setup
    lang="ts"
    generic="TData, TValue"
>
import type {
    ColumnDef,
    Row,
    SortingState,
    Table as TableInstance,
} from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import type { HTMLAttributes } from 'vue';
import { ref } from 'vue';
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { valueUpdater } from '@/components/ui/table/utils';
import { cn } from '@/lib/utils';

type RowClass<TData> =
    | HTMLAttributes['class']
    | ((row: Row<TData>) => HTMLAttributes['class']);

const props = withDefaults(
    defineProps<{
        columns: ColumnDef<TData, TValue>[];
        data: TData[];
        emptyMessage?: string;
        class?: HTMLAttributes['class'];
        containerClass?: HTMLAttributes['class'];
        tableClass?: HTMLAttributes['class'];
        rowClass?: RowClass<TData>;
        getRowId?: (originalRow: TData, index: number, parent?: Row<TData>) => string;
        manualSorting?: boolean;
    }>(),
    {
        emptyMessage: 'No results found.',
        class: undefined,
        containerClass: undefined,
        tableClass: undefined,
        rowClass: undefined,
        getRowId: undefined,
        manualSorting: false,
    },
);

defineSlots<{
    toolbar?: (slotProps: { table: TableInstance<TData> }) => unknown;
    empty?: (slotProps: { table: TableInstance<TData> }) => unknown;
    pagination?: (slotProps: { table: TableInstance<TData> }) => unknown;
}>();

const sorting = ref<SortingState>([]);

const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getRowId: props.getRowId,
    manualSorting: props.manualSorting,
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    state: {
        get sorting() {
            return sorting.value;
        },
    },
});

const getRowClass = (row: Row<TData>): HTMLAttributes['class'] => {
    if (typeof props.rowClass === 'function') {
        return props.rowClass(row);
    }

    return props.rowClass;
};

const getColumnClass = (
    columnDef: ColumnDef<TData, TValue>,
): HTMLAttributes['class'] => {
    return (columnDef.meta as { className?: HTMLAttributes['class'] } | undefined)
        ?.className;
};
</script>

<template>
    <div data-slot="data-table" :class="cn('space-y-4', props.class)">
        <slot name="toolbar" :table="table" />

        <div
            :class="
                cn(
                    'overflow-hidden rounded-lg border border-border bg-background',
                    props.containerClass,
                )
            "
        >
            <Table :class="props.tableClass">
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                            :class="getColumnClass(header.column.columnDef)"
                        >
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="table.getRowModel().rows.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :data-state="row.getIsSelected() && 'selected'"
                            :class="getRowClass(row)"
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                :class="getColumnClass(cell.column.columnDef)"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableEmpty
                        v-else
                        :colspan="table.getAllLeafColumns().length"
                    >
                        <slot name="empty" :table="table">
                            {{ props.emptyMessage }}
                        </slot>
                    </TableEmpty>
                </TableBody>
            </Table>
        </div>

        <slot name="pagination" :table="table" />
    </div>
</template>
