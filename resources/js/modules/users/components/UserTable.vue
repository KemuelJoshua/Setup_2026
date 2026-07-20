<script setup lang="ts">
import { computed } from 'vue';
import { DataTable } from '@/components/ui/data-table';
import type { User } from './columns';
import { createColumns } from './columns';

const props = defineProps<{
    users: User[];
}>();

const emit = defineEmits<{
    edit: [user: User];
    delete: [user: User];
}>();

const columns = computed(() =>
    createColumns({
        edit: (user) => emit('edit', user),
        delete: (user) => emit('delete', user),
    }),
);
</script>

<template>
    <DataTable
        :columns="columns"
        :data="props.users"
        empty-message="No users found."
    />
</template>
