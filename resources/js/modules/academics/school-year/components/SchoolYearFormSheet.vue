<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { store, update } from '@/routes/admin/school-years';
import type { SchoolYear } from './columns';

const props = defineProps<{
    mode: 'create' | 'edit';
    schoolYear?: SchoolYear | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.mode === 'edit' && props.schoolYear
        ? update.form(props.schoolYear.id)
        : store.form(),
);

const title = computed(() =>
    props.mode === 'edit' ? 'Edit school year' : 'Create school year',
);
const description = computed(() =>
    props.mode === 'edit'
        ? 'Update the academic period and its current status.'
        : 'Add a new academic period to the school calendar.',
);
const submitLabel = computed(() =>
    props.mode === 'edit' ? 'Save changes' : 'Create school year',
);
const processingLabel = computed(() =>
    props.mode === 'edit' ? 'Saving...' : 'Creating...',
);

const closeSheet = (): void => {
    isOpen.value = false;
};

const onSuccess = (): void => {
    toast.success(
        props.mode === 'edit'
            ? 'School year updated successfully.'
            : 'School year created successfully.',
    );
    closeSheet();
};

const onError = (): void => {
    toast.error('Please fix the validation errors.');
};
</script>

<template>
    <Sheet v-model:open="isOpen">
        <SheetTrigger v-if="$slots.trigger" as-child>
            <slot name="trigger" />
        </SheetTrigger>

        <SheetContent side="right" class="w-full gap-0 p-0 sm:max-w-lg">
            <Form
                v-if="mode === 'create' || schoolYear"
                :key="schoolYear?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                reset-on-success
                class="flex min-h-0 flex-1 flex-col"
                :options="{ preserveScroll: true }"
                @success="onSuccess"
                @error="onError"
            >
                <SheetHeader class="border-b border-border px-6 py-5 text-left">
                    <SheetTitle>{{ title }}</SheetTitle>
                    <SheetDescription>{{ description }}</SheetDescription>
                </SheetHeader>

                <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5">
                    <div class="grid gap-2">
                        <Label :for="`${mode}-school-year-name`">Name</Label>
                        <Input
                            :id="`${mode}-school-year-name`"
                            name="sc_name"
                            :default-value="schoolYear?.sc_name"
                            placeholder="School Year 2028-2029"
                            autofocus
                        />
                        <InputError :message="errors.sc_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`${mode}-school-year-code`">Code</Label>
                        <Input
                            :id="`${mode}-school-year-code`"
                            name="sc_code"
                            :default-value="schoolYear?.sc_code"
                            placeholder="SY-2028"
                        />
                        <InputError :message="errors.sc_code" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label :for="`${mode}-school-year-start-date`"
                                >Start date</Label
                            >
                            <Input
                                :id="`${mode}-school-year-start-date`"
                                name="sc_start_date"
                                type="date"
                                :default-value="schoolYear?.sc_start_date"
                            />
                            <InputError :message="errors.sc_start_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label :for="`${mode}-school-year-end-date`"
                                >End date</Label
                            >
                            <Input
                                :id="`${mode}-school-year-end-date`"
                                name="sc_end_date"
                                type="date"
                                :default-value="schoolYear?.sc_end_date"
                            />
                            <InputError :message="errors.sc_end_date" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`${mode}-school-year-status`"
                            >Status</Label
                        >
                        <select
                            :id="`${mode}-school-year-status`"
                            name="sc_status"
                            :value="schoolYear?.sc_status ?? 'planned'"
                            class="h-9 w-full rounded-lg border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        >
                            <option value="planned">Planned</option>
                            <option value="active">Active</option>
                            <option value="closed">Closed</option>
                        </select>
                        <InputError :message="errors.sc_status" />
                    </div>
                </div>

                <SheetFooter
                    class="border-t border-border px-6 py-4 sm:flex-row sm:justify-end"
                >
                    <SheetClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </SheetClose>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? processingLabel : submitLabel }}
                    </Button>
                </SheetFooter>
            </Form>
        </SheetContent>
    </Sheet>
</template>
