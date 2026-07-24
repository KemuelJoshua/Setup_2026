<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    BookOpen,
    CalendarRange,
    Check,
    FilePlus2,
    GraduationCap,
    Info,
    LockKeyhole,
    Sparkles,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
import PageHero from '@/components/PageHero.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import DefaultContainer from '@/components/ui/containers/DefaultContainer.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import { index, store } from '@/routes/admin/academics/curriculum';

import type {
    AcademicStructureOption,
    ProgramOption,
    SelectOption,
} from './columns';

const props = defineProps<{
    educationalLevels: SelectOption[];
    programs: ProgramOption[];
    academicStructures: AcademicStructureOption[];
}>();

const educationalLevelId = ref('');
const programId = ref('');
const academicStructureId = ref('');
const filteredPrograms = computed(() => {
    if (!educationalLevelId.value) {
        return [];
    }

    return props.programs.filter(
        (program) =>
            program.educational_level_id === Number(educationalLevelId.value),
    );
});
const filteredAcademicStructures = computed(() => {
    if (!educationalLevelId.value) {
        return [];
    }

    return props.academicStructures.filter(
        (structure) =>
            structure.educational_level_id === Number(educationalLevelId.value),
    );
});

watch(educationalLevelId, () => {
    programId.value = '';
    academicStructureId.value = '';
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Curricula',
                href: index(),
            },
            {
                title: 'Create',
                href: '#',
            },
        ],
    },
});
</script>

<template>
    <Head title="Create curriculum" />

    <DefaultContainer>
        <PageHero>
            <template #icon>
                <FilePlus2 class="size-5" aria-hidden="true" />
            </template>
            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Curriculum setup
            </template>
            <template #title>Create a curriculum</template>
            <template #description>
                Define the academic scope and structure first. You will organize
                subjects by year and term next.
            </template>
            <template #actions>
                <Button variant="outline" as-child>
                    <Link :href="index()">
                        <ArrowLeft aria-hidden="true" />
                        All curricula
                    </Link>
                </Button>
            </template>
        </PageHero>

        <ol
            aria-label="Curriculum creation progress"
            class="grid overflow-hidden rounded-xl border bg-card shadow-sm sm:grid-cols-2"
        >
            <li
                aria-current="step"
                class="flex items-center gap-3 border-b bg-primary/5 px-5 py-4 sm:border-r sm:border-b-0"
            >
                <span
                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground"
                >
                    1
                </span>
                <span>
                    <span class="block text-sm font-semibold">
                        Curriculum details
                    </span>
                    <span class="block text-xs text-muted-foreground">
                        Identity, program, and structure
                    </span>
                </span>
            </li>
            <li class="flex items-center gap-3 px-5 py-4 text-muted-foreground">
                <span
                    class="flex size-8 shrink-0 items-center justify-center rounded-full border bg-background text-sm font-semibold"
                >
                    2
                </span>
                <span>
                    <span class="block text-sm font-semibold">
                        Subject planning
                    </span>
                    <span class="block text-xs">
                        Assign subjects by year and term
                    </span>
                </span>
            </li>
        </ol>

        <Form
            v-bind="store.form()"
            v-slot="{ errors, processing }"
            class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]"
            :options="{ preserveScroll: true }"
            @error="toast.error('Please fix the validation errors.')"
        >
            <div class="flex min-w-0 flex-col gap-6">
                <Card>
                    <CardHeader class="border-b">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                            >
                                <BookOpen class="size-4" aria-hidden="true" />
                            </div>
                            <div>
                                <CardTitle>Identity</CardTitle>
                                <CardDescription class="mt-1">
                                    Give this curriculum a clear, recognizable
                                    name and code.
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="grid gap-5 pt-6 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="curriculum-name">
                                Curriculum name *
                            </Label>
                            <Input
                                id="curriculum-name"
                                name="name"
                                placeholder="e.g. BSIT Curriculum 2026"
                                autofocus
                            />
                            <p class="text-xs text-muted-foreground">
                                Use a name staff can quickly identify.
                            </p>
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="curriculum-code">
                                Curriculum code *
                            </Label>
                            <Input
                                id="curriculum-code"
                                name="code"
                                placeholder="e.g. BSIT-2026"
                            />
                            <p class="text-xs text-muted-foreground">
                                Keep it short and unique.
                            </p>
                            <InputError :message="errors.code" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="border-b">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400"
                            >
                                <GraduationCap
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </div>
                            <div>
                                <CardTitle>Academic scope</CardTitle>
                                <CardDescription class="mt-1">
                                    Choose an educational level first to reveal
                                    compatible programs.
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="grid gap-5 pt-6 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="educational-level">
                                Educational level *
                            </Label>
                            <Select v-model="educationalLevelId">
                                <SelectTrigger
                                    id="educational-level"
                                    class="w-full"
                                >
                                    <SelectValue
                                        placeholder="Select educational level"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="level in educationalLevels"
                                        :key="level.id"
                                        :value="String(level.id)"
                                    >
                                        {{ level.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="grid gap-2">
                            <Label for="program">Program *</Label>
                            <Select
                                v-model="programId"
                                name="program_id"
                                :disabled="!educationalLevelId"
                            >
                                <SelectTrigger id="program" class="w-full">
                                    <SelectValue
                                        :placeholder="
                                            educationalLevelId
                                                ? filteredPrograms.length
                                                    ? 'Select program'
                                                    : 'No programs available'
                                                : 'Select educational level first'
                                        "
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="program in filteredPrograms"
                                        :key="program.id"
                                        :value="String(program.id)"
                                    >
                                        {{ program.code }} — {{ program.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.program_id" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="border-b">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400"
                            >
                                <CalendarRange
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </div>
                            <div>
                                <CardTitle>Schedule and status</CardTitle>
                                <CardDescription class="mt-1">
                                    Set when this plan applies and how it should
                                    appear to staff.
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="grid gap-5 pt-6 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="effective-year">
                                Effective year *
                            </Label>
                            <Input
                                id="effective-year"
                                name="effective_year"
                                type="number"
                                min="1900"
                                max="9999"
                                placeholder="2026"
                            />
                            <InputError :message="errors.effective_year" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="number-of-years">
                                Number of years *
                            </Label>
                            <Input
                                id="number-of-years"
                                name="number_of_years"
                                type="number"
                                min="1"
                                max="10"
                                placeholder="4"
                            />
                            <InputError :message="errors.number_of_years" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">Initial status *</Label>
                            <Select name="status" default-value="Draft">
                                <SelectTrigger id="status" class="w-full">
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Draft">
                                        Draft
                                    </SelectItem>
                                    <SelectItem value="Active">
                                        Active
                                    </SelectItem>
                                    <SelectItem value="Inactive">
                                        Inactive
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-xs text-muted-foreground">
                                Draft is recommended while adding subjects.
                            </p>
                            <InputError :message="errors.status" />
                        </div>

                        <div class="grid gap-2 sm:col-span-2">
                            <Label for="description">Remarks</Label>
                            <Textarea
                                id="description"
                                name="description"
                                rows="3"
                                class="resize-y"
                                placeholder="Add optional notes for staff"
                            />
                            <InputError :message="errors.description" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <aside class="flex flex-col gap-4 lg:sticky lg:top-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <CardTitle>Academic structure</CardTitle>
                                <CardDescription class="mt-1">
                                    Required for subject planning
                                </CardDescription>
                            </div>
                            <Badge variant="outline">Required</Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-if="filteredAcademicStructures.length"
                            class="grid gap-3"
                        >
                            <label
                                v-for="structure in filteredAcademicStructures"
                                :key="structure.id"
                                class="group flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition-all hover:border-primary/60 hover:bg-muted/30 has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:ring-1 has-[:checked]:ring-primary/20"
                            >
                                <input
                                    v-model="academicStructureId"
                                    type="radio"
                                    name="academic_term_structure_id"
                                    :value="structure.id"
                                    class="mt-1 size-4 accent-primary"
                                />
                                <span class="min-w-0 flex-1">
                                    <span
                                        class="block text-sm leading-5 font-medium"
                                    >
                                        {{ structure.name }}
                                    </span>
                                    <span
                                        class="mt-1 flex items-center gap-1.5 text-xs text-muted-foreground"
                                    >
                                        <CalendarRange
                                            class="size-3.5"
                                            aria-hidden="true"
                                        />
                                        {{ structure.root_periods.length }}
                                        academic terms
                                    </span>
                                </span>
                                <Check
                                    class="size-4 text-primary opacity-0 transition-opacity group-has-[:checked]:opacity-100"
                                    aria-hidden="true"
                                />
                            </label>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center gap-3 rounded-xl border border-dashed px-4 py-8 text-center"
                        >
                            <Info
                                class="size-5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <p class="text-sm text-muted-foreground">
                                {{
                                    educationalLevelId
                                        ? 'No academic structures are available for this educational level.'
                                        : 'Select an educational level to view compatible structures.'
                                }}
                            </p>
                        </div>
                        <InputError
                            :message="errors.academic_term_structure_id"
                        />

                        <div
                            class="flex items-start gap-3 rounded-lg border border-amber-500/30 bg-amber-500/5 p-3 text-xs leading-5"
                        >
                            <LockKeyhole
                                class="mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-400"
                                aria-hidden="true"
                            />
                            <p>
                                The selected structure is locked after creation
                                because it controls the year and term layout.
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="space-y-4 p-4">
                        <div class="flex items-start gap-3 text-sm">
                            <div
                                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"
                            >
                                <BookOpen class="size-4" aria-hidden="true" />
                            </div>
                            <div>
                                <p class="font-medium">What happens next?</p>
                                <p
                                    class="mt-1 text-xs leading-5 text-muted-foreground"
                                >
                                    After saving, you will continue to the
                                    subject planner for this curriculum.
                                </p>
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Button
                                type="submit"
                                size="lg"
                                class="w-full"
                                :disabled="processing"
                            >
                                <Check aria-hidden="true" />
                                {{
                                    processing
                                        ? 'Creating curriculum...'
                                        : 'Create and continue'
                                }}
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="ghost"
                                class="w-full"
                            >
                                <Link :href="index()">Cancel</Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </aside>
        </Form>
    </DefaultContainer>
</template>
