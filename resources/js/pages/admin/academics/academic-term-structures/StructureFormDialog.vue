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
import {
    store,
    update,
} from '@/routes/admin/academics/academic-term-structures';

import type {
    AcademicTermStructure,
    AcademicTermStructureType,
    EducationalLevelOption,
} from './types';

const props = defineProps<{
    structure: AcademicTermStructure | null;
    educationalLevels: EducationalLevelOption[];
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
                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="structure-educational-level">
                            Educational level
                        </Label>
                        <Select
                            name="educational_level_id"
                            :default-value="
                                structure?.educational_level_id
                                    ? String(structure.educational_level_id)
                                    : undefined
                            "
                        >
                            <SelectTrigger
                                id="structure-educational-level"
                                class="w-full"
                            >
                                <SelectValue
                                    placeholder="Select educational level"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="level in educationalLevels"
                                    :key="level.id"
                                    :value="String(level.id)"
                                >
                                    {{ level.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.educational_level_id" />
                    </div>

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
                        <Select
                            name="type"
                            :default-value="structure?.type ?? 'semester'"
                        >
                            <SelectTrigger id="structure-type" class="w-full">
                                <SelectValue placeholder="Select type" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="typeOption in structureTypes"
                                    :key="typeOption.value"
                                    :value="typeOption.value"
                                >
                                    {{ typeOption.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.type" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="structure-status">Status</Label>
                        <Select
                            name="status"
                            :default-value="structure?.status ?? 'active'"
                        >
                            <SelectTrigger id="structure-status" class="w-full">
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
                        {{ processing ? 'Saving...' : 'Save structure' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
