<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Upload } from '@lucide/vue';
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
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { importMethod } from '@/routes/admin/project-impact-tracking';

const isOpen = defineModel<boolean>('open', { default: false });

const onSuccess = (): void => {
    toast.success('Project impact workbook imported.');
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button type="button" size="sm" variant="outline">
                <Upload aria-hidden="true" />
                Import Excel
            </Button>
        </DialogTrigger>

        <DialogContent>
            <Form
                v-bind="importMethod.form()"
                v-slot="{ errors, processing, progress }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                reset-on-success
                @success="onSuccess"
                @error="toast.error('The workbook could not be imported.')"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>Import project impact records</DialogTitle>
                    <DialogDescription>
                        Upload the Project Impact Tracking Matrix workbook. All
                        supported program sheets will be imported together.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="project-impact-import-file">Workbook</Label>
                    <Input
                        id="project-impact-import-file"
                        name="file"
                        type="file"
                        accept=".xlsx,.csv"
                        required
                    />
                    <InputError :message="errors.file" />
                    <p v-if="progress" class="text-sm text-muted-foreground">
                        Uploading {{ progress.percentage }}%
                    </p>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Importing...' : 'Import records' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
