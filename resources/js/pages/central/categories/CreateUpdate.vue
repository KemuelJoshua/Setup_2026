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
import { store, update } from '@/routes/central/categories';

import type { Category } from './columns';

const props = defineProps<{
    category: Category | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAction = computed(() =>
    props.category ? update(props.category.id) : store(),
);

const handleSuccess = (): void => {
    toast.success(
        props.category
            ? 'Category updated successfully.'
            : 'Category created successfully.',
    );
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="sm:max-w-lg">
            <Form
                :key="category?.id ?? 'create'"
                :action="formAction"
                v-slot="{ errors, processing }"
                reset-on-success
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleSuccess"
                @error="toast.error('Please fix the validation errors.')"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{ category ? 'Edit category' : 'Create category' }}
                    </DialogTitle>
                    <DialogDescription>
                        Categories group schools for display and discovery.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="category-name">Category name</Label>
                        <Input
                            id="category-name"
                            name="name"
                            :default-value="category?.name ?? ''"
                            placeholder="Category name"
                            autofocus
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="category-status">Status</Label>
                        <Select
                            name="is_active"
                            :default-value="
                                category?.is_active === false ? '0' : '1'
                            "
                        >
                            <SelectTrigger id="category-status" class="w-full">
                                <SelectValue placeholder="Select a status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="1">Active</SelectItem>
                                <SelectItem value="0">Inactive</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.is_active" />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Saving...' : 'Save category' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
