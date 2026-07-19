<script setup lang="ts">
import { computed } from 'vue';
import { DataTable } from '@/components/ui/data-table';
import type { Role } from './columns';
import { createColumns } from './columns';

const props = defineProps<{
    roles: Role[];
}>();

const emit = defineEmits<{
    edit: [role: Role];
    delete: [role: Role];
}>();

const columns = computed(() =>
    createColumns({
        edit: (role) => emit('edit', role),
        delete: (role) => emit('delete', role),
    }),
);
</script>

<template>
    <DataTable
        :columns="columns"
        :data="props.roles"
        empty-message="No roles found."
    />
</template>
