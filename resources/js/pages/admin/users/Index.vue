<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus, Sparkles, UsersRound } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import DataTableContainer from '@/components/DataTableContainer.vue';
import PageHero from '@/components/PageHero.vue';
import { Button } from '@/components/ui/button';
import DefaultContainer from '@/components/ui/containers/DefaultContainer.vue';
import {
    DataTable,
    DataTablePageSizeSelect,
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { createColumns } from '@/modules/users/components/columns';
import type { User } from '@/modules/users/components/columns';
import UserFormDialog from '@/modules/users/components/UserFormDialog.vue';
import type { UserRoleOption } from '@/modules/users/components/UserFormDialog.vue';
import { destroy, index } from '@/routes/admin/users';
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
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedUser = ref<User | null>(null);
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchUsers = (
    perPage: string | number | undefined = props.users.per_page,
): void => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
                per_page: perPage,
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
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (user: User): void => {
    selectedUser.value = user;
    isDeleteDialogOpen.value = true;
};

const openCreateDialog = (): void => {
    selectedUser.value = null;
    isFormDialogOpen.value = true;
};

const columns = createColumns({
    edit: openEditSheet,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('User deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (): void => {
    toast.error('Unable to delete the user. Please try again.');
};

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(fetchUsers, 300);
});

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedUser.value = null;
    }
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Users" />

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
            <Button type="button" @click="openCreateDialog">
                <Plus aria-hidden="true" />
                Create user
            </Button>
        </template>
    </PageHero>

    <DefaultContainer>
        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="users.total"
                item-label="user"
                search-placeholder="Search by name or email..."
                search-label="Search users"
                class="border-b px-4 py-4 sm:px-5"
            >
                <template #filters>
                    <DataTablePageSizeSelect
                        :model-value="users.per_page"
                        @update:model-value="fetchUsers"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="users.data"
                empty-message="No users found."
            />

            <template #footer>
                <DataTablePagination
                    :from="users.from"
                    :to="users.to"
                    :total="users.total"
                    :links="users.links"
                    :previous-page-url="users.prev_page_url"
                    :next-page-url="users.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <UserFormDialog
        v-model:open="isFormDialogOpen"
        :user="selectedUser"
        :roles="roles"
    />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedUser">
            <Form
                v-bind="destroy.form(selectedUser.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete user?</DialogTitle>
                    <DialogDescription>
                        The <strong>{{ selectedUser.name }}</strong> account
                        will be permanently deleted. This action cannot be
                        undone.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="processing"
                        >
                            Cancel
                        </Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Deleting...' : 'Delete user' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
