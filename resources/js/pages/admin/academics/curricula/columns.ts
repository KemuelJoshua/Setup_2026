import {
    ArrowRight,
    BookOpen,
    CalendarDays,
    CircleDotDashed,
    GraduationCap,
    Trash2,
} from '@lucide/vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DataTableColumnHeader } from '@/components/ui/data-table';
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
    educational_level_id: number | null;
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
    educational_level_id?: number;
    program_id?: number;
    status?: string;
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

export interface ProgramOption extends SelectOption {
    educational_level_id: number | null;
}

interface CurriculumColumnActions {
    manage: (curriculum: Curriculum) => void;
    changeStatus: (curriculum: Curriculum) => void;
    delete: (curriculum: Curriculum) => void;
}

const getStatusVariant = (
    status: string,
): 'default' | 'secondary' | 'outline' => {
    if (status === 'Active') {
        return 'default';
    }

    if (status === 'Inactive') {
        return 'secondary';
    }

    return 'outline';
};

const getStatusClass = (status: string): string | undefined => {
    if (status === 'Active') {
        return [
            'border-emerald-500/20',
            'bg-emerald-500/10',
            'text-emerald-700',
            'dark:text-emerald-400',
        ].join(' ');
    }

    return undefined;
};

export const createColumns = (
    actions: CurriculumColumnActions,
): ColumnDef<Curriculum>[] => [
    {
        accessorKey: 'name',
        meta: {
            className: 'min-w-[280px] py-4',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Curriculum',
            }),
        cell: ({ row }) => {
            const curriculum = row.original;

            return h('div', { class: 'flex items-start gap-3' }, [
                h(
                    'div',
                    {
                        class: [
                            'flex size-10 shrink-0 items-center justify-center',
                            'rounded-xl border border-primary/10',
                            'bg-primary/10 text-primary',
                        ].join(' '),
                    },
                    [
                        h(GraduationCap, {
                            'aria-hidden': true,
                            class: 'size-5',
                        }),
                    ],
                ),

                h('div', { class: 'min-w-0 space-y-1' }, [
                    h(
                        'button',
                        {
                            type: 'button',
                            class: [
                                'block max-w-[320px] truncate text-left',
                                'text-sm font-semibold text-foreground',
                                'transition-colors hover:text-primary',
                                'hover:underline underline-offset-4',
                            ].join(' '),
                            title: curriculum.name,
                            onClick: () => actions.manage(curriculum),
                        },
                        curriculum.name,
                    ),

                    h(
                        'p',
                        {
                            class: [
                                'max-w-[320px] truncate',
                                'text-xs text-muted-foreground',
                            ].join(' '),
                            title: curriculum.program ?? undefined,
                        },
                        curriculum.program ?? 'No program assigned',
                    ),

                    h(
                        'span',
                        {
                            class: [
                                'inline-flex rounded-md bg-muted',
                                'px-2 py-0.5 font-mono text-[11px]',
                                'text-muted-foreground',
                            ].join(' '),
                        },
                        curriculum.code,
                    ),
                ]),
            ]);
        },
    },

    {
        accessorKey: 'academic_structure',
        meta: {
            className: 'min-w-[230px] py-4',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Academic Structure',
            }),
        cell: ({ row }) => {
            const curriculum = row.original;

            return h('div', { class: 'flex items-start gap-2.5' }, [
                h(BookOpen, {
                    'aria-hidden': true,
                    class: 'mt-0.5 size-4 shrink-0 text-muted-foreground',
                }),

                h('div', { class: 'min-w-0 space-y-1' }, [
                    h(
                        'p',
                        {
                            class: [
                                'max-w-[240px] truncate',
                                'text-sm font-medium text-foreground',
                            ].join(' '),
                            title: curriculum.academic_structure ?? undefined,
                        },
                        curriculum.academic_structure ??
                            'No structure assigned',
                    ),

                    h(
                        'p',
                        {
                            class: 'text-xs text-muted-foreground',
                        },
                        `${curriculum.number_of_years} ${
                            curriculum.number_of_years === 1 ? 'year' : 'years'
                        } duration`,
                    ),
                ]),
            ]);
        },
    },

    {
        id: 'curriculum_details',
        enableSorting: false,
        meta: {
            className: 'min-w-[180px] py-4',
        },
        header: () =>
            h(
                'span',
                {
                    class: 'text-sm font-medium',
                },
                'Curriculum Details',
            ),
        cell: ({ row }) => {
            const curriculum = row.original;

            return h('div', { class: 'space-y-2' }, [
                h('div', { class: 'flex items-center gap-2' }, [
                    h(CalendarDays, {
                        'aria-hidden': true,
                        class: 'size-4 text-muted-foreground',
                    }),

                    h('div', { class: 'text-sm' }, [
                        h(
                            'span',
                            {
                                class: 'text-muted-foreground',
                            },
                            'Effective ',
                        ),
                        h(
                            'span',
                            {
                                class: 'font-medium text-foreground',
                            },
                            curriculum.effective_year.toString(),
                        ),
                    ]),
                ]),

                h('div', { class: 'flex items-center gap-2' }, [
                    h(BookOpen, {
                        'aria-hidden': true,
                        class: 'size-4 text-muted-foreground',
                    }),

                    h('div', { class: 'text-sm' }, [
                        h(
                            'span',
                            {
                                class: 'font-medium text-foreground',
                            },
                            curriculum.curriculum_subjects_count.toString(),
                        ),
                        h(
                            'span',
                            {
                                class: 'text-muted-foreground',
                            },
                            ` ${
                                curriculum.curriculum_subjects_count === 1
                                    ? 'subject'
                                    : 'subjects'
                            }`,
                        ),
                    ]),
                ]),
            ]);
        },
    },

    {
        accessorKey: 'status',
        meta: {
            className: 'min-w-[135px] py-4',
        },
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Status',
            }),
        cell: ({ row }) => {
            const curriculum = row.original;

            return h('div', { class: 'space-y-2' }, [
                h(
                    Badge,
                    {
                        variant: getStatusVariant(curriculum.status),
                        class: getStatusClass(curriculum.status),
                    },
                    () => curriculum.status,
                ),

                h(
                    'p',
                    {
                        class: [
                            'whitespace-nowrap text-xs',
                            'text-muted-foreground',
                        ].join(' '),
                    },
                    `Created ${formatDate(curriculum.created_at)}`,
                ),
            ]);
        },
    },

    {
        id: 'actions',
        enableHiding: false,
        enableSorting: false,
        meta: {
            className: 'w-[150px] py-4 text-right',
        },
        header: () =>
            h(
                'div',
                {
                    class: 'text-right text-sm font-medium',
                },
                'Actions',
            ),
        cell: ({ row }) => {
            const curriculum = row.original;

            return h(
                'div',
                {
                    class: 'flex items-center justify-end gap-2',
                },
                [
                    h(
                        Button,
                        {
                            variant: 'secondary',
                            size: 'sm',
                            class: 'gap-1.5',
                            onClick: () => actions.manage(curriculum),
                        },
                        () => [
                            h(ArrowRight, {
                                'aria-hidden': true,
                                class: 'size-3.5',
                            }),
                            'Manage',
                        ],
                    ),

                    h(
                        Button,
                        {
                            variant: 'outline',
                            size: 'icon-sm',
                            title: 'Change status',
                            'aria-label': `Change status for ${curriculum.name}`,
                            onClick: () => actions.changeStatus(curriculum),
                        },
                        () =>
                            h(CircleDotDashed, {
                                'aria-hidden': true,
                                class: 'size-4',
                            }),
                    ),

                    h(
                        Button,
                        {
                            variant: 'destructive',
                            size: 'icon-sm',
                            title: 'Delete curriculum',
                            'aria-label': `Delete ${curriculum.name}`,
                            onClick: () => actions.delete(curriculum),
                        },
                        () =>
                            h(Trash2, {
                                'aria-hidden': true,
                                class: 'size-4',
                            }),
                    ),
                ],
            );
        },
    },
];
