import { ShieldCheck } from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import { DataTableColumnHeader } from '@/components/ui/data-table';
import RoleRowActions from './RoleRowActions.vue';

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    permission_ids: number[];
}

export interface Permission {
    id: number;
    name: string;
    category: string;
    guard_name: string;
}

interface RoleColumnActions {
    edit: (role: Role) => void;
    delete: (role: Role) => void;
}

export const createColumns = (
    actions: RoleColumnActions,
): ColumnDef<Role>[] => [
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Role',
            }),
        cell: ({ row }) =>
            h('div', { class: 'flex items-center gap-3' }, [
                h(
                    'span',
                    {
                        class: 'flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/8 text-primary dark:bg-primary/15',
                    },
                    [
                        h(ShieldCheck, {
                            'aria-hidden': true,
                            class: 'size-4.5',
                        }),
                    ],
                ),
                h(
                    'span',
                    { class: 'font-medium text-foreground' },
                    row.original.name,
                ),
            ]),
    },
    {
        accessorKey: 'guard_name',
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Guard',
            }),
        cell: ({ row }) =>
            h(
                Badge,
                {
                    variant: 'secondary',
                    class: 'font-mono text-xs',
                },
                () => row.original.guard_name,
            ),
    },
    {
        id: 'actions',
        meta: {
            className: 'w-30 text-end',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                class: 'justify-end',
                title: 'Actions',
            }),
        cell: ({ row }) =>
            h(RoleRowActions, {
                role: row.original,
                onEdit: () => actions.edit(row.original),
                onDelete: () => actions.delete(row.original),
            }),
    },
];
