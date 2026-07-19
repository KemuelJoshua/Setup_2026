import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';
import { DataTableColumnHeader } from '@/components/ui/data-table';
import UserRowActions from './UserRowActions.vue';

export interface User {
    id: number;
    name: string;
    email: string;
}

export const columns: ColumnDef<User>[] = [
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
            }),
    },
];
