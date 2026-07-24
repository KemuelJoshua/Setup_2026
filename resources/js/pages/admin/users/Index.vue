<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Plus, Sparkles, UsersRound } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import PageHero from '@/components/PageHero.vue';
import { Button } from '@/components/ui/button';
import {
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import type { User } from '@/modules/users/components/columns';
import UserDeleteDialog from '@/modules/users/components/UserDeleteDialog.vue';
import UserFormSheet from '@/modules/users/components/UserFormSheet.vue';
import type { UserRoleOption } from '@/modules/users/components/UserFormSheet.vue';
import UserTable from '@/modules/users/components/UserTable.vue';
import { index } from '@/routes/admin/users';
import type { LengthAwarePaginator } from '@/types';

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
    roles: UserRoleOption[];
    filters: {
        search?: string;
        per_page?: string | number;
    };
}>();

const searchQuery = ref(props.filters.search ?? '');
const isCreateSheetOpen = ref(false);
const isEditSheetOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedUser = ref<User | null>(null);
const userPendingDeletion = ref<User | null>(null);
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

const openEditSheet = (user: User): void => {
    selectedUser.value = user;
    isEditSheetOpen.value = true;
};

const openDeleteDialog = (user: User): void => {
    userPendingDeletion.value = user;
    isDeleteDialogOpen.value = true;
};

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(fetchUsers, 300);
});

watch(isEditSheetOpen, (isOpen) => {
    if (!isOpen) {
        selectedUser.value = null;
    }
});

watch(isDeleteDialogOpen, (isOpen) => {
    if (!isOpen) {
        userPendingDeletion.value = null;
    }
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-8">
        <PageHero>
            <template #icon>
                <UsersRound class="size-5" aria-hidden="true" />
            </template>
            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Access management
            </template>
            <template #title>User workspace</template>
            <template #description>
                Create user accounts, assign roles, and manage access from one
                organized workspace.
            </template>
            <template #actions>
                <UserFormSheet
                    v-model:open="isCreateSheetOpen"
                    mode="create"
                    :roles="roles"
                >
                    <template #trigger>
                        <Button type="button">
                            <Plus aria-hidden="true" />
                            Create user
                        </Button>
                    </template>
                </UserFormSheet>
            </template>
        </PageHero>

        <DataTableToolbar
            v-model="searchQuery"
            :count="users.total"
            item-label="user"
            search-placeholder="Search by name or email..."
            search-label="Search users"
        >
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <UserTable
                :users="users.data"
                @edit="openEditSheet"
                @delete="openDeleteDialog"
            />

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

    <UserFormSheet
        v-model:open="isEditSheetOpen"
        mode="edit"
        :user="selectedUser"
        :roles="roles"
    />

    <UserDeleteDialog
        v-model:open="isDeleteDialogOpen"
        :user="userPendingDeletion"
    />
</template>
