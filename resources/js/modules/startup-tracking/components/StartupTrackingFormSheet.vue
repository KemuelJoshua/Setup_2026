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
import { store, update } from '@/routes/admin/startup-tracking';
import type { StartupTrackingRecord } from '../types';

interface FormField {
    key: keyof Omit<StartupTrackingRecord, 'id'>;
    label: string;
    placeholder?: string;
    required?: boolean;
    type?: 'number' | 'text';
    multiline?: boolean;
}

const fields: FormField[] = [
    { key: 'type', label: 'Type', placeholder: 'PAC', required: true },
    {
        key: 'program',
        label: 'Program',
        placeholder: 'TECHNICOM 1.0',
        required: true,
    },
    {
        key: 'project_title',
        label: 'Project Title',
        required: true,
        multiline: true,
    },
    {
        key: 'proponent_name',
        label: 'Name of Proponent',
        multiline: true,
    },
    {
        key: 'contact_details',
        label: 'Contact Details',
        multiline: true,
    },
    { key: 'amount', label: 'Amount', type: 'number' },
    { key: 'class', label: 'Class', required: true },
    { key: 'status', label: 'Status', placeholder: 'ONGOING' },
    {
        key: 'promotional_assistance',
        label: 'Promotional Assistance',
        multiline: true,
    },
    {
        key: 'revenue_growth',
        label: 'Revenue Growth (annual/cumulative)',
        multiline: true,
    },
    { key: 'jobs_created', label: 'Jobs Created', multiline: true },
    {
        key: 'investments_attracted',
        label: 'Investments Attracted',
        multiline: true,
    },
    { key: 'market_reach', label: 'Market Reach', multiline: true },
    {
        key: 'high_tech_exports',
        label: 'High-tech Exports (if applicable)',
        multiline: true,
    },
    { key: 'social_impact', label: 'Social Impact', multiline: true },
    {
        key: 'next_possible_intervention',
        label: 'Next Possible Intervention',
        multiline: true,
    },
];

const props = defineProps<{
    mode: 'create' | 'edit';
    record?: StartupTrackingRecord | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });
const formAttributes = computed(() =>
    props.mode === 'edit' && props.record
        ? update.form(props.record.id)
        : store.form(),
);
const title = computed(() =>
    props.mode === 'edit' ? 'Edit startup record' : 'Create startup record',
);

const fieldValue = (field: FormField): string => {
    if (field.key === 'class') {
        return props.record?.class ?? 'STARTUP';
    }

    return String(props.record?.[field.key] ?? '');
};

const onSuccess = (): void => {
    toast.success(
        props.mode === 'edit'
            ? 'Startup tracking record updated.'
            : 'Startup tracking record created.',
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
                        Record the startup’s program information, outcomes, and
                        next intervention.
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
                            :placeholder="field.placeholder"
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
                            :placeholder="field.placeholder"
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
