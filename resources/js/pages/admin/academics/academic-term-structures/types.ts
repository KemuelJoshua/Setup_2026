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
    educational_level_id: number | null;
    educational_level: EducationalLevelOption | null;
    name: string;
    code: string;
    type: AcademicTermStructureType;
    status: AcademicStatus;
    root_periods: AcademicPeriod[];
}

export interface AcademicTermStructureFilters {
    search?: string;
    educational_level_id?: number;
    per_page?: string | number;
}

export interface EducationalLevelOption {
    id: number;
    name: string;
}
