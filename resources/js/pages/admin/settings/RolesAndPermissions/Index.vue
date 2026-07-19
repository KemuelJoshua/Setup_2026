<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import type {
    Permission,
    Role,
} from '@/modules/roles-and-permissions/components/columns';
import RoleDeleteDialog from '@/modules/roles-and-permissions/components/RoleDeleteDialog.vue';
import RoleFormSheet from '@/modules/roles-and-permissions/components/RoleFormSheet.vue';
import RoleTable from '@/modules/roles-and-permissions/components/RoleTable.vue';
import { index } from '@/routes/admin/settings/roles';
import type { LengthAwarePaginator } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Roles & Permissions',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    roles: LengthAwarePaginator<Role>;
    guards: string[];
    permissions: Permission[];
    filters: {
        search: string;
        guard: string;
    };
}>();

const searchQuery = ref(props.filters.search);
const selectedGuard = ref(props.filters.guard);
const isCreateSheetOpen = ref(false);
const isEditSheetOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedRole = ref<Role | null>(null);
const rolePendingDeletion = ref<Role | null>(null);
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const applyFilters = (): void => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
                guard:
                    selectedGuard.value === 'all'
                        ? undefined
                        : selectedGuard.value,
            },
        }),
        {
            only: ['roles', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const openEditSheet = (role: Role): void => {
    selectedRole.value = role;
    isEditSheetOpen.value = true;
};

const openDeleteDialog = (role: Role): void => {
    rolePendingDeletion.value = role;
    isDeleteDialogOpen.value = true;
};

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(applyFilters, 300);
});

watch(selectedGuard, () => {
    window.clearTimeout(searchTimer);
    applyFilters();
});

watch(isEditSheetOpen, (isOpen) => {
    if (!isOpen) {
        selectedRole.value = null;
    }
});

watch(isDeleteDialogOpen, (isOpen) => {
    if (!isOpen) {
        rolePendingDeletion.value = null;
    }
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Roles & Permissions" />

    <div class="flex flex-1 flex-col gap-5">
        <DataTableToolbar
            v-model="searchQuery"
            title="Roles & permissions"
            description="Manage access levels and the guards assigned to each role."
            :count="roles.total"
            item-label="role"
            search-placeholder="Search roles..."
            search-label="Search roles"
        >
            <template #filters>
                <label class="relative block sm:w-36">
                    <span class="sr-only">Filter roles by guard</span>
                    <select
                        v-model="selectedGuard"
                        class="h-9 w-full appearance-none rounded-lg border border-input bg-background px-3 pr-8 text-sm shadow-xs transition-[border-color,box-shadow,background-color] outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                    >
                        <option
                            v-for="guard in ['all', ...guards]"
                            :key="guard"
                            :value="guard"
                        >
                            {{ guard === 'all' ? 'All guards' : guard }}
                        </option>
                    </select>
                    <svg
                        aria-hidden="true"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="pointer-events-none absolute top-1/2 right-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </label>
            </template>

            <template #actions>
                <RoleFormSheet
                    v-model:open="isCreateSheetOpen"
                    mode="create"
                    :permissions="permissions"
                >
                    <template #trigger>
                        <Button type="button" size="sm">
                            <Plus aria-hidden="true" />
                            Create
                        </Button>
                    </template>
                </RoleFormSheet>
            </template>
        </DataTableToolbar>

        <div class="flex flex-col gap-4">
            <RoleTable
                :roles="roles.data"
                @edit="openEditSheet"
                @delete="openDeleteDialog"
            />

            <DataTablePagination
                :from="roles.from"
                :to="roles.to"
                :total="roles.total"
                :links="roles.links"
                :previous-page-url="roles.prev_page_url"
                :next-page-url="roles.next_page_url"
            />
        </div>
    </div>

    <RoleFormSheet
        v-model:open="isEditSheetOpen"
        mode="edit"
        :role="selectedRole"
        :permissions="permissions"
    />

    <RoleDeleteDialog
        v-model:open="isDeleteDialogOpen"
        :role="rolePendingDeletion"
    />
</template>
