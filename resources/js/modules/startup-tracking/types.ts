export interface StartupTrackingRecord {
    id: number;
    type: string;
    program: string;
    project_title: string;
    proponent_name: string | null;
    contact_details: string | null;
    amount: string | null;
    class: string;
    status: string | null;
    promotional_assistance: string | null;
    revenue_growth: string | null;
    jobs_created: string | null;
    investments_attracted: string | null;
    market_reach: string | null;
    high_tech_exports: string | null;
    social_impact: string | null;
    next_possible_intervention: string | null;
}
