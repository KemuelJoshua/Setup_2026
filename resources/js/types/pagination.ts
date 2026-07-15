export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface LengthAwarePaginator<T> {
    data: T[];
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationLink[];
    next_page_url: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}
