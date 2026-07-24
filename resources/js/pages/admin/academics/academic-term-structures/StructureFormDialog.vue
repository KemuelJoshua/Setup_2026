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
    store,
    update,
} from '@/routes/admin/academics/academic-term-structures';

import type { AcademicTermStructure, AcademicTermStructureType } from './types';

const props = defineProps<{
    structure: AcademicTermStructure | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.structure ? update.form(props.structure.id) : store.form(),
);

const structureTypes: {
    value: AcademicTermStructureType;
    label: string;
}[] = [
    { value: 'semester', label: 'Semester' },
    { value: 'quarterly', label: 'Quarterly' },
    { value: 'trisem', label: 'Trisem' },
    { value: 'term', label: 'Term' },
];

const handleSuccess = (): void => {
    toast.success(
        props.structure
            ? 'Academic term structure updated.'
            : 'Academic term structure created.',
    );
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent>
            <Form
                :key="structure?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleSuccess"
                @error="toast.error('Please fix the validation errors.')"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{
                            structure
                                ? 'Edit academic term structure'
                                : 'Add academic term structure'
                        }}
                    </DialogTitle>
                    <DialogDescription>
                        Define the academic calendar used by a school level or
                        program.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="structure-name">Name</Label>
                        <Input
                            id="structure-name"
                            name="name"
                            :default-value="structure?.name"
                            placeholder="College"
                            autofocus
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="structure-code">Code</Label>
                        <Input
                            id="structure-code"
                            name="code"
                            :default-value="structure?.code"
                            placeholder="COLLEGE"
                        />
                        <InputError :message="errors.code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="structure-type">Type</Label>
                        <select
                            id="structure-type"
                            name="type"
                            :value="structure?.type ?? 'semester'"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        >
                            <option
                                v-for="typeOption in structureTypes"
                                :key="typeOption.value"
                                :value="typeOption.value"
                            >
                                {{ typeOption.label }}
                            </option>
                        </select>
                        <InputError :message="errors.type" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="structure-status">Status</Label>
                        <select
                            id="structure-status"
                            name="status"
                            :value="structure?.status ?? 'active'"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <InputError :message="errors.status" />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Saving...' : 'Save structure' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
