import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import { Badge } from '@/components/ui/badge';
import { DataTableColumnHeader } from '@/components/ui/data-table';
import SchoolYearRowActions from './SchoolYearRowActions.vue';
import { formatDate } from '@/lib/formatDate.js';

export interface SchoolYear {
    id: number;
    sc_name: string;
    sc_code: string;
    sc_start_date: string;
    sc_end_date: string;
    sc_status: 'active' | 'planned' | 'closed';
}

interface SchoolYearColumnActions {
    edit: (schoolYear: SchoolYear) => void;
    delete: (schoolYear: SchoolYear) => void;
}

const getStatusClasses = (status: SchoolYear['sc_status']): string => {
    const statusClasses: Record<SchoolYear['sc_status'], string> = {
        active: 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300',
        planned:
            'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-300',
        closed: 'border-slate-200 bg-slate-100 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };

    return statusClasses[status];
};

const formatStatus = (status: string): string => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

export const createColumns = (
    actions: SchoolYearColumnActions,
): ColumnDef<SchoolYear>[] => [
    {
        accessorKey: 'sc_name',
        meta: {
            className: 'min-w-[220px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'School Year',
            }),
        cell: ({ row }) =>
            h('div', { class: 'flex flex-col gap-0.5 py-1' }, [
                h(
                    'span',
                    {
                        class: 'font-medium leading-none text-foreground',
                    },
                    row.original.sc_name,
                ),
                h(
                    'span',
                    {
                        class: 'text-xs text-muted-foreground',
                    },
                    row.original.sc_code,
                ),
            ]),
    },
    {
        accessorKey: 'sc_start_date',
        meta: {
            className: 'min-w-[150px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Start Date',
            }),
        cell: ({ row }) =>
            h(
                'span',
                {
                    class: 'whitespace-nowrap text-sm text-muted-foreground',
                },
                formatDate(row.original.sc_start_date),
            ),
    },
    {
        accessorKey: 'sc_end_date',
        meta: {
            className: 'min-w-[150px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'End Date',
            }),
        cell: ({ row }) =>
            h(
                'span',
                {
                    class: 'whitespace-nowrap text-sm text-muted-foreground',
                },
                formatDate(row.original.sc_end_date),
            ),
    },
    {
        accessorKey: 'sc_status',
        meta: {
            className: 'w-[130px]',
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
                    variant: 'outline',
                    class: [
                        'min-w-[78px] justify-center rounded-full',
                        'px-2.5 py-1 text-xs font-medium',
                        getStatusClasses(row.original.sc_status),
                    ],
                },
                () => formatStatus(row.original.sc_status),
            ),
    },
    {
        id: 'actions',
        enableHiding: false,
        enableSorting: false,
        meta: {
            className: 'w-[100px] text-right',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                class: 'justify-end',
                title: 'Actions',
            }),
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'flex justify-end',
                },
                [
                    h(SchoolYearRowActions, {
                        schoolYear: row.original,
                        onEdit: () => actions.edit(row.original),
                        onDelete: () => actions.delete(row.original),
                    }),
                ],
            ),
    },
];
