<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { store, update } from '@/routes/admin/project-impact-tracking';
import type { ProjectImpactRecord } from '../types';

interface FormField {
    key: keyof Omit<ProjectImpactRecord, 'id' | 'additional_data'>;
    label: string;
    required?: boolean;
    type?: 'date' | 'number' | 'text';
    multiline?: boolean;
}

const fields: FormField[] = [
    { key: 'source_sheet', label: 'Source Sheet / Program', required: true },
    { key: 'record_number', label: 'Record Number' },
    {
        key: 'project_title',
        label: 'Project / Technology Title',
        required: true,
        multiline: true,
    },
    { key: 'proponent', label: 'Proponent', multiline: true },
    { key: 'classification', label: 'Classification' },
    { key: 'sub_classification', label: 'Sub-classification' },
    { key: 'ip_type', label: 'IP Type' },
    { key: 'field_of_technology', label: 'Field of Technology' },
    {
        key: 'program_intervention',
        label: 'Program / Intervention',
        multiline: true,
    },
    { key: 'amount_assistance', label: 'Amount of Assistance', type: 'number' },
    { key: 'date_assistance', label: 'Date of Assistance', type: 'date' },
    { key: 'project_status', label: 'Project Status' },
    { key: 'date_completed', label: 'Date Completed', type: 'date' },
    { key: 'readiness_before', label: 'TRL / IRL / MRL Before' },
    { key: 'readiness_after', label: 'TRL / IRL / MRL After' },
    {
        key: 'other_interventions',
        label: 'Other Interventions',
        multiline: true,
    },
    { key: 'revenue_amount', label: 'Revenue Amount', multiline: true },
    {
        key: 'technology_commercialized',
        label: 'Technology Commercialized or Licensed',
        multiline: true,
    },
    {
        key: 'jobs_created',
        label: 'Jobs Created or Sustained',
        multiline: true,
    },
    {
        key: 'investment_leveraged',
        label: 'Private / Counterpart Investment Leveraged',
        multiline: true,
    },
    {
        key: 'efficiency_improved',
        label: 'Income, Productivity, or Service Efficiency Improved',
        multiline: true,
    },
    {
        key: 'communities_served',
        label: 'Communities, LGUs, or Institutions Served',
        multiline: true,
    },
    {
        key: 'priority_sectors_benefited',
        label: 'Women and Priority Sectors Benefited',
        multiline: true,
    },
    {
        key: 'ip_assets_utilized',
        label: 'IP Assets Utilized or Transferred',
        multiline: true,
    },
    {
        key: 'spin_offs_formed',
        label: 'Spin-offs or Startups Formed',
        multiline: true,
    },
    {
        key: 'human_capital_developed',
        label: 'Human Capital Developed',
        multiline: true,
    },
    { key: 'other_impacts', label: 'Other Impacts', multiline: true },
    {
        key: 'impact_narrative',
        label: 'Status / Narrative of Impact',
        multiline: true,
    },
];

const props = defineProps<{
    mode: 'create' | 'edit';
    record?: ProjectImpactRecord | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });
const formAttributes = computed(() =>
    props.mode === 'edit' && props.record
        ? update.form(props.record.id)
        : store.form(),
);
const title = computed(() =>
    props.mode === 'edit'
        ? 'Edit project impact record'
        : 'Create project impact record',
);
const fieldValue = (field: FormField): string =>
    String(props.record?.[field.key] ?? '');

const onSuccess = (): void => {
    toast.success(
        props.mode === 'edit'
            ? 'Project impact record updated.'
            : 'Project impact record created.',
    );
    isOpen.value = false;
};
</script>

<template>
    <Sheet v-model:open="isOpen">
        <SheetTrigger v-if="$slots.trigger" as-child>
            <slot name="trigger" />
        </SheetTrigger>

        <SheetContent side="right" class="w-full gap-0 p-0 sm:max-w-2xl">
            <Form
                v-if="mode === 'create' || record"
                :key="record?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                reset-on-success
                class="flex min-h-0 flex-1 flex-col"
                :options="{ preserveScroll: true }"
                @success="onSuccess"
                @error="toast.error('Please fix the validation errors.')"
            >
                <SheetHeader class="border-b border-border px-6 py-5 text-left">
                    <SheetTitle>{{ title }}</SheetTitle>
                    <SheetDescription>
                        Record interventions, readiness, and measurable project
                        impacts.
                    </SheetDescription>
                </SheetHeader>

                <div
                    class="grid flex-1 gap-5 overflow-y-auto px-6 py-5 sm:grid-cols-2"
                >
                    <div
                        v-for="field in fields"
                        :key="field.key"
                        class="grid content-start gap-2"
                        :class="{ 'sm:col-span-2': field.multiline }"
                    >
                        <Label :for="`${mode}-${field.key}`">
                            {{ field.label }}
                        </Label>
                        <textarea
                            v-if="field.multiline"
                            :id="`${mode}-${field.key}`"
                            :name="field.key"
                            :value="fieldValue(field)"
                            :required="field.required"
                            rows="3"
                            class="min-h-20 w-full resize-y rounded-lg border border-input bg-background px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        />
                        <Input
                            v-else
                            :id="`${mode}-${field.key}`"
                            :name="field.key"
                            :type="field.type ?? 'text'"
                            :default-value="fieldValue(field)"
                            :required="field.required"
                            :step="field.type === 'number' ? '0.01' : undefined"
                            :min="field.type === 'number' ? '0' : undefined"
                        />
                        <InputError :message="errors[field.key]" />
                    </div>
                </div>

                <SheetFooter
                    class="border-t border-border px-6 py-4 sm:flex-row sm:justify-end"
                >
                    <SheetClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </SheetClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Saving...' : 'Save record' }}
                    </Button>
                </SheetFooter>
            </Form>
        </SheetContent>
    </Sheet>
</template>
