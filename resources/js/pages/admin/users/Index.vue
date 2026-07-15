<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { index } from '@/routes/admin/users'
import Create from './Create.vue';
import TableToolbar from '@/components/TableToolbar.vue'; 
import { Table,  } from '@/components/ui/table'

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: index(),
            },
        ],
    },
});
</script>

<template>

    <Head title="Users" />

    <div class="p-5">
        <TableToolbar
            title="User Management"
            description="Manage user accounts and assign roles."
            search-placeholder="Search by name or email..."
            search-label="Search users">

            <template #actions>
                <Create/>
            </template>
        </TableToolbar>

        <div class="overflow-x-auto">

        </div>
    </div>

</template>
