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
import { store, update } from '@/routes/admin/school-years';

import type { SchoolYear } from './columns';

const props = defineProps<{
    schoolYear: SchoolYear | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.schoolYear ? update.form(props.schoolYear.id) : store.form(),
);

const handleSuccess = (): void => {
    toast.success(
        props.schoolYear
            ? 'School year updated successfully.'
            : 'School year created successfully.',
    );

    isOpen.value = false;
};

const handleError = (): void => {
    toast.error('Please fix the validation errors.');
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent>
            <Form
                :key="schoolYear?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                reset-on-success
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleSuccess"
                @error="handleError"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{
                            schoolYear
                                ? 'Edit school year'
                                : 'Create school year'
                        }}
                    </DialogTitle>

                    <DialogDescription>
                        {{
                            schoolYear
                                ? 'Update the academic period and its current status.'
                                : 'Add a new academic period to the school calendar.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="school-year-name">Name</Label>
                        <Input
                            id="school-year-name"
                            name="sc_name"
                            :default-value="schoolYear?.sc_name"
                            placeholder="School Year 2028-2029"
                            autofocus
                        />
                        <InputError :message="errors.sc_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="school-year-code">Code</Label>
                        <Input
                            id="school-year-code"
                            name="sc_code"
                            :default-value="schoolYear?.sc_code"
                            placeholder="SY-2028"
                        />
                        <InputError :message="errors.sc_code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="school-year-status">Status</Label>
                        <Select
                            name="sc_status"
                            :default-value="schoolYear?.sc_status ?? 'planned'"
                        >
                            <SelectTrigger
                                id="school-year-status"
                                class="w-full"
                            >
                                <SelectValue placeholder="Select a status" />
                            </SelectTrigger>

                            <SelectContent>
                                <SelectItem value="planned">Planned</SelectItem>
                                <SelectItem value="active">Active</SelectItem>
                                <SelectItem value="closed">Closed</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.sc_status" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="school-year-start-date">Start date</Label>
                        <Input
                            id="school-year-start-date"
                            name="sc_start_date"
                            type="date"
                            :default-value="schoolYear?.sc_start_date"
                        />
                        <InputError :message="errors.sc_start_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="school-year-end-date">End date</Label>
                        <Input
                            id="school-year-end-date"
                            name="sc_end_date"
                            type="date"
                            :default-value="schoolYear?.sc_end_date"
                        />
                        <InputError :message="errors.sc_end_date" />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>

                    <Button type="submit" :disabled="processing">
                        {{
                            processing
                                ? 'Saving...'
                                : schoolYear
                                  ? 'Save changes'
                                  : 'Create school year'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
