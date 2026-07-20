<script setup lang="ts">
import { computed } from 'vue';
import { DataTable } from '@/components/ui/data-table';
import type { SchoolYear } from './columns';
import { createColumns } from './columns';

const props = defineProps<{
    schoolYears: SchoolYear[];
}>();

const emit = defineEmits<{
    edit: [schoolYear: SchoolYear];
    delete: [schoolYear: SchoolYear];
}>();

const columns = computed(() =>
    createColumns({
        edit: (schoolYear) => emit('edit', schoolYear),
        delete: (schoolYear) => emit('delete', schoolYear),
    }),
);
</script>

<template>
    <DataTable
        :columns="columns"
        :data="props.schoolYears"
        empty-message="No school years found."
    />
</template>
