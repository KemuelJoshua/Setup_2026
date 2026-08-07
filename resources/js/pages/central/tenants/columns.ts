import { Power, PowerOff, Pencil, Trash2 } from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import { Badge } from '@/components/ui/badge';
import {
    DataTableColumnHeader,
    DataTableRowActions,
} from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';

export interface Tenant {
    id: string;
    school_code: string;
    school_name: string;
    school_address: string | null;
    school_email: string | null;
    school_contact_number: string | null;
    school_motto: string | null;
    school_website: string | null;
    school_director: string | null;
    domain: string | null;
    is_active: boolean;
}

export interface TenantFilters {
    search?: string;
    per_page?: string | number;
}

interface TenantColumnActions {
    edit: (tenant: Tenant) => void;
    toggleStatus: (tenant: Tenant) => void;
    delete: (tenant: Tenant) => void;
}

export const createColumns = (
    actions: TenantColumnActions,
): ColumnDef<Tenant>[] => [
    {
        accessorKey: 'school_name',
        meta: { className: 'min-w-[220px]' },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'School',
            }),
        cell: ({ row }) =>
            h('div', { class: 'flex flex-col gap-0.5 py-1' }, [
                h(
                    'span',
                    { class: 'font-medium text-foreground' },
                    row.original.school_name,
                ),
                h(
                    'span',
                    { class: 'text-xs text-muted-foreground' },
                    row.original.school_code,
                ),
            ]),
    },
    {
        accessorKey: 'domain',
        meta: { className: 'min-w-[200px]' },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Domain',
            }),
        cell: ({ row }) =>
            h(
                'span',
                { class: 'font-mono text-sm text-muted-foreground' },
                row.original.domain ?? 'No domain assigned',
            ),
    },
    {
        accessorKey: 'school_email',
        meta: { className: 'min-w-[210px]' },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Contact',
            }),
        cell: ({ row }) =>
            h('div', { class: 'flex flex-col gap-0.5 text-sm' }, [
                h('span', {}, row.original.school_email ?? 'No email address'),
                row.original.school_contact_number
                    ? h(
                          'span',
                          { class: 'text-xs text-muted-foreground' },
                          row.original.school_contact_number,
                      )
                    : null,
            ]),
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
                    { label: `Open actions for ${row.original.school_name}` },
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
                                    onSelect: () =>
                                        actions.toggleStatus(row.original),
                                },
                                {
                                    default: () => [
                                        h(
                                            row.original.is_active
                                                ? PowerOff
                                                : Power,
                                            { 'aria-hidden': true },
                                        ),
                                        row.original.is_active
                                            ? 'Deactivate'
                                            : 'Activate',
                                    ],
                                },
                            ),
                            !row.original.is_active
                                ? h(
                                      DropdownMenuItem,
                                      {
                                          variant: 'destructive',
                                          onSelect: () =>
                                              actions.delete(row.original),
                                      },
                                      {
                                          default: () => [
                                              h(Trash2, {
                                                  'aria-hidden': true,
                                              }),
                                              'Delete',
                                          ],
                                      },
                                  )
                                : null,
                        ],
                    },
                ),
            ),
    },
];
