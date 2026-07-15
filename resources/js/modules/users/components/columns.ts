import type { ColumnDef } from '@tanstack/vue-table';

export interface User {
    id: number;
    name: string;
    email: string;
}

export const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'email',
        header: 'Email',
    },
];
