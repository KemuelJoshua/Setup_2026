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
import { store, update } from '@/routes/admin/academics/grade-level';

import type { GradeLevel } from './columns';

const props = defineProps<{
    gradeLevel: GradeLevel | null;
    educationalLevels: Array<{ id: number; name: string }>;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.gradeLevel ? update.form(props.gradeLevel.id) : store.form(),
);

const handleSuccess = (): void => {
    toast.success(
        props.gradeLevel
            ? 'Grade level updated successfully.'
            : 'Grade level created successfully.',
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
                :key="gradeLevel?.id ?? 'create'"
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
                            gradeLevel
                                ? 'Edit grade level'
                                : 'Create grade level'
                        }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            gradeLevel
                                ? 'Update this grade level.'
                                : 'Add a grade level.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="grade-level-educational-level">
                            Educational Level
                        </Label>
                        <select
                            id="grade-level-educational-level"
                            name="educational_level_id"
                            :value="gradeLevel?.educational_level_id ?? ''"
                            class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        >
                            <option value="" disabled>Select a level</option>
                            <option
                                v-for="level in educationalLevels"
                                :key="level.id"
                                :value="level.id"
                            >
                                {{ level.name }}
                            </option>
                        </select>
                        <InputError :message="errors.educational_level_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="grade-level-name">Name</Label>
                        <Input
                            id="grade-level-name"
                            name="name"
                            :default-value="gradeLevel?.name"
                            placeholder="Grade 7"
                            autofocus
                        />
                        <InputError :message="errors.name" />
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
                                : gradeLevel
                                  ? 'Save changes'
                                  : 'Create grade level'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
