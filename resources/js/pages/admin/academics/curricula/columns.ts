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

export interface CurriculumSubject {
    id: number;
    subject_id: number;
    subject_name: string;
    year_level_id: number;
    year_level_name: string;
    academic_period_id: number;
    academic_period_name: string;
    units: string | null;
    lecture_hours: string | null;
    laboratory_hours: string | null;
    sort_order: number;
    remarks: string | null;
    prerequisites: CurriculumSubjectReference[];
    corequisites: CurriculumSubjectReference[];
}

export interface CurriculumSubjectReference {
    id: number;
    name: string;
}

export interface AcademicPeriodOption {
    id: number;
    name: string;
    code: string | null;
    sequence: number;
}

export interface AcademicStructureOption {
    id: number;
    name: string;
    code: string;
    type: string;
    root_periods: AcademicPeriodOption[];
}

export interface Curriculum {
    id: number;
    code: string;
    name: string;
    program: string | null;
    academic_structure: string | null;
    effective_year: number;
    number_of_years: number;
    description: string | null;
    status: string;
    curriculum_subjects_count: number;
    created_at: string;
}

export interface CurriculumFormData {
    id: number;
    code: string;
    name: string;
    program: SelectOption | null;
    academic_structure: AcademicStructureOption | null;
    effective_year: number;
    number_of_years: number;
    description: string | null;
    status: string;
    curriculum_subjects: CurriculumSubject[];
}

export interface CurriculumFilters {
    search?: string;
    per_page?: string | number;
}

export interface SelectOption {
    id: number;
    name: string;
    code?: string;
    structure?: {
        name: string;
        type: string;
    };
}

interface CurriculumColumnActions {
    edit: (curriculum: Curriculum) => void;
    delete: (curriculum: Curriculum) => void;
}

export const createColumns = (
    actions: CurriculumColumnActions,
): ColumnDef<Curriculum>[] => [
    {
        accessorKey: 'name',
        meta: {
            className: 'min-w-[240px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Curriculum',
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
        accessorKey: 'program',
        meta: {
            className: 'min-w-[170px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Program',
            }),
    },
    {
        accessorKey: 'academic_structure',
        meta: {
            className: 'min-w-[220px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Academic Structure',
            }),
    },
    {
        accessorKey: 'effective_year',
        meta: {
            className: 'min-w-[130px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Effective Year',
            }),
    },
    {
        accessorKey: 'number_of_years',
        meta: {
            className: 'min-w-[120px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Duration',
            }),
        cell: ({ row }) =>
            `${row.original.number_of_years} ${row.original.number_of_years === 1 ? 'year' : 'years'}`,
    },
    {
        accessorKey: 'curriculum_subjects_count',
        meta: {
            className: 'min-w-[110px]',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Subjects',
            }),
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
