import { CalendarPlus, Pencil, Trash2 } from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import { Badge } from '@/components/ui/badge';
import {
    DataTableColumnHeader,
    DataTableRowActions,
} from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';

import StructurePeriodsCell from './StructurePeriodsCell.vue';
import type { AcademicPeriod, AcademicTermStructure } from './types';

interface AcademicTermStructureColumnActions {
    addPeriod: (
        structure: AcademicTermStructure,
        parent?: AcademicPeriod,
    ) => void;
    editPeriod: (
        structure: AcademicTermStructure,
        period: AcademicPeriod,
        parent?: AcademicPeriod,
    ) => void;
    deletePeriod: (
        structure: AcademicTermStructure,
        period: AcademicPeriod,
    ) => void;
    editStructure: (structure: AcademicTermStructure) => void;
    deleteStructure: (structure: AcademicTermStructure) => void;
}

export const createColumns = (
    actions: AcademicTermStructureColumnActions,
): ColumnDef<AcademicTermStructure>[] => [
    {
        accessorKey: 'name',
        meta: {
            className: 'min-w-[220px] align-top',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Structure',
            }),
        cell: ({ row }) =>
            h('div', { class: 'flex flex-col gap-0.5 py-2' }, [
                h(
                    'span',
                    { class: 'font-medium text-foreground' },
                    row.original.name,
                ),
                h(
                    'span',
                    { class: 'text-xs text-muted-foreground' },
                    row.original.code,
                ),
            ]),
    },
    {
        id: 'educational_level',
        accessorFn: (row) => row.educational_level?.name ?? '',
        meta: {
            className: 'min-w-[170px] align-top',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Educational Level',
            }),
        cell: ({ row }) =>
            row.original.educational_level?.name ?? 'Not assigned',
    },
    {
        accessorKey: 'type',
        meta: {
            className: 'min-w-[120px] align-top',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Type',
            }),
        cell: ({ row }) =>
            h(
                Badge,
                { variant: 'outline', class: 'capitalize' },
                () => row.original.type,
            ),
    },
    {
        accessorKey: 'status',
        meta: {
            className: 'min-w-[110px] align-top',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Status',
            }),
        cell: ({ row }) =>
            h(
                Badge,
                {
                    variant:
                        row.original.status === 'active'
                            ? 'default'
                            : 'secondary',
                    class:
                        row.original.status === 'active'
                            ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-700 capitalize dark:text-emerald-400'
                            : 'capitalize',
                },
                () => row.original.status,
            ),
    },
    {
        id: 'periods',
        enableSorting: false,
        meta: {
            className: 'min-w-[380px] align-top',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Academic Periods',
            }),
        cell: ({ row }) =>
            h(StructurePeriodsCell, {
                structure: row.original,
                onAdd: actions.addPeriod,
                onEdit: actions.editPeriod,
                onDelete: actions.deletePeriod,
            }),
    },
    {
        id: 'actions',
        enableHiding: false,
        enableSorting: false,
        meta: {
            className: 'w-[72px] align-top text-right',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                class: 'justify-end',
                title: 'Actions',
            }),
        cell: ({ row }) =>
            h('div', { class: 'flex justify-end pt-1' }, [
                h(
                    DataTableRowActions,
                    {
                        label: `Open actions for ${row.original.name}`,
                    },
                    {
                        default: () => [
                            h(
                                DropdownMenuItem,
                                {
                                    onSelect: () =>
                                        actions.editStructure(row.original),
                                },
                                {
                                    default: () => [
                                        h(Pencil, { 'aria-hidden': true }),
                                        'Edit structure',
                                    ],
                                },
                            ),
                            h(
                                DropdownMenuItem,
                                {
                                    onSelect: () =>
                                        actions.addPeriod(row.original),
                                },
                                {
                                    default: () => [
                                        h(CalendarPlus, {
                                            'aria-hidden': true,
                                        }),
                                        'Add root period',
                                    ],
                                },
                            ),
                            h(
                                DropdownMenuItem,
                                {
                                    variant: 'destructive',
                                    onSelect: () =>
                                        actions.deleteStructure(row.original),
                                },
                                {
                                    default: () => [
                                        h(Trash2, { 'aria-hidden': true }),
                                        'Delete structure',
                                    ],
                                },
                            ),
                        ],
                    },
                ),
            ]),
    },
];
