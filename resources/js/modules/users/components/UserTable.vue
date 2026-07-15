<script setup lang="ts">
import { DataTable } from '@/components/ui/data-table';
import type { User } from './columns';
import { columns } from './columns';

defineProps<{
    users: User[];
}>();
</script>

<template>
    <DataTable
        :columns="columns"
        :data="users"
        empty-message="No users found."
    />
</template>
