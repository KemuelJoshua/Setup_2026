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
import { store, update } from '@/routes/admin/academics/section';

import type { Section } from './columns';

const props = defineProps<{
    section: Section | null;
    educationalLevels: Array<{ id: number; name: string }>;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.section ? update.form(props.section.id) : store.form(),
);

const handleSuccess = (): void => {
    toast.success(
        props.section
            ? 'Section updated successfully.'
            : 'Section created successfully.',
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
                :key="section?.id ?? 'create'"
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
                        {{ section ? 'Edit section' : 'Create section' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            section ? 'Update this section.' : 'Add a section.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="section-educational-level">
                            Educational Level
                        </Label>
                        <Select
                            name="educational_level_id"
                            :default-value="
                                section?.educational_level_id ?? undefined
                            "
                        >
                            <SelectTrigger
                                id="section-educational-level"
                                class="w-full"
                            >
                                <SelectValue placeholder="Select a level" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="level in educationalLevels"
                                    :key="level.id"
                                    :value="level.id"
                                >
                                    {{ level.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.educational_level_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="section-name">Name</Label>
                        <Input
                            id="section-name"
                            name="name"
                            :default-value="section?.name"
                            placeholder="Section A"
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
                                : section
                                  ? 'Save changes'
                                  : 'Create section'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
