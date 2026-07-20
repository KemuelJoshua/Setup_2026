<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
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
import { destroy } from '@/routes/admin/school-years';
import type { SchoolYear } from './columns';

defineProps<{
    schoolYear: SchoolYear | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const onSuccess = (): void => {
    toast.success('School year deleted successfully.');
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent v-if="schoolYear">
            <Form
                v-bind="destroy.form(schoolYear.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="onSuccess"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>Delete school year?</DialogTitle>
                    <DialogDescription>
                        {{ schoolYear.sc_name }} ({{ schoolYear.sc_code }}) will
                        be permanently deleted.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary"
                            >Cancel</Button
                        >
                    </DialogClose>
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Deleting...' : 'Delete school year' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
