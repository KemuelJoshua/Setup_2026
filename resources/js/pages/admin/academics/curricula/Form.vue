<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Check, LockKeyhole } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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

    <div
        class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 px-4 py-6 sm:px-6 md:py-8"
    >
        <div class="flex items-start gap-4">
            <div
                class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary font-semibold text-primary-foreground"
            >
                1
            </div>
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight">
                    Basic information
                </h1>
                <p class="text-sm leading-6 text-muted-foreground">
                    Create the curriculum, then add subjects in Step 2.
                </p>
            </div>
        </div>

        <Form
            v-bind="store.form()"
            v-slot="{ errors, processing }"
            class="overflow-hidden rounded-xl border bg-card shadow-sm"
            :options="{ preserveScroll: true }"
            @error="toast.error('Please fix the validation errors.')"
        >
            <div class="grid gap-6 p-5 sm:grid-cols-2 sm:p-6">
                <div class="grid gap-2">
                    <Label for="curriculum-name">Curriculum name *</Label>
                    <Input
                        id="curriculum-name"
                        name="name"
                        placeholder="BSIT Curriculum 2026"
                        autofocus
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="curriculum-code">Curriculum code *</Label>
                    <Input
                        id="curriculum-code"
                        name="code"
                        placeholder="BSIT-2026"
                    />
                    <InputError :message="errors.code" />
                </div>

                <div class="grid gap-2">
                    <Label for="educational-level">Educational level *</Label>
                    <Select v-model="educationalLevelId">
                        <SelectTrigger id="educational-level" class="w-full">
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

                <div class="grid gap-2">
                    <Label for="effective-year">Effective year *</Label>
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
                    <Label for="number-of-years">Number of years *</Label>
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
                    <Label for="status">Status *</Label>
                    <Select name="status" default-value="Draft">
                        <SelectTrigger id="status" class="w-full">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Draft">Draft</SelectItem>
                            <SelectItem value="Active">Active</SelectItem>
                            <SelectItem value="Inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.status" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label for="description">Remarks</Label>
                    <Textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="resize-y"
                        placeholder="Optional curriculum remarks"
                    />
                    <InputError :message="errors.description" />
                </div>
            </div>

            <div class="space-y-4 border-t p-5 sm:p-6">
                <div>
                    <h2 class="font-semibold">Academic structure *</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        This determines the year and term layout in Step 2.
                    </p>
                </div>

                <div
                    v-if="filteredAcademicStructures.length"
                    class="grid gap-3 sm:grid-cols-2"
                >
                    <label
                        v-for="structure in filteredAcademicStructures"
                        :key="structure.id"
                        class="group flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition-colors hover:border-primary/60 has-[:checked]:border-primary has-[:checked]:bg-primary/5"
                    >
                        <input
                            v-model="academicStructureId"
                            type="radio"
                            name="academic_term_structure_id"
                            :value="structure.id"
                            class="mt-1 size-4 accent-primary"
                        />
                        <span class="min-w-0">
                            <span class="block text-sm font-medium">
                                {{ structure.name }}
                            </span>
                            <span
                                class="mt-1 block text-xs text-muted-foreground"
                            >
                                {{ structure.root_periods.length }} academic
                                terms
                            </span>
                        </span>
                    </label>
                </div>
                <p
                    v-else
                    class="rounded-lg border border-dashed px-4 py-8 text-center text-sm text-muted-foreground"
                >
                    {{
                        educationalLevelId
                            ? 'No academic structures are available for this educational level.'
                            : 'Select an educational level to view academic structures.'
                    }}
                </p>
                <InputError :message="errors.academic_term_structure_id" />

                <div
                    class="flex items-start gap-3 rounded-lg border border-amber-500/30 bg-amber-500/5 p-4 text-sm"
                >
                    <LockKeyhole
                        class="mt-0.5 size-4 shrink-0 text-amber-600"
                        aria-hidden="true"
                    />
                    <p>
                        Once saved, this curriculum is permanently tied to the
                        selected academic structure.
                    </p>
                </div>
            </div>

            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-5 py-4 sm:flex-row sm:justify-end sm:px-6"
            >
                <Button as-child type="button" variant="outline">
                    <Link :href="index()">Cancel</Link>
                </Button>
                <Button type="submit" :disabled="processing">
                    <Check aria-hidden="true" />
                    {{ processing ? 'Saving...' : 'Save and continue' }}
                </Button>
            </div>
        </Form>
    </div>
</template>
