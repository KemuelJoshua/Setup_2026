<script setup lang="ts">
import { FileText } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogDescription,
    DialogHeader,
    DialogScrollContent,
    DialogTitle,
} from '@/components/ui/dialog';
import type { StartupTrackingRecord } from '../types';

interface DocumentField {
    label: string;
    value: string | null;
}

const props = defineProps<{
    record: StartupTrackingRecord | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formatAmount = (amount: string | null): string =>
    amount
        ? new Intl.NumberFormat('en-PH', {
              style: 'currency',
              currency: 'PHP',
          }).format(Number(amount))
        : '—';

const formatValue = (value: string | null): string => value || '—';

const outcomeFields = (): DocumentField[] => [
    {
        label: 'Revenue Growth (annual/cumulative)',
        value: props.record?.revenue_growth ?? null,
    },
    {
        label: 'Jobs Created',
        value: props.record?.jobs_created ?? null,
    },
    {
        label: 'Investments Attracted',
        value: props.record?.investments_attracted ?? null,
    },
    {
        label: 'Market Reach',
        value: props.record?.market_reach ?? null,
    },
    {
        label: 'High-tech Exports',
        value: props.record?.high_tech_exports ?? null,
    },
    {
        label: 'Social Impact',
        value: props.record?.social_impact ?? null,
    },
];
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogScrollContent
            v-if="record"
            class="max-w-5xl self-start gap-0 overflow-hidden border-0 bg-muted p-0 shadow-2xl"
        >
            <DialogHeader class="sr-only">
                <DialogTitle>Startup tracking record</DialogTitle>
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
                                <FileText class="size-5" aria-hidden="true" />
                            </div>
                            <div>
                                <p
                                    class="text-xs font-semibold tracking-[0.18em] text-primary uppercase"
                                >
                                    PEMS
                                </p>
                                <h1 class="text-xl font-bold tracking-tight">
                                    Startup Tracking Record
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
                                ST-{{ String(record.id).padStart(5, '0') }}
                            </p>
                        </div>
                    </header>

                    <section class="border-b border-border py-8 text-center">
                        <p
                            class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            Project Title
                        </p>
                        <h2
                            class="mx-auto mt-3 max-w-3xl text-2xl leading-snug font-bold tracking-tight sm:text-3xl"
                        >
                            {{ record.project_title }}
                        </h2>
                    </section>

                    <section
                        class="grid border-b border-border sm:grid-cols-2 lg:grid-cols-5"
                    >
                        <div class="border-border p-4 lg:border-r">
                            <p class="text-xs text-muted-foreground">Type</p>
                            <p class="mt-1 font-semibold break-words">
                                {{ record.type }}
                            </p>
                        </div>
                        <div class="border-border p-4 lg:border-r">
                            <p class="text-xs text-muted-foreground">Program</p>
                            <p class="mt-1 font-semibold break-words">
                                {{ record.program }}
                            </p>
                        </div>
                        <div class="border-border p-4 lg:border-r">
                            <p class="text-xs text-muted-foreground">Class</p>
                            <p class="mt-1 font-semibold break-words">
                                {{ record.class }}
                            </p>
                        </div>
                        <div class="border-border p-4 lg:border-r">
                            <p class="text-xs text-muted-foreground">Status</p>
                            <Badge class="mt-1" variant="secondary">
                                {{ record.status || 'Not specified' }}
                            </Badge>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-muted-foreground">Amount</p>
                            <p class="mt-1 font-semibold tabular-nums">
                                {{ formatAmount(record.amount) }}
                            </p>
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
                                {{ formatValue(record.proponent_name) }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                            >
                                Contact Details
                            </h3>
                            <p
                                class="mt-3 leading-relaxed break-words whitespace-pre-line"
                            >
                                {{ formatValue(record.contact_details) }}
                            </p>
                        </div>
                    </section>

                    <section class="border-b border-border py-8">
                        <h3
                            class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                        >
                            Promotional Assistance
                        </h3>
                        <p
                            class="mt-3 leading-relaxed break-words whitespace-pre-line"
                        >
                            {{ formatValue(record.promotional_assistance) }}
                        </p>
                    </section>

                    <section class="py-8">
                        <div class="mb-5 flex items-center gap-3">
                            <h3
                                class="text-xs font-bold tracking-[0.14em] text-primary uppercase"
                            >
                                Outcomes and Impact
                            </h3>
                            <div class="h-px flex-1 bg-border" />
                        </div>

                        <dl
                            class="grid gap-px overflow-hidden border border-border bg-border sm:grid-cols-2"
                        >
                            <div
                                v-for="field in outcomeFields()"
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
                            Next Possible Intervention
                        </h3>
                        <p
                            class="mt-3 leading-relaxed font-medium break-words whitespace-pre-line"
                        >
                            {{ formatValue(record.next_possible_intervention) }}
                        </p>
                    </section>

                    <footer
                        class="mt-10 flex flex-col gap-2 border-t border-border pt-5 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
                    >
                        <span>Startup Tracking Information System</span>
                        <span>Official monitoring record</span>
                    </footer>
                </article>
            </div>
        </DialogScrollContent>
    </Dialog>
</template>
