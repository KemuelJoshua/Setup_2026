import { Pencil, Trash2 } from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import { Badge } from '@/components/ui/badge';
import {
    DataTableColumnHeader,
    DataTableRowActions,
} from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';

export interface Category {
    id: number;
    name: string;
    is_active: boolean;
    tenants_count: number;
}

export interface CategoryFilters {
    search?: string;
    per_page?: string | number;
}

interface CategoryColumnActions {
    edit: (category: Category) => void;
    delete: (category: Category) => void;
}

export const createColumns = (
    actions: CategoryColumnActions,
): ColumnDef<Category>[] => [
    {
        accessorKey: 'name',
        meta: { className: 'min-w-[240px]' },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Category',
            }),
        cell: ({ row }) =>
            h(
                'span',
                { class: 'font-medium text-foreground' },
                row.original.name,
            ),
    },
    {
        accessorKey: 'tenants_count',
        meta: { className: 'w-[140px]' },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Schools',
            }),
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                row.original.tenants_count,
            ),
    },
    {
        accessorKey: 'is_active',
        meta: { className: 'w-[120px]' },
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
                    class: row.original.is_active
                        ? 'rounded-full border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300'
                        : 'rounded-full border-slate-200 bg-slate-100 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
                },
                () => (row.original.is_active ? 'Active' : 'Inactive'),
            ),
    },
    {
        id: 'actions',
        enableHiding: false,
        enableSorting: false,
        meta: { className: 'w-[100px] text-right' },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                class: 'justify-end',
                title: 'Actions',
            }),
        cell: ({ row }) =>
            h(
                'div',
                { class: 'flex justify-end' },
                h(
                    DataTableRowActions,
                    { label: `Open actions for ${row.original.name}` },
                    {
                        default: () => [
                            h(
                                DropdownMenuItem,
                                { onSelect: () => actions.edit(row.original) },
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
            ),
    },
];
