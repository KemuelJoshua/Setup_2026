import { Pencil, Trash2 } from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import {
    DataTableColumnHeader,
    DataTableRowActions,
} from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { formatDate } from '@/lib/formatDate';

export interface Program {
    id: number;
    code: string;
    name: string;
    description: string | null;
    status: string;
    created_at: string;
}

export interface ProgramFilters {
    search?: string;
    per_page?: string | number;
}

interface ProgramColumnActions {
    edit: (program: Program) => void;
    delete: (program: Program) => void;
}

export const createColumns = (
    actions: ProgramColumnActions,
): ColumnDef<Program>[] => [
    {
        accessorKey: 'name',
        meta: {
            className: 'min-w-[240px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Program',
            }),
        cell: ({ row }) =>
            h('div', { class: 'flex flex-col gap-0.5 py-1' }, [
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
        accessorKey: 'status',
        meta: {
            className: 'min-w-[120px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Status',
            }),
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                row.original.status,
            ),
    },
    {
        accessorKey: 'created_at',
        meta: {
            className: 'min-w-[150px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Created At',
            }),
        cell: ({ row }) =>
            h(
                'span',
                {
                    class: 'whitespace-nowrap text-sm text-muted-foreground',
                },
                formatDate(row.original.created_at),
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
            h('div', { class: 'flex justify-end' }, [
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
                                    onSelect: () => actions.edit(row.original),
                                },
                                {
                                    default: () => [
                                        h(Pencil, { 'aria-hidden': true }),
                                        'Edit',
                                    ],
                                },
                            ),
                            h(
                                DropdownMenuItem,
                                {
                                    variant: 'destructive',
                                    onSelect: () =>
                                        actions.delete(row.original),
                                },
                                {
                                    default: () => [
                                        h(Trash2, { 'aria-hidden': true }),
                                        'Delete',
                                    ],
                                },
                            ),
                        ],
                    },
                ),
            ]),
    },
];
