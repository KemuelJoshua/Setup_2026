<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onBeforeUnmount, ref, watch } from 'vue';
import {
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import type { User } from '@/modules/users/components/columns';
import UserTable from '@/modules/users/components/UserTable.vue';
import { index } from '@/routes/admin/users';
import type { LengthAwarePaginator } from '@/types';
import Create from './Create.vue';

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

const props = defineProps<{
    users: LengthAwarePaginator<User>;
    filters: {
        search?: string;
        per_page?: string | number;
    };
}>();

const searchQuery = ref(props.filters.search ?? '');
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchUsers = (): void => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
                per_page: props.users.per_page,
            },
        }),
        {
            only: ['users', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(fetchUsers, 300);
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <DataTableToolbar
            v-model="searchQuery"
            title="User Management"
            description="Manage user accounts and assign roles."
            :count="users.total"
            item-label="user"
            search-placeholder="Search by name or email..."
            search-label="Search users"
        >
            <template #actions>
                <Create />
            </template>
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <UserTable :users="users.data" />

            <DataTablePagination
                :from="users.from"
                :to="users.to"
                :total="users.total"
                :links="users.links"
                :previous-page-url="users.prev_page_url"
                :next-page-url="users.next_page_url"
            />
        </div>
    </div>
</template>
