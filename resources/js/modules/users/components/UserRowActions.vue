<script setup lang="ts">
import { Pencil, Trash2 } from '@lucide/vue';
import { DataTableRowActions } from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import type { User } from './columns';

defineProps<{
    user: User;
}>();

defineEmits<{
    edit: [];
    delete: [];
}>();
</script>

<template>
    <DataTableRowActions :label="`Open actions for ${user.name}`">
        <DropdownMenuItem @select="$emit('edit')">
            <Pencil aria-hidden="true" />
            Edit
        </DropdownMenuItem>
        <DropdownMenuItem variant="destructive" @select="$emit('delete')">
            <Trash2 aria-hidden="true" />
            Delete
        </DropdownMenuItem>
    </DataTableRowActions>
</template>
