<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    BookOpen,
    ChevronRight,
    GraduationCap,
    Pencil,
    Plus,
    Trash2,
} from '@lucide/vue';
import { computed, ref } from 'vue';
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
import { index } from '@/routes/admin/academics/curriculum';
import {
    destroy,
    store,
    update,
} from '@/routes/admin/academics/curriculum/subjects';

import type {
    CurriculumFormData,
    CurriculumSubject,
    SelectOption,
} from './columns';

interface SubjectDraft {
    subject_id: number | '';
    year_level_id: number | '';
    academic_period_id: number | '';
    units: string;
    lecture_hours: string;
    laboratory_hours: string;
    sort_order: number;
    remarks: string;
    prerequisite_ids: number[];
    corequisite_ids: number[];
}

const props = defineProps<{
    curriculum: CurriculumFormData;
    subjects: SelectOption[];
    yearLevels: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Curricula', href: index() },
            { title: 'Curriculum subjects', href: '#' },
        ],
    },
});

const isDialogOpen = ref(false);
const editingSubject = ref<CurriculumSubject | null>(null);
const dialogKey = ref(0);

const emptyDraft = (): SubjectDraft => ({
    subject_id: '',
    year_level_id: '',
    academic_period_id: '',
    units: '',
    lecture_hours: '',
    laboratory_hours: '',
    sort_order: props.curriculum.curriculum_subjects.length + 1,
    remarks: '',
    prerequisite_ids: [],
    corequisite_ids: [],
});

const draft = ref<SubjectDraft>(emptyDraft());

const structureCode = computed(
    () => props.curriculum.academic_structure?.code ?? null,
);

const availableYearLevels = computed(() => {
    const names =
        structureCode.value === 'JHS4Q'
            ? ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10']
            : structureCode.value === 'SHS4Q'
              ? ['Grade 11', 'Grade 12']
              : Array.from(
                    { length: props.curriculum.number_of_years },
                    (_, index) => `Year ${index + 1}`,
                );

    return props.yearLevels.filter((level) =>
        names.slice(0, props.curriculum.number_of_years).includes(level.name),
    );
});

const terms = computed(
    () => props.curriculum.academic_structure?.root_periods ?? [],
);

const prerequisiteCandidates = computed(() =>
    props.curriculum.curriculum_subjects.filter(
        (subject) => subject.id !== editingSubject.value?.id,
    ),
);

const formAttributes = computed(() =>
    editingSubject.value
        ? update.form({
              curriculum: props.curriculum.id,
              curriculumSubject: editingSubject.value.id,
          })
        : store.form(props.curriculum.id),
);

const subjectsFor = (
    yearLevelId: number,
    academicPeriodId: number,
): CurriculumSubject[] =>
    props.curriculum.curriculum_subjects.filter(
        (subject) =>
            subject.year_level_id === yearLevelId &&
            subject.academic_period_id === academicPeriodId,
    );

const openAddDialog = (
    yearLevelId?: number,
    academicPeriodId?: number,
): void => {
    editingSubject.value = null;
    draft.value = {
        ...emptyDraft(),
        year_level_id: yearLevelId ?? '',
        academic_period_id: academicPeriodId ?? '',
    };
    dialogKey.value++;
    isDialogOpen.value = true;
};

const openEditDialog = (subject: CurriculumSubject): void => {
    editingSubject.value = subject;
    draft.value = {
        subject_id: subject.subject_id,
        year_level_id: subject.year_level_id,
        academic_period_id: subject.academic_period_id,
        units: subject.units ?? '',
        lecture_hours: subject.lecture_hours ?? '',
        laboratory_hours: subject.laboratory_hours ?? '',
        sort_order: subject.sort_order,
        remarks: subject.remarks ?? '',
        prerequisite_ids: subject.prerequisites.map((item) => item.id),
        corequisite_ids: subject.corequisites.map((item) => item.id),
    };
    dialogKey.value++;
    isDialogOpen.value = true;
};

const toggleReference = (
    field: 'prerequisite_ids' | 'corequisite_ids',
    id: number,
    checked: boolean,
): void => {
    draft.value[field] = checked
        ? [...draft.value[field], id]
        : draft.value[field].filter((value) => value !== id);
};

const handleSaved = (): void => {
    toast.success(
        editingSubject.value
            ? 'Subject updated successfully.'
            : 'Subject added successfully.',
    );
    isDialogOpen.value = false;
};
</script>

<template>
    <Head :title="`${curriculum.name} subjects`" />

    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-4 md:p-8">
        <div
            class="flex flex-col gap-4 rounded-xl border bg-card p-5 shadow-sm sm:flex-row sm:items-start sm:justify-between"
        >
            <div class="flex gap-4">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary font-semibold text-primary-foreground"
                >
                    2
                </div>
                <div>
                    <p class="text-xs font-medium tracking-wide text-primary uppercase">
                        Curriculum subjects
                    </p>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight">
                        {{ curriculum.name }}
                    </h1>
                    <div
                        class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-sm text-muted-foreground"
                    >
                        <span>{{ curriculum.code }}</span>
                        <span>{{ curriculum.program?.name ?? 'No program' }}</span>
                        <span>Effective {{ curriculum.effective_year }}</span>
                        <span>
                            {{ curriculum.number_of_years }}
                            {{
                                curriculum.number_of_years === 1
                                    ? 'year'
                                    : 'years'
                            }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm font-medium">
                        {{
                            curriculum.academic_structure?.name ??
                            'No academic structure assigned'
                        }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        Academic structure is locked for this curriculum.
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link :href="index()">Finish</Link>
                </Button>
                <Button
                    :disabled="!curriculum.academic_structure"
                    @click="openAddDialog()"
                >
                    <Plus aria-hidden="true" />
                    Add subject
                </Button>
            </div>
        </div>

        <div
            v-if="availableYearLevels.length === 0"
            class="rounded-xl border border-dashed p-10 text-center text-sm text-muted-foreground"
        >
            No compatible year levels are configured for this curriculum.
        </div>

        <details
            v-for="yearLevel in availableYearLevels"
            :key="yearLevel.id"
            open
            class="group overflow-hidden rounded-xl border bg-card shadow-sm"
        >
            <summary
                class="flex cursor-pointer list-none items-center gap-3 border-b bg-muted/20 px-5 py-4"
            >
                <ChevronRight
                    class="size-4 transition-transform group-open:rotate-90"
                    aria-hidden="true"
                />
                <GraduationCap class="size-5 text-primary" aria-hidden="true" />
                <span class="font-semibold">{{ yearLevel.name }}</span>
            </summary>

            <div class="grid gap-4 p-4 lg:grid-cols-2">
                <section
                    v-for="term in terms"
                    :key="term.id"
                    class="overflow-hidden rounded-lg border bg-background"
                >
                    <div
                        class="flex items-center justify-between gap-3 border-b px-4 py-3"
                    >
                        <div class="flex items-center gap-2">
                            <BookOpen
                                class="size-4 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <h2 class="text-sm font-semibold">
                                {{ term.name }}
                            </h2>
                        </div>
                        <Button
                            size="sm"
                            variant="outline"
                            @click="openAddDialog(yearLevel.id, term.id)"
                        >
                            <Plus aria-hidden="true" />
                            Add subject
                        </Button>
                    </div>

                    <div class="divide-y">
                        <p
                            v-if="subjectsFor(yearLevel.id, term.id).length === 0"
                            class="px-4 py-8 text-center text-sm text-muted-foreground"
                        >
                            No subjects added.
                        </p>

                        <article
                            v-for="subject in subjectsFor(
                                yearLevel.id,
                                term.id,
                            )"
                            :key="subject.id"
                            class="flex items-start justify-between gap-4 px-4 py-3"
                        >
                            <div class="min-w-0">
                                <h3 class="text-sm font-medium">
                                    {{ subject.subject_name }}
                                </h3>
                                <p
                                    class="mt-1 text-xs text-muted-foreground"
                                >
                                    {{ subject.units ?? '0.00' }} units ·
                                    {{ subject.lecture_hours ?? '0.00' }} lecture
                                    ·
                                    {{ subject.laboratory_hours ?? '0.00' }} lab
                                </p>
                                <div
                                    v-if="subject.prerequisites.length"
                                    class="mt-2 text-xs"
                                >
                                    <span class="text-muted-foreground">
                                        Prerequisite:
                                    </span>
                                    {{
                                        subject.prerequisites
                                            .map((item) => item.name)
                                            .join(', ')
                                    }}
                                </div>
                                <div
                                    v-if="subject.corequisites.length"
                                    class="mt-1 text-xs"
                                >
                                    <span class="text-muted-foreground">
                                        Corequisite:
                                    </span>
                                    {{
                                        subject.corequisites
                                            .map((item) => item.name)
                                            .join(', ')
                                    }}
                                </div>
                                <p
                                    v-if="subject.remarks"
                                    class="mt-2 text-xs text-muted-foreground"
                                >
                                    {{ subject.remarks }}
                                </p>
                            </div>

                            <div class="flex shrink-0 gap-1">
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    aria-label="Edit subject"
                                    @click="openEditDialog(subject)"
                                >
                                    <Pencil aria-hidden="true" />
                                </Button>
                                <Form
                                    v-bind="
                                        destroy.form({
                                            curriculum: curriculum.id,
                                            curriculumSubject: subject.id,
                                        })
                                    "
                                    @success="
                                        toast.success(
                                            'Subject removed successfully.',
                                        )
                                    "
                                >
                                    <Button
                                        type="submit"
                                        size="icon"
                                        variant="ghost"
                                        class="text-destructive"
                                        aria-label="Remove subject"
                                    >
                                        <Trash2 aria-hidden="true" />
                                    </Button>
                                </Form>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </details>
    </div>

    <Dialog v-model:open="isDialogOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-3xl">
            <Form
                :key="dialogKey"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleSaved"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{ editingSubject ? 'Edit subject' : 'Add subject' }}
                    </DialogTitle>
                    <DialogDescription>
                        Assign a course to a year level and academic term.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="course">Course *</Label>
                        <select
                            id="course"
                            v-model="draft.subject_id"
                            name="subject_id"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Select course</option>
                            <option
                                v-for="subject in subjects"
                                :key="subject.id"
                                :value="subject.id"
                            >
                                {{ subject.name }}
                            </option>
                        </select>
                        <InputError :message="errors.subject_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="year-level">Year level *</Label>
                        <select
                            id="year-level"
                            v-model="draft.year_level_id"
                            name="year_level_id"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Select year level</option>
                            <option
                                v-for="yearLevel in availableYearLevels"
                                :key="yearLevel.id"
                                :value="yearLevel.id"
                            >
                                {{ yearLevel.name }}
                            </option>
                        </select>
                        <InputError :message="errors.year_level_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="academic-term">Academic term *</Label>
                        <select
                            id="academic-term"
                            v-model="draft.academic_period_id"
                            name="academic_period_id"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Select academic term</option>
                            <option
                                v-for="term in terms"
                                :key="term.id"
                                :value="term.id"
                            >
                                {{ term.name }}
                            </option>
                        </select>
                        <InputError :message="errors.academic_period_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="units">Units</Label>
                        <Input
                            id="units"
                            v-model="draft.units"
                            name="units"
                            type="number"
                            min="0"
                            step="0.25"
                        />
                        <InputError :message="errors.units" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="lecture-hours">Lecture hours</Label>
                        <Input
                            id="lecture-hours"
                            v-model="draft.lecture_hours"
                            name="lecture_hours"
                            type="number"
                            min="0"
                            step="0.25"
                        />
                        <InputError :message="errors.lecture_hours" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="laboratory-hours">
                            Laboratory hours
                        </Label>
                        <Input
                            id="laboratory-hours"
                            v-model="draft.laboratory_hours"
                            name="laboratory_hours"
                            type="number"
                            min="0"
                            step="0.25"
                        />
                        <InputError :message="errors.laboratory_hours" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="sort-order">Sort order *</Label>
                        <Input
                            id="sort-order"
                            v-model.number="draft.sort_order"
                            name="sort_order"
                            type="number"
                            min="0"
                        />
                        <InputError :message="errors.sort_order" />
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <fieldset class="space-y-3">
                        <legend class="text-sm font-medium">
                            Prerequisites
                        </legend>
                        <p
                            v-if="prerequisiteCandidates.length === 0"
                            class="text-sm text-muted-foreground"
                        >
                            Add another subject first.
                        </p>
                        <label
                            v-for="candidate in prerequisiteCandidates"
                            :key="candidate.id"
                            class="flex items-center gap-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                :checked="
                                    draft.prerequisite_ids.includes(candidate.id)
                                "
                                class="size-4 accent-primary"
                                @change="
                                    toggleReference(
                                        'prerequisite_ids',
                                        candidate.id,
                                        (
                                            $event.target as HTMLInputElement
                                        ).checked,
                                    )
                                "
                            />
                            {{ candidate.subject_name }}
                        </label>
                        <input
                            v-for="id in draft.prerequisite_ids"
                            :key="`prerequisite-${id}`"
                            type="hidden"
                            name="prerequisite_ids[]"
                            :value="id"
                        />
                        <InputError :message="errors.prerequisite_ids" />
                    </fieldset>

                    <fieldset class="space-y-3">
                        <legend class="text-sm font-medium">
                            Corequisites
                        </legend>
                        <p
                            v-if="prerequisiteCandidates.length === 0"
                            class="text-sm text-muted-foreground"
                        >
                            Add another subject first.
                        </p>
                        <label
                            v-for="candidate in prerequisiteCandidates"
                            :key="candidate.id"
                            class="flex items-center gap-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                :checked="
                                    draft.corequisite_ids.includes(candidate.id)
                                "
                                class="size-4 accent-primary"
                                @change="
                                    toggleReference(
                                        'corequisite_ids',
                                        candidate.id,
                                        (
                                            $event.target as HTMLInputElement
                                        ).checked,
                                    )
                                "
                            />
                            {{ candidate.subject_name }}
                        </label>
                        <input
                            v-for="id in draft.corequisite_ids"
                            :key="`corequisite-${id}`"
                            type="hidden"
                            name="corequisite_ids[]"
                            :value="id"
                        />
                        <InputError :message="errors.corequisite_ids" />
                    </fieldset>
                </div>

                <div class="grid gap-2">
                    <Label for="remarks">Remarks</Label>
                    <textarea
                        id="remarks"
                        v-model="draft.remarks"
                        name="remarks"
                        rows="3"
                        class="w-full resize-y rounded-md border border-input bg-transparent px-3 py-2.5 text-sm"
                    />
                    <InputError :message="errors.remarks" />
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        {{
                            processing
                                ? 'Saving...'
                                : editingSubject
                                  ? 'Save changes'
                                  : 'Add subject'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
