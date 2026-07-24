<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, store, update } from '@/routes/admin/academics/curriculum';

import type { CurriculumFormData, SelectOption } from './columns';

interface CurriculumSubjectRow {
    key: number;
    subject_id: number | '';
    year_level_id: number | '';
    academic_period_id: number | '';
    is_required: number;
    sort_order: number;
}

const props = defineProps<{
    curriculum: CurriculumFormData | null;
    subjects: SelectOption[];
    yearLevels: SelectOption[];
    academicPeriods: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Curricula',
                href: index(),
            },
        ],
    },
});

const activeTab = ref<'curriculum' | 'subjects'>('curriculum');
let nextRowKey = 0;

const curriculumSubjects = ref<CurriculumSubjectRow[]>(
    props.curriculum?.curriculum_subjects.map((subject) => ({
        key: nextRowKey++,
        subject_id: subject.subject_id,
        year_level_id: subject.year_level_id,
        academic_period_id: subject.academic_period_id,
        is_required: subject.is_required ? 1 : 0,
        sort_order: subject.sort_order,
    })) ?? [],
);

const pageTitle = computed(() =>
    props.curriculum ? 'Edit curriculum' : 'Create curriculum',
);

const formAttributes = computed(() =>
    props.curriculum ? update.form(props.curriculum.id) : store.form(),
);

const addSubject = (): void => {
    curriculumSubjects.value.push({
        key: nextRowKey++,
        subject_id: '',
        year_level_id: '',
        academic_period_id: '',
        is_required: 1,
        sort_order: curriculumSubjects.value.length + 1,
    });
};

const removeSubject = (index: number): void => {
    curriculumSubjects.value.splice(index, 1);
};

const subjectError = (
    errors: Record<string, string>,
    index: number,
    field: string,
): string | undefined => errors[`curriculum_subjects.${index}.${field}`];

const handleError = (errors: Record<string, string>): void => {
    activeTab.value = Object.keys(errors).some((key) =>
        key.startsWith('curriculum_subjects'),
    )
        ? 'subjects'
        : 'curriculum';

    toast.error('Please fix the validation errors.');
};
</script>

<template>
    <Head :title="pageTitle" />

    <div
        class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 px-4 py-6 sm:px-6 md:py-8"
    >
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ pageTitle }}
            </h1>
            <p class="max-w-2xl text-sm leading-6 text-muted-foreground">
                Enter the curriculum details and assign its subjects.
            </p>
        </div>

        <Form
            v-bind="formAttributes"
            v-slot="{ errors, processing }"
            class="overflow-hidden rounded-xl border bg-card shadow-sm"
            :options="{ preserveScroll: true }"
            @error="handleError"
        >
            <div
                role="tablist"
                aria-label="Curriculum form sections"
                class="flex gap-2 border-b bg-muted/20 px-4 pt-3 sm:px-6"
            >
                <button
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === 'curriculum'"
                    aria-controls="curriculum-panel"
                    class="flex items-center gap-2 rounded-t-lg border-b-2 px-4 py-3 text-sm font-medium transition-colors hover:text-foreground"
                    :class="
                        activeTab === 'curriculum'
                            ? 'border-primary text-foreground'
                            : 'border-transparent text-muted-foreground'
                    "
                    @click="activeTab = 'curriculum'"
                >
                    Curriculum
                </button>
                <button
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === 'subjects'"
                    aria-controls="subjects-panel"
                    class="flex items-center gap-2 rounded-t-lg border-b-2 px-4 py-3 text-sm font-medium transition-colors hover:text-foreground"
                    :class="
                        activeTab === 'subjects'
                            ? 'border-primary text-foreground'
                            : 'border-transparent text-muted-foreground'
                    "
                    @click="activeTab = 'subjects'"
                >
                    Subjects
                    <span
                        v-if="curriculumSubjects.length"
                        class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground tabular-nums"
                    >
                        {{ curriculumSubjects.length }}
                    </span>
                </button>
            </div>

            <div
                id="curriculum-panel"
                v-show="activeTab === 'curriculum'"
                role="tabpanel"
                class="space-y-6 p-5 sm:p-6"
            >
                <div class="max-w-4xl space-y-1">
                    <h2 class="text-base font-semibold">Curriculum details</h2>
                    <p class="text-sm leading-6 text-muted-foreground">
                        Basic information used to identify this curriculum.
                    </p>
                </div>

                <div
                    class="grid max-w-4xl gap-x-5 gap-y-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div class="grid gap-2">
                        <Label for="curriculum-code">Code</Label>
                        <Input
                            id="curriculum-code"
                            name="code"
                            :default-value="curriculum?.code"
                            placeholder="BSCS-2026"
                            class="h-10"
                            autofocus
                        />
                        <InputError :message="errors.code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="curriculum-year">Effective year</Label>
                        <Input
                            id="curriculum-year"
                            name="effective_year"
                            type="number"
                            min="1900"
                            max="9999"
                            :default-value="curriculum?.effective_year"
                            placeholder="2026"
                            class="h-10"
                        />
                        <InputError :message="errors.effective_year" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="curriculum-status">Status</Label>
                        <Input
                            id="curriculum-status"
                            name="status"
                            :default-value="curriculum?.status"
                            placeholder="Active"
                            class="h-10"
                        />
                        <InputError :message="errors.status" />
                    </div>
                </div>

                <div class="grid max-w-4xl gap-2">
                    <Label for="curriculum-name">Name</Label>
                    <Input
                        id="curriculum-name"
                        name="name"
                        :default-value="curriculum?.name"
                        placeholder="BSCS Curriculum 2026"
                        class="h-10"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid max-w-4xl gap-2">
                    <Label for="curriculum-description">Description</Label>
                    <textarea
                        id="curriculum-description"
                        name="description"
                        :value="curriculum?.description ?? ''"
                        rows="4"
                        class="flex min-h-32 w-full resize-y rounded-md border border-input bg-transparent px-3 py-2.5 text-sm leading-6 text-foreground shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        placeholder="Optional curriculum description"
                    />
                    <InputError :message="errors.description" />
                </div>
            </div>

            <div
                id="subjects-panel"
                v-show="activeTab === 'subjects'"
                role="tabpanel"
                class="space-y-5 p-5 sm:p-6"
            >
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="space-y-1">
                        <h2 class="text-base font-semibold">
                            Curriculum subjects
                        </h2>
                        <p class="text-sm leading-6 text-muted-foreground">
                            Assign subjects by grade level and academic period.
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addSubject"
                    >
                        <Plus aria-hidden="true" />
                        Add subject
                    </Button>
                </div>

                <InputError :message="errors.curriculum_subjects" />

                <p
                    v-if="curriculumSubjects.length === 0"
                    class="rounded-xl border border-dashed bg-muted/10 px-5 py-12 text-center text-sm text-muted-foreground"
                >
                    No subjects added yet. Use “Add subject” to get started.
                </p>

                <div
                    v-for="(subject, subjectIndex) in curriculumSubjects"
                    :key="subject.key"
                    class="grid gap-4 rounded-xl border bg-muted/10 p-4 shadow-xs md:grid-cols-12"
                >
                    <div class="grid gap-2 md:col-span-3">
                        <Label :for="`subject-${subject.key}`">Subject</Label>
                        <select
                            :id="`subject-${subject.key}`"
                            v-model="subject.subject_id"
                            :name="`curriculum_subjects[${subjectIndex}][subject_id]`"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20"
                        >
                            <option value="">Select subject</option>
                            <option
                                v-for="option in subjects"
                                :key="option.id"
                                :value="option.id"
                            >
                                {{ option.name }}
                            </option>
                        </select>
                        <InputError
                            :message="
                                subjectError(errors, subjectIndex, 'subject_id')
                            "
                        />
                    </div>

                    <div class="grid gap-2 md:col-span-3">
                        <Label :for="`year-level-${subject.key}`">
                            Grade level
                        </Label>
                        <select
                            :id="`year-level-${subject.key}`"
                            v-model="subject.year_level_id"
                            :name="`curriculum_subjects[${subjectIndex}][year_level_id]`"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20"
                        >
                            <option value="">Select grade level</option>
                            <option
                                v-for="option in yearLevels"
                                :key="option.id"
                                :value="option.id"
                            >
                                {{ option.name }}
                            </option>
                        </select>
                        <InputError
                            :message="
                                subjectError(
                                    errors,
                                    subjectIndex,
                                    'year_level_id',
                                )
                            "
                        />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label :for="`academic-period-${subject.key}`">
                            Academic period
                        </Label>
                        <select
                            :id="`academic-period-${subject.key}`"
                            v-model="subject.academic_period_id"
                            :name="`curriculum_subjects[${subjectIndex}][academic_period_id]`"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20"
                        >
                            <option value="">Select academic period</option>
                            <option
                                v-for="option in academicPeriods"
                                :key="option.id"
                                :value="option.id"
                            >
                                {{ option.name }} ·
                                {{ option.structure?.name }} ({{
                                    option.structure?.type
                                }})
                            </option>
                        </select>
                        <InputError
                            :message="
                                subjectError(
                                    errors,
                                    subjectIndex,
                                    'academic_period_id',
                                )
                            "
                        />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label :for="`required-${subject.key}`">Type</Label>
                        <select
                            :id="`required-${subject.key}`"
                            v-model="subject.is_required"
                            :name="`curriculum_subjects[${subjectIndex}][is_required]`"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20"
                        >
                            <option :value="1">Required</option>
                            <option :value="0">Optional</option>
                        </select>
                        <InputError
                            :message="
                                subjectError(
                                    errors,
                                    subjectIndex,
                                    'is_required',
                                )
                            "
                        />
                    </div>

                    <div class="grid gap-2 md:col-span-1">
                        <Label :for="`sort-${subject.key}`">Order</Label>
                        <Input
                            :id="`sort-${subject.key}`"
                            v-model.number="subject.sort_order"
                            :name="`curriculum_subjects[${subjectIndex}][sort_order]`"
                            type="number"
                            min="0"
                            class="h-10"
                        />
                        <InputError
                            :message="
                                subjectError(errors, subjectIndex, 'sort_order')
                            "
                        />
                    </div>

                    <div class="flex items-end md:col-span-1">
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            class="text-muted-foreground hover:text-destructive"
                            aria-label="Remove subject"
                            @click="removeSubject(subjectIndex)"
                        >
                            <Trash2 aria-hidden="true" />
                        </Button>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-5 py-4 sm:flex-row sm:justify-end sm:px-6"
            >
                <Button as-child type="button" variant="outline">
                    <Link :href="index()" class="sm:min-w-24">Cancel</Link>
                </Button>
                <Button
                    type="submit"
                    class="sm:min-w-36"
                    :disabled="processing"
                >
                    {{
                        processing
                            ? 'Saving...'
                            : curriculum
                              ? 'Save changes'
                              : 'Create curriculum'
                    }}
                </Button>
            </div>
        </Form>
    </div>
</template>
