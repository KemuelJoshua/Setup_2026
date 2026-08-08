<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    BookOpen,
    ChevronRight,
    Clock3,
    GraduationCap,
    Layers3,
    Pencil,
    Plus,
    Trash2,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
import PageHero from '@/components/PageHero.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import DefaultContainer from '@/components/ui/containers/DefaultContainer.vue';
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
const isDeleteDialogOpen = ref(false);
const selectedSubject = ref<CurriculumSubject | null>(null);
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
const totalUnits = computed(() =>
    props.curriculum.curriculum_subjects.reduce(
        (total, subject) => total + Number(subject.units ?? 0),
        0,
    ),
);
const totalContactHours = computed(() =>
    props.curriculum.curriculum_subjects.reduce(
        (total, subject) =>
            total +
            Number(subject.lecture_hours ?? 0) +
            Number(subject.laboratory_hours ?? 0),
        0,
    ),
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

const openDeleteDialog = (subject: CurriculumSubject): void => {
    selectedSubject.value = subject;
    isDeleteDialogOpen.value = true;
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

const handleDeleted = (): void => {
    toast.success('Subject removed successfully.');
    isDeleteDialogOpen.value = false;
    selectedSubject.value = null;
};
</script>

<template>
    <Head :title="`${curriculum.name} subjects`" />

    <PageHero>
        <template #icon>
            <BookOpen class="size-5" aria-hidden="true" />
        </template>
        <template #badge>
            <GraduationCap class="size-3.5" aria-hidden="true" />
            Subject planning
        </template>
        <template #title>{{ curriculum.name }}</template>
        <template #description>
            {{ curriculum.code }} · {{ curriculum.status }} ·
            {{ curriculum.program?.name ?? 'No program' }} · Effective
            {{ curriculum.effective_year }} ·
            {{
                curriculum.academic_structure?.name ??
                'No academic structure assigned'
            }}
        </template>
        <template #actions>
            <div class="flex flex-wrap gap-2">
                <Button variant="outline" as-child>
                    <Link :href="index()">
                        <ArrowLeft aria-hidden="true" />
                        All curricula
                    </Link>
                </Button>
                <Button
                    :disabled="!curriculum.academic_structure"
                    @click="openAddDialog()"
                >
                    <Plus aria-hidden="true" />
                    Add subject
                </Button>
            </div>
        </template>
    </PageHero>

    <DefaultContainer>
        <div class="grid gap-3 sm:grid-cols-3">
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    >
                        <Layers3 class="size-4" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ curriculum.curriculum_subjects.length }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Assigned subjects
                        </p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400"
                    >
                        <BookOpen class="size-4" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ totalUnits.toFixed(2) }}
                        </p>
                        <p class="text-xs text-muted-foreground">Total units</p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400"
                    >
                        <Clock3 class="size-4" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ totalContactHours.toFixed(2) }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Contact hours
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div
            v-if="availableYearLevels.length === 0"
            class="rounded-xl border border-dashed p-10 text-center text-sm text-muted-foreground"
        >
            No compatible year levels are configured for this curriculum.
        </div>

        <nav
            v-else
            aria-label="Year level navigation"
            class="sticky top-3 z-10 flex gap-2 overflow-x-auto rounded-xl border bg-background/95 p-2 shadow-sm backdrop-blur supports-[backdrop-filter]:bg-background/80"
        >
            <Button
                v-for="yearLevel in availableYearLevels"
                :key="yearLevel.id"
                variant="ghost"
                size="sm"
                as-child
                class="shrink-0"
            >
                <a :href="`#year-level-${yearLevel.id}`">
                    {{ yearLevel.name }}
                    <Badge variant="secondary">
                        {{
                            curriculum.curriculum_subjects.filter(
                                (subject) =>
                                    subject.year_level_id === yearLevel.id,
                            ).length
                        }}
                    </Badge>
                </a>
            </Button>
        </nav>

        <details
            v-for="yearLevel in availableYearLevels"
            :key="yearLevel.id"
            :id="`year-level-${yearLevel.id}`"
            open
            class="group scroll-mt-24 overflow-hidden rounded-xl border bg-card shadow-sm"
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
                <Badge variant="secondary" class="ml-auto">
                    {{
                        curriculum.curriculum_subjects.filter(
                            (subject) => subject.year_level_id === yearLevel.id,
                        ).length
                    }}
                    subjects
                </Badge>
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
                            <Badge variant="outline">
                                {{ subjectsFor(yearLevel.id, term.id).length }}
                            </Badge>
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
                            v-if="
                                subjectsFor(yearLevel.id, term.id).length === 0
                            "
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
                            class="group/subject flex items-start justify-between gap-4 px-4 py-3 transition-colors hover:bg-muted/30"
                        >
                            <div class="min-w-0">
                                <h3 class="text-sm font-medium">
                                    {{ subject.subject_name }}
                                </h3>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ subject.units ?? '0.00' }} units ·
                                    {{ subject.lecture_hours ?? '0.00' }}
                                    lecture ·
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
                                <Button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    class="text-destructive"
                                    aria-label="Remove subject"
                                    @click="openDeleteDialog(subject)"
                                >
                                    <Trash2 aria-hidden="true" />
                                </Button>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </details>
    </DefaultContainer>

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
                        <Select v-model="draft.subject_id" name="subject_id">
                            <SelectTrigger id="course" class="w-full">
                                <SelectValue placeholder="Select course" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="subject in subjects"
                                    :key="subject.id"
                                    :value="subject.id"
                                >
                                    {{ subject.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.subject_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="year-level">Year level *</Label>
                        <Select
                            v-model="draft.year_level_id"
                            name="year_level_id"
                        >
                            <SelectTrigger id="year-level" class="w-full">
                                <SelectValue placeholder="Select year level" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="yearLevel in availableYearLevels"
                                    :key="yearLevel.id"
                                    :value="yearLevel.id"
                                >
                                    {{ yearLevel.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.year_level_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="academic-term">Academic term *</Label>
                        <Select
                            v-model="draft.academic_period_id"
                            name="academic_period_id"
                        >
                            <SelectTrigger id="academic-term" class="w-full">
                                <SelectValue
                                    placeholder="Select academic term"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="term in terms"
                                    :key="term.id"
                                    :value="term.id"
                                >
                                    {{ term.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
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
                        <Label for="laboratory-hours"> Laboratory hours </Label>
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
                            <Checkbox
                                :model-value="
                                    draft.prerequisite_ids.includes(
                                        candidate.id,
                                    )
                                "
                                @update:model-value="
                                    toggleReference(
                                        'prerequisite_ids',
                                        candidate.id,
                                        $event === true,
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
                            <Checkbox
                                :model-value="
                                    draft.corequisite_ids.includes(candidate.id)
                                "
                                @update:model-value="
                                    toggleReference(
                                        'corequisite_ids',
                                        candidate.id,
                                        $event === true,
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
                    <Textarea
                        id="remarks"
                        v-model="draft.remarks"
                        name="remarks"
                        rows="3"
                        class="resize-y"
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

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedSubject">
            <Form
                v-bind="
                    destroy.form({
                        curriculum: curriculum.id,
                        curriculumSubject: selectedSubject.id,
                    })
                "
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="toast.error('Unable to remove the subject.')"
            >
                <DialogHeader>
                    <DialogTitle>Remove subject?</DialogTitle>
                    <DialogDescription>
                        {{ selectedSubject.subject_name }} will be removed from
                        this curriculum. Any prerequisite or corequisite links
                        to it may also be affected.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Removing...' : 'Remove subject' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
