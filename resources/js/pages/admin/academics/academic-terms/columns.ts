import { Pencil, Trash2 } from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import {
    DataTableColumnHeader,
    DataTableRowActions,
} from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';

export interface AcademicTerm {
    id: number;
    name: string;
    code: string;
    type: 'Quarter' | 'Semester' | 'Not Applicable';
    grading_periods: GradingPeriod[];
}

export interface GradingPeriod {
    id: number;
    name: string;
    code: string;
    sort_order: number;
}

export interface AcademicTermFilters {
    search?: string;
    per_page?: string | number;
}

interface AcademicTermColumnActions {
    edit: (academicTerm: AcademicTerm) => void;
    delete: (academicTerm: AcademicTerm) => void;
}

/**
 * Creates the TanStack Table column definitions for the Academic Term module.
 *
 * Actions are injected to keep this file focused on presentation and reusable
 * across different pages or dialogs.
 */
export const createColumns = (
    actions: AcademicTermColumnActions,
): ColumnDef<AcademicTerm>[] => [
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
        id: 'grading_periods',
        meta: {
            className: 'min-w-[260px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Grading periods',
            }),
        cell: ({ row }) => {
            const periods = row.original.grading_periods;

            return h(
                'div',
                { class: 'flex flex-wrap gap-1.5' },
                periods.length
                    ? periods.map((period) =>
                          h(
                              'span',
                              {
                                  class: 'rounded-md bg-muted px-2 py-1 text-xs text-muted-foreground',
                              },
                              period.name,
                          ),
                      )
                    : [
                          h(
                              'span',
                              { class: 'text-sm text-muted-foreground' },
                              'No grading periods',
                          ),
                      ],
            );
        },
    },
    {
        accessorKey: 'type',
        meta: {
            className: 'min-w-[160px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Type',
            }),
        cell: ({ row }) =>
            h(
                'span',
                {
                    class: 'inline-flex rounded-full border border-border bg-muted/40 px-2 py-0.5 text-xs font-medium text-foreground',
                },
                row.original.type,
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
