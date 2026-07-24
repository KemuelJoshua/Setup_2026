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
import { Textarea } from '@/components/ui/textarea';
import { store, update } from '@/routes/admin/academics/program';

import type { Program } from './columns';

const props = defineProps<{
    program: Program | null;
    educationalLevels: Array<{ id: number; name: string }>;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.program ? update.form(props.program.id) : store.form(),
);

const handleSuccess = (): void => {
    toast.success(
        props.program
            ? 'Program updated successfully.'
            : 'Program created successfully.',
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
                :key="program?.id ?? 'create'"
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
                        {{ program ? 'Edit program' : 'Create program' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            program
                                ? 'Update this academic program.'
                                : 'Add an academic program.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-5">
                    <div class="grid gap-2">
                        <Label for="program-educational-level">
                            Educational Level
                        </Label>
                        <Select
                            name="educational_level_id"
                            :default-value="
                                program?.educational_level_id ?? undefined
                            "
                        >
                            <SelectTrigger
                                id="program-educational-level"
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

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="program-code">Code</Label>
                            <Input
                                id="program-code"
                                name="code"
                                :default-value="program?.code"
                                placeholder="BSCS"
                                autofocus
                            />
                            <InputError :message="errors.code" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="program-status">Status</Label>
                            <Input
                                id="program-status"
                                name="status"
                                :default-value="program?.status ?? 'Active'"
                                placeholder="Active"
                            />
                            <InputError :message="errors.status" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="program-name">Name</Label>
                        <Input
                            id="program-name"
                            name="name"
                            :default-value="program?.name"
                            placeholder="Bachelor of Science in Computer Science"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="program-description">Description</Label>
                        <Textarea
                            id="program-description"
                            name="description"
                            :default-value="program?.description ?? ''"
                            rows="4"
                            placeholder="Optional program description"
                        />
                        <InputError :message="errors.description" />
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
                                : program
                                  ? 'Save changes'
                                  : 'Create program'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
