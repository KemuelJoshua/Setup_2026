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
import { store, update } from '@/routes/admin/academics/semester';

import type { Semester } from './columns';

const props = defineProps<{
    semester: Semester | null;
}>();

// Controls the dialog visibility from the parent component.
const isOpen = defineModel<boolean>('open', { default: false });

// Select the correct form action (create or update).
const formAttributes = computed(() =>
    props.semester ? update.form(props.semester.id) : store.form(),
);

// Display a success message and close the dialog.
const handleSuccess = (): void => {
    toast.success(
        props.semester
            ? 'Semester updated successfully.'
            : 'Semester created successfully.',
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
                :key="semester?.id ?? 'create'"
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
                        {{ semester ? 'Edit semester' : 'Create semester' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            semester
                                ? 'Update this academic period.'
                                : 'Add an academic period to the calendar.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <!-- Semester information -->
                <div class="space-y-5">
                    <div class="grid gap-2">
                        <Label for="semester-name">Name</Label>
                        <Input
                            id="semester-name"
                            name="name"
                            :default-value="semester?.name"
                            placeholder="First Semester"
                            autofocus
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="semester-code">Code</Label>
                        <Input
                            id="semester-code"
                            name="code"
                            :default-value="semester?.code"
                            placeholder="SEM-1"
                        />
                        <InputError :message="errors.code" />
                    </div>

                    <!-- Semester duration -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="semester-start-date">Start date</Label>
                            <Input
                                id="semester-start-date"
                                name="start_date"
                                type="date"
                                :default-value="semester?.start_date"
                            />
                            <InputError :message="errors.start_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="semester-end-date">End date</Label>
                            <Input
                                id="semester-end-date"
                                name="end_date"
                                type="date"
                                :default-value="semester?.end_date"
                            />
                            <InputError :message="errors.end_date" />
                        </div>
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
                                : semester
                                  ? 'Save changes'
                                  : 'Create semester'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
