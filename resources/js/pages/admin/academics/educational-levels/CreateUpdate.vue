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
import { store, update } from '@/routes/admin/academics/educational-level';

import type { EducationalLevel } from './columns';

const props = defineProps<{
    educationalLevel: EducationalLevel | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.educationalLevel
        ? update.form(props.educationalLevel.id)
        : store.form(),
);

const handleSuccess = (): void => {
    toast.success(
        props.educationalLevel
            ? 'Educational level updated successfully.'
            : 'Educational level created successfully.',
    );
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent>
            <Form
                :key="educationalLevel?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                reset-on-success
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleSuccess"
                @error="toast.error('Please fix the validation errors.')"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{
                            educationalLevel
                                ? 'Edit educational level'
                                : 'Create educational level'
                        }}
                    </DialogTitle>
                    <DialogDescription>
                        Organize academic records by educational level.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="educational-level-name">Name</Label>
                    <Input
                        id="educational-level-name"
                        name="name"
                        :default-value="educationalLevel?.name"
                        placeholder="Junior High School"
                        autofocus
                    />
                    <InputError :message="errors.name" />
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{
                            processing
                                ? 'Saving...'
                                : educationalLevel
                                  ? 'Save changes'
                                  : 'Create educational level'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
