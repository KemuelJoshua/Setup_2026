import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import { DataTableColumnHeader } from '@/components/ui/data-table';
import UserRowActions from './UserRowActions.vue';

export interface User {
    id: number;
    name: string;
    email: string;
    roles: string[];
}

interface UserColumnActions {
    edit: (user: User) => void;
    delete: (user: User) => void;
}

export const createColumns = (
    actions: UserColumnActions,
): ColumnDef<User>[] => [
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Name',
            }),
    },
    {
        accessorKey: 'email',
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Email',
            }),
    },
    {
        accessorKey: 'roles',
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Role',
            }),
        cell: ({ row }) =>
            row.original.roles.length > 0
                ? h(
                      'div',
                      { class: 'flex flex-wrap gap-1.5' },
                      row.original.roles.map((role) =>
                          h(Badge, { variant: 'secondary' }, () => role),
                      ),
                  )
                : h('span', { class: 'text-muted-foreground' }, 'No role'),
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
            h(UserRowActions, {
                user: row.original,
                onEdit: () => actions.edit(row.original),
                onDelete: () => actions.delete(row.original),
            }),
    },
];
