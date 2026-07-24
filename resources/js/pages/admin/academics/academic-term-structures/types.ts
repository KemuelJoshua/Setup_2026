export type AcademicStatus = 'active' | 'inactive';

export type AcademicTermStructureType =
    | 'semester'
    | 'quarterly'
    | 'trisem'
    | 'term';

export interface AcademicPeriod {
    id: number;
    academic_term_structure_id: number;
    parent_id: number | null;
    name: string;
    code: string | null;
    sequence: number;
    status: AcademicStatus;
    children: AcademicPeriod[];
}

export interface AcademicTermStructure {
    id: number;
    name: string;
    code: string;
    type: AcademicTermStructureType;
    status: AcademicStatus;
    root_periods: AcademicPeriod[];
}

export interface AcademicTermStructureFilters {
    search?: string;
    per_page?: string | number;
}
