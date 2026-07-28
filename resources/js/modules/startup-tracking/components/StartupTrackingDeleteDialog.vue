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
import { destroy } from '@/routes/admin/startup-tracking';
import type { StartupTrackingRecord } from '../types';

defineProps<{
    record: StartupTrackingRecord | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const onSuccess = (): void => {
    toast.success('Startup tracking record deleted.');
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent v-if="record">
            <Form
                v-bind="destroy.form(record.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="onSuccess"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>Delete startup record?</DialogTitle>
                    <DialogDescription>
                        “{{ record.project_title }}” will be permanently
                        deleted.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Deleting...' : 'Delete record' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
