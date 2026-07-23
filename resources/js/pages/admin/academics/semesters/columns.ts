import { Pencil, Trash2 } from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import {
    DataTableColumnHeader,
    DataTableRowActions,
} from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';

export interface Semester {
    id: number;
    name: string;
    code: string;
}

export interface SemesterFilters {
    search?: string;
    per_page?: string | number;
}

interface SemesterColumnActions {
    edit: (semester: Semester) => void;
    delete: (semester: Semester) => void;
}

/**
 * Creates the TanStack Table column definitions for the Semester module.
 *
 * Actions are injected to keep this file focused on presentation and reusable
 * across different pages or dialogs.
 */
export const createColumns = (
    actions: SemesterColumnActions,
): ColumnDef<Semester>[] => [
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
            h('div', { class: 'flex flex-col gap-0.5 py-1' }, [
                h(
                    'span',
                    {
                        class: 'font-medium leading-none text-foreground',
                    },
                    row.original.name,
                ),
                h(
                    'span',
                    {
                        class: 'text-xs text-muted-foreground',
                    },
                    row.original.code,
                ),
            ]),
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
                    h(
                        DataTableRowActions,
                        {
                            label: `Open actions for ${row.original.name}`,
                        },
                        {
                            // Inject page-specific actions into the reusable dropdown.
                            default: () => [
                                h(
                                    DropdownMenuItem,
                                    {
                                        onSelect: () =>
                                            actions.edit(row.original),
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
                                            h(Trash2, {
                                                'aria-hidden': true,
                                            }),
                                            'Delete',
                                        ],
                                    },
                                ),
                            ],
                        },
                    ),
                ],
            ),
    },
];
