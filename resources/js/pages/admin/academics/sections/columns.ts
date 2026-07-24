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

export interface Section {
    id: number;
    educational_level_id: number | null;
    educational_level: { id: number; name: string } | null;
    name: string;
    created_at: string;
}

export interface SectionFilters {
    search?: string;
    educational_level_id?: string | number;
    per_page?: string | number;
}

interface SectionColumnActions {
    edit: (section: Section) => void;
    delete: (section: Section) => void;
}

export const createColumns = (
    actions: SectionColumnActions,
): ColumnDef<Section>[] => [
    {
        accessorKey: 'name',
        meta: {
            className: 'min-w-[220px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Name',
            }),
        cell: ({ row }) =>
            h(
                'span',
                { class: 'font-medium text-foreground' },
                row.original.name,
            ),
    },
    {
        accessorKey: 'educational_level.name',
        meta: {
            className: 'min-w-[190px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Educational Level',
            }),
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                row.original.educational_level?.name ?? 'Not assigned',
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
