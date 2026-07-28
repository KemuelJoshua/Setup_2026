<script setup lang="ts">
import { FileChartColumn } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogDescription,
    DialogHeader,
    DialogScrollContent,
    DialogTitle,
} from '@/components/ui/dialog';
import type { ProjectImpactRecord } from '../types';

interface DocumentField {
    label: string;
    value: string | null;
}

const props = defineProps<{ record: ProjectImpactRecord | null }>();
const isOpen = defineModel<boolean>('open', { default: false });
const formatValue = (value: string | null): string => value || '—';
const formatAmount = (amount: string | null): string =>
    amount
        ? new Intl.NumberFormat('en-PH', {
              style: 'currency',
              currency: 'PHP',
          }).format(Number(amount))
        : '—';
const humanize = (key: string): string =>
    key.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
const formatAdditionalValue = (value: unknown): string => {
    if (Array.isArray(value)) {
        return value
            .map((item, index) => {
                if (typeof item !== 'object' || item === null) {
                    return String(item);
                }

                return `${index + 1}. ${Object.entries(item)
                    .filter(([, nestedValue]) => nestedValue)
                    .map(
                        ([key, nestedValue]) =>
                            `${humanize(key)}: ${String(nestedValue)}`,
                    )
                    .join(' • ')}`;
            })
            .join('\n');
    }

    if (typeof value === 'object' && value !== null) {
        return Object.entries(value)
            .map(([key, nestedValue]) => `${humanize(key)}: ${nestedValue}`)
            .join('\n');
    }

    return String(value ?? '—');
};

const impactFields = (): DocumentField[] => [
    { label: 'Revenue Amount', value: props.record?.revenue_amount ?? null },
    {
        label: 'Technology Commercialized or Licensed',
        value: props.record?.technology_commercialized ?? null,
    },
    {
        label: 'Jobs Created or Sustained',
        value: props.record?.jobs_created ?? null,
    },
    {
        label: 'Private / Counterpart Investment Leveraged',
        value: props.record?.investment_leveraged ?? null,
    },
    {
        label: 'Income, Productivity, or Service Efficiency Improved',
        value: props.record?.efficiency_improved ?? null,
    },
    {
        label: 'Communities, LGUs, or Institutions Served',
        value: props.record?.communities_served ?? null,
    },
    {
        label: 'Women and Priority Sectors Benefited',
        value: props.record?.priority_sectors_benefited ?? null,
    },
    {
        label: 'IP Assets Utilized or Transferred',
        value: props.record?.ip_assets_utilized ?? null,
    },
    {
        label: 'Spin-offs or Startups Formed',
        value: props.record?.spin_offs_formed ?? null,
    },
    {
        label: 'Human Capital Developed',
        value: props.record?.human_capital_developed ?? null,
    },
    { label: 'Other Impacts', value: props.record?.other_impacts ?? null },
];
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogScrollContent
            v-if="record"
            class="max-w-5xl self-start gap-0 overflow-hidden border-0 bg-muted p-0 shadow-2xl"
        >
            <DialogHeader class="sr-only">
                <DialogTitle>Project impact record</DialogTitle>
                <DialogDescription>
                    Complete document view for {{ record.project_title }}.
                </DialogDescription>
            </DialogHeader>

            <div class="p-3 sm:p-6 lg:p-8">
                <article
                    class="mx-auto min-h-[70vh] max-w-4xl border border-border/70 bg-background px-5 py-8 shadow-sm sm:px-10 sm:py-12"
                >
                    <header
                        class="flex flex-col gap-6 border-b-2 border-primary pb-7 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-11 items-center justify-center rounded-full bg-primary text-primary-foreground"
                            >
                                <FileChartColumn
                                    class="size-5"
                                    aria-hidden="true"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-xs font-semibold tracking-[0.18em] text-primary uppercase"
                                >
                                    PEMS
                                </p>
                                <h1 class="text-xl font-bold tracking-tight">
                                    Project Impact Tracking Record
                                </h1>
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <p
                                class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                            >
                                Record Number
                            </p>
                            <p class="font-mono text-sm font-semibold">
                                PI-{{ String(record.id).padStart(5, '0') }}
                            </p>
                        </div>
                    </header>

                    <section class="border-b border-border py-8 text-center">
                        <Badge variant="outline">{{
                            record.source_sheet
                        }}</Badge>
                        <h2
                            class="mx-auto mt-4 max-w-3xl text-2xl leading-snug font-bold tracking-tight sm:text-3xl"
                        >
                            {{ record.project_title }}
                        </h2>
                        <p
                            v-if="record.record_number"
                            class="mt-2 text-sm text-muted-foreground"
                        >
                            Source record {{ record.record_number }}
                        </p>
                    </section>

                    <section
                        class="grid border-b border-border sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <div class="border-border p-4 lg:border-r">
                            <p class="text-xs text-muted-foreground">
                                Classification
                            </p>
                            <p class="mt-1 font-semibold break-words">
                                {{ formatValue(record.classification) }}
                            </p>
                        </div>
                        <div class="border-border p-4 lg:border-r">
                            <p class="text-xs text-muted-foreground">
                                Sub-classification
                            </p>
                            <p class="mt-1 font-semibold break-words">
                                {{ formatValue(record.sub_classification) }}
                            </p>
                        </div>
                        <div class="border-border p-4 lg:border-r">
                            <p class="text-xs text-muted-foreground">IP Type</p>
                            <p class="mt-1 font-semibold break-words">
                                {{ formatValue(record.ip_type) }}
                            </p>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-muted-foreground">Status</p>
                            <Badge class="mt-1" variant="secondary">
                                {{ record.project_status || 'Not specified' }}
                            </Badge>
                        </div>
                    </section>

                    <section
                        class="grid gap-6 border-b border-border py-8 sm:grid-cols-2"
                    >
                        <div>
                            <h3
                                class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                            >
                                Proponent
                            </h3>
                            <p
                                class="mt-3 leading-relaxed break-words whitespace-pre-line"
                            >
                                {{ formatValue(record.proponent) }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                            >
                                Field of Technology
                            </h3>
                            <p
                                class="mt-3 leading-relaxed break-words whitespace-pre-line"
                            >
                                {{ formatValue(record.field_of_technology) }}
                            </p>
                        </div>
                    </section>

                    <section class="border-b border-border py-8">
                        <div class="mb-5 flex items-center gap-3">
                            <h3
                                class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                            >
                                Assistance and Intervention
                            </h3>
                            <div class="h-px flex-1 bg-border" />
                        </div>
                        <dl
                            class="grid gap-px overflow-hidden border border-border bg-border sm:grid-cols-3"
                        >
                            <div class="bg-background p-4 sm:col-span-2">
                                <dt class="text-xs text-muted-foreground">
                                    Program / Intervention
                                </dt>
                                <dd
                                    class="mt-2 break-words whitespace-pre-line"
                                >
                                    {{
                                        formatValue(record.program_intervention)
                                    }}
                                </dd>
                            </div>
                            <div class="bg-background p-4">
                                <dt class="text-xs text-muted-foreground">
                                    Amount
                                </dt>
                                <dd class="mt-2 font-semibold tabular-nums">
                                    {{ formatAmount(record.amount_assistance) }}
                                </dd>
                            </div>
                            <div class="bg-background p-4">
                                <dt class="text-xs text-muted-foreground">
                                    Date of Assistance
                                </dt>
                                <dd class="mt-2">
                                    {{ formatValue(record.date_assistance) }}
                                </dd>
                            </div>
                            <div class="bg-background p-4">
                                <dt class="text-xs text-muted-foreground">
                                    Readiness Before
                                </dt>
                                <dd class="mt-2">
                                    {{ formatValue(record.readiness_before) }}
                                </dd>
                            </div>
                            <div class="bg-background p-4">
                                <dt class="text-xs text-muted-foreground">
                                    Readiness After
                                </dt>
                                <dd class="mt-2">
                                    {{ formatValue(record.readiness_after) }}
                                </dd>
                            </div>
                            <div class="bg-background p-4 sm:col-span-3">
                                <dt class="text-xs text-muted-foreground">
                                    Other Interventions
                                </dt>
                                <dd
                                    class="mt-2 break-words whitespace-pre-line"
                                >
                                    {{
                                        formatValue(record.other_interventions)
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section class="py-8">
                        <div class="mb-5 flex items-center gap-3">
                            <h3
                                class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                            >
                                Measurable Impact as of 2025
                            </h3>
                            <div class="h-px flex-1 bg-border" />
                        </div>
                        <dl
                            class="grid gap-px overflow-hidden border border-border bg-border sm:grid-cols-2"
                        >
                            <div
                                v-for="field in impactFields()"
                                :key="field.label"
                                class="min-h-28 bg-background p-5"
                            >
                                <dt
                                    class="text-xs font-semibold text-muted-foreground"
                                >
                                    {{ field.label }}
                                </dt>
                                <dd
                                    class="mt-2 leading-relaxed break-words whitespace-pre-line"
                                >
                                    {{ formatValue(field.value) }}
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section
                        class="border-l-4 border-primary bg-primary/5 px-5 py-5"
                    >
                        <h3
                            class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                        >
                            Status / Narrative of Impact
                        </h3>
                        <p
                            class="mt-3 leading-relaxed font-medium break-words whitespace-pre-line"
                        >
                            {{ formatValue(record.impact_narrative) }}
                        </p>
                    </section>

                    <section
                        v-if="
                            record.additional_data &&
                            Object.keys(record.additional_data).length > 0
                        "
                        class="mt-8 border-t border-border pt-8"
                    >
                        <h3
                            class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                        >
                            Program-specific Details
                        </h3>
                        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div
                                v-for="(value, key) in record.additional_data"
                                :key="key"
                                class="border border-border p-4"
                            >
                                <dt
                                    class="text-xs font-semibold text-muted-foreground"
                                >
                                    {{ humanize(key) }}
                                </dt>
                                <dd
                                    class="mt-2 text-sm leading-relaxed break-words whitespace-pre-line"
                                >
                                    {{ formatAdditionalValue(value) }}
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <footer
                        class="mt-10 flex flex-col gap-2 border-t border-border pt-5 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
                    >
                        <span>Project Impact Tracking Matrix 2021–2025</span>
                        <span>Official monitoring record</span>
                    </footer>
                </article>
            </div>
        </DialogScrollContent>
    </Dialog>
</template>
