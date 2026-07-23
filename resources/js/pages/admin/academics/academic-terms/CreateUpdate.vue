<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
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
import { store, update } from '@/routes/admin/academics/academic-term';

import type { AcademicTerm } from './columns';

const props = defineProps<{
    academicTerm: AcademicTerm | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

interface EditableGradingPeriod {
    key: number;
    name: string;
    code: string;
}

const gradingPeriods = ref<EditableGradingPeriod[]>([]);
let nextPeriodKey = 0;

const formAttributes = computed(() =>
    props.academicTerm ? update.form(props.academicTerm.id) : store.form(),
);

const resetGradingPeriods = (): void => {
    gradingPeriods.value =
        props.academicTerm?.grading_periods.map((period) => ({
            key: nextPeriodKey++,
            name: period.name,
            code: period.code,
        })) ?? [];
};

const addGradingPeriod = (): void => {
    gradingPeriods.value.push({
        key: nextPeriodKey++,
        name: '',
        code: '',
    });
};

const removeGradingPeriod = (index: number): void => {
    gradingPeriods.value.splice(index, 1);
};

const handleSuccess = (): void => {
    toast.success(
        props.academicTerm
            ? 'Academic term updated successfully.'
            : 'Academic term created successfully.',
    );

    isOpen.value = false;
};

const handleError = (): void => {
    toast.error('Please fix the validation errors.');
};

watch(isOpen, (open) => {
    if (open) {
        resetGradingPeriods();
    }
});
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-2xl">
            <Form
                :key="academicTerm?.id ?? 'create'"
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
                            academicTerm
                                ? 'Edit academic term'
                                : 'Add academic term'
                        }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            academicTerm
                                ? 'Update the term and its grading periods.'
                                : 'Enter the term details and add grading periods if needed.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="academic-term-name">Name</Label>
                            <Input
                                id="academic-term-name"
                                name="name"
                                :default-value="academicTerm?.name"
                                placeholder="1st Semester"
                                autofocus
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="academic-term-code">Code</Label>
                            <Input
                                id="academic-term-code"
                                name="code"
                                :default-value="academicTerm?.code"
                                placeholder="1ST"
                            />
                            <InputError :message="errors.code" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="academic-term-type">Term type</Label>
                        <select
                            id="academic-term-type"
                            name="type"
                            :value="academicTerm?.type ?? ''"
                            class="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20"
                        >
                            <option value="" disabled>Select a type</option>
                            <option value="Quarter">Quarter</option>
                            <option value="Semester">Semester</option>
                            <option value="Not Applicable">
                                Not Applicable
                            </option>
                        </select>
                        <InputError :message="errors.type" />
                    </div>

                    <div class="space-y-3 border-t border-border pt-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-foreground">
                                    Grading periods
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Optional, such as Prelim, Midterm, and
                                    Finals.
                                </p>
                            </div>

                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="addGradingPeriod"
                            >
                                <Plus aria-hidden="true" />
                                Add period
                            </Button>
                        </div>

                        <div v-if="gradingPeriods.length" class="space-y-3">
                            <div
                                v-for="(period, index) in gradingPeriods"
                                :key="period.key"
                                class="grid gap-3 rounded-lg border border-border bg-muted/20 p-3 sm:grid-cols-[minmax(0,1fr)_140px_auto]"
                            >
                                <div class="grid gap-2">
                                    <Label
                                        :for="`grading-period-name-${period.key}`"
                                    >
                                        Name
                                    </Label>
                                    <Input
                                        :id="`grading-period-name-${period.key}`"
                                        v-model="period.name"
                                        :name="`grading_periods[${index}][name]`"
                                        placeholder="Prelim"
                                    />
                                    <InputError
                                        :message="
                                            errors[
                                                `grading_periods.${index}.name`
                                            ]
                                        "
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        :for="`grading-period-code-${period.key}`"
                                    >
                                        Code
                                    </Label>
                                    <Input
                                        :id="`grading-period-code-${period.key}`"
                                        v-model="period.code"
                                        :name="`grading_periods[${index}][code]`"
                                        placeholder="PRE"
                                    />
                                    <InputError
                                        :message="
                                            errors[
                                                `grading_periods.${index}.code`
                                            ]
                                        "
                                    />
                                </div>

                                <input
                                    type="hidden"
                                    :name="`grading_periods[${index}][sort_order]`"
                                    :value="index + 1"
                                />

                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    class="self-end text-muted-foreground hover:text-destructive"
                                    :aria-label="`Remove ${period.name || 'grading period'}`"
                                    @click="removeGradingPeriod(index)"
                                >
                                    <Trash2 aria-hidden="true" />
                                </Button>
                            </div>
                        </div>

                        <div
                            v-else
                            class="rounded-lg border border-dashed border-border px-4 py-5 text-center text-sm text-muted-foreground"
                        >
                            No grading periods added.
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
                                : academicTerm
                                  ? 'Save changes'
                                  : 'Add academic term'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
