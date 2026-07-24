<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { store, update } from '@/routes/admin/academics/academic-periods';

import type { AcademicPeriod, AcademicTermStructure } from './types';

const props = defineProps<{
    structure: AcademicTermStructure | null;
    period: AcademicPeriod | null;
    parent: AcademicPeriod | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.period ? update.form(props.period.id) : store.form(),
);

const selectedParentId = computed(
    () => props.parent?.id ?? props.period?.parent_id ?? null,
);

const handleSuccess = (): void => {
    toast.success(
        props.period ? 'Academic period updated.' : 'Academic period created.',
    );
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent>
            <Form
                v-if="structure"
                :key="period?.id ?? `${structure.id}-${parent?.id ?? 'root'}`"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleSuccess"
                @error="toast.error('Please fix the validation errors.')"
            >
                <input
                    type="hidden"
                    name="academic_term_structure_id"
                    :value="structure.id"
                />
                <input
                    v-if="selectedParentId"
                    type="hidden"
                    name="parent_id"
                    :value="selectedParentId"
                />

                <DialogHeader>
                    <DialogTitle>
                        {{
                            period
                                ? 'Edit academic period'
                                : parent
                                  ? `Add grading period to ${parent.name}`
                                  : `Add root period to ${structure.name}`
                        }}
                    </DialogTitle>
                    <DialogDescription>
                        Root periods may optionally contain one level of grading
                        periods.
                    </DialogDescription>
                </DialogHeader>

                <InputError :message="errors.academic_term_structure_id" />
                <InputError :message="errors.parent_id" />

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="period-name">Name</Label>
                        <Input
                            id="period-name"
                            name="name"
                            :default-value="period?.name"
                            placeholder="First Semester"
                            autofocus
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="period-code">Code (optional)</Label>
                        <Input
                            id="period-code"
                            name="code"
                            :default-value="period?.code ?? ''"
                            placeholder="SEM1"
                        />
                        <InputError :message="errors.code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="period-sequence">Display sequence</Label>
                        <Input
                            id="period-sequence"
                            name="sequence"
                            type="number"
                            min="1"
                            :default-value="period?.sequence ?? 1"
                        />
                        <InputError :message="errors.sequence" />
                    </div>

                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="period-status">Status</Label>
                        <Select
                            name="status"
                            :default-value="period?.status ?? 'active'"
                        >
                            <SelectTrigger id="period-status" class="w-full">
                                <SelectValue placeholder="Select status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="active">Active</SelectItem>
                                <SelectItem value="inactive"
                                    >Inactive</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.status" />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Saving...' : 'Save period' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
