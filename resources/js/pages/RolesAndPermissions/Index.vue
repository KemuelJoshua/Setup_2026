<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, ShieldCheck, Trash2 } from '@lucide/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import TablePagination from '@/components/TablePagination.vue';
import TableToolbar from '@/components/TableToolbar.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { destroy, index, store, update } from '@/routes/administration/roles';

interface Role {
    id: number;
    name: string;
    guard_name: string;
    permission_ids: number[];
}

interface Permission {
    id: number;
    name: string;
    category: string;
    guard_name: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedRoles {
    data: Role[];
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationLink[];
    next_page_url: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Roles & Permissions',
                href: '#',
            },
        ],
    },
});

const props = defineProps<{
    roles: PaginatedRoles;
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
const permissionSearchQuery = ref('');
let searchTimer: ReturnType<typeof setTimeout> | undefined;

const activePermissionGuard = computed(
    () => selectedRole.value?.guard_name ?? 'web',
);

const availablePermissions = computed(() =>
    props.permissions.filter(
        (permission) => permission.guard_name === activePermissionGuard.value,
    ),
);

const categorizedPermissions = computed(() => {
    const permissionsByCategory = new Map<string, Permission[]>();

    availablePermissions.value.forEach((permission) => {
        const categoryPermissions =
            permissionsByCategory.get(permission.category) ?? [];

        categoryPermissions.push(permission);
        permissionsByCategory.set(permission.category, categoryPermissions);
    });

    return Array.from(permissionsByCategory, ([category, permissions]) => ({
        category,
        permissions,
    })).sort((first, second) => first.category.localeCompare(second.category));
});

const normalizedPermissionSearch = computed(() =>
    permissionSearchQuery.value.trim().toLocaleLowerCase(),
);

const matchesPermissionSearch = (permission: Permission): boolean => {
    if (normalizedPermissionSearch.value === '') {
        return true;
    }

    return `${permission.category} ${permission.name}`
        .toLocaleLowerCase()
        .includes(normalizedPermissionSearch.value);
};

const matchingPermissionCount = computed(
    () => availablePermissions.value.filter(matchesPermissionSearch).length,
);

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
    permissionSearchQuery.value = '';
    isEditSheetOpen.value = true;
};

const openDeleteDialog = (role: Role): void => {
    rolePendingDeletion.value = role;
    isDeleteDialogOpen.value = true;
};

const closeEditSheet = (): void => {
    isEditSheetOpen.value = false;
};

const closeDeleteDialog = (): void => {
    isDeleteDialogOpen.value = false;
};

watch(searchQuery, () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(applyFilters, 300);
});

watch(selectedGuard, () => {
    window.clearTimeout(searchTimer);
    applyFilters();
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <div class="contents">
        <Head title="Roles & Permissions" />

        <div class="m-4 flex flex-1 flex-col gap-5 p-4 md:p-6">
            <TableToolbar
                v-model="searchQuery"
                title="Roles & permissions"
                description="Manage access levels and the guards assigned to each role."
                :count="roles.total"
                item-label="role"
                search-placeholder="Search roles…"
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
                    <Sheet v-model:open="isCreateSheetOpen">
                        <SheetTrigger as-child>
                            <Button type="button" size="sm">
                                <Plus aria-hidden="true" />
                                Create
                            </Button>
                        </SheetTrigger>

                        <SheetContent
                            side="right"
                            class="w-full gap-0 p-0 sm:max-w-lg"
                        >
                            <Form
                                v-bind="store.form()"
                                v-slot="{ errors, processing }"
                                reset-on-success
                                class="flex min-h-0 flex-1 flex-col"
                                :options="{ preserveScroll: true }"
                                @success="isCreateSheetOpen = false"
                            >
                                <SheetHeader
                                    class="border-b border-border px-6 py-5 text-left"
                                >
                                    <SheetTitle>Create role</SheetTitle>
                                    <SheetDescription>
                                        Add a web role and choose the
                                        permissions it should receive.
                                    </SheetDescription>
                                </SheetHeader>

                                <div
                                    class="flex-1 space-y-6 overflow-y-auto px-6 py-5"
                                >
                                    <div class="grid gap-2">
                                        <Label for="role-name">Role name</Label>
                                        <Input
                                            id="role-name"
                                            name="name"
                                            placeholder="e.g. Instructor"
                                            autocomplete="off"
                                            autofocus
                                        />
                                        <InputError :message="errors.name" />
                                    </div>

                                    <fieldset class="grid gap-3">
                                        <div class="space-y-1">
                                            <legend class="text-sm font-medium">
                                                Permissions
                                            </legend>
                                            <p
                                                class="text-sm text-muted-foreground"
                                            >
                                                Select the abilities available
                                                to this role.
                                            </p>
                                        </div>

                                        <div
                                            v-if="
                                                availablePermissions.length > 0
                                            "
                                            class="relative"
                                        >
                                            <Search
                                                aria-hidden="true"
                                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                            />
                                            <Input
                                                v-model="permissionSearchQuery"
                                                type="search"
                                                aria-label="Search permissions"
                                                placeholder="Search permissions…"
                                                autocomplete="off"
                                                class="pl-9"
                                            />
                                        </div>

                                        <div
                                            v-if="
                                                availablePermissions.length > 0
                                            "
                                            class="grid gap-5"
                                        >
                                            <section
                                                v-for="group in categorizedPermissions"
                                                v-show="
                                                    group.permissions.some(
                                                        matchesPermissionSearch,
                                                    )
                                                "
                                                :key="group.category"
                                                class="grid gap-2"
                                            >
                                                <h3
                                                    class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                                >
                                                    {{ group.category }}
                                                </h3>
                                                <div
                                                    class="grid gap-2 sm:grid-cols-2"
                                                >
                                                    <Label
                                                        v-for="permission in group.permissions"
                                                        v-show="
                                                            matchesPermissionSearch(
                                                                permission,
                                                            )
                                                        "
                                                        :key="permission.id"
                                                        :for="`permission-${permission.id}`"
                                                        class="cursor-pointer rounded-lg border border-border p-3 transition-colors hover:bg-muted/50"
                                                    >
                                                        <Checkbox
                                                            :id="`permission-${permission.id}`"
                                                            name="permissions[]"
                                                            :value="
                                                                permission.id
                                                            "
                                                        />
                                                        <span
                                                            class="min-w-0 break-words"
                                                        >
                                                            {{
                                                                permission.name
                                                            }}
                                                        </span>
                                                    </Label>
                                                </div>
                                            </section>

                                            <p
                                                v-if="
                                                    matchingPermissionCount ===
                                                    0
                                                "
                                                class="rounded-lg border border-dashed border-border px-4 py-6 text-center text-sm text-muted-foreground"
                                            >
                                                No permissions match your
                                                search.
                                            </p>
                                        </div>
                                        <p
                                            v-else
                                            class="rounded-lg border border-dashed border-border px-4 py-6 text-center text-sm text-muted-foreground"
                                        >
                                            No web permissions are available
                                            yet.
                                        </p>
                                        <InputError
                                            :message="
                                                errors.permissions ||
                                                errors['permissions.0']
                                            "
                                        />
                                    </fieldset>
                                </div>

                                <SheetFooter
                                    class="border-t border-border px-6 py-4 sm:flex-row sm:justify-end"
                                >
                                    <SheetClose as-child>
                                        <Button type="button" variant="outline">
                                            Cancel
                                        </Button>
                                    </SheetClose>
                                    <Button
                                        type="submit"
                                        :disabled="processing"
                                    >
                                        {{
                                            processing
                                                ? 'Creating…'
                                                : 'Create role'
                                        }}
                                    </Button>
                                </SheetFooter>
                            </Form>
                        </SheetContent>
                    </Sheet>
                </template>
            </TableToolbar>

            <Sheet v-model:open="isEditSheetOpen">
                <SheetContent side="right" class="w-full gap-0 p-0 sm:max-w-lg">
                    <Form
                        v-if="selectedRole"
                        :key="selectedRole.id"
                        v-bind="update.form(String(selectedRole.id))"
                        v-slot="{ errors, processing }"
                        class="flex min-h-0 flex-1 flex-col"
                        :options="{ preserveScroll: true }"
                        @success="closeEditSheet"
                    >
                        <SheetHeader
                            class="border-b border-border px-6 py-5 text-left"
                        >
                            <SheetTitle>Edit role</SheetTitle>
                            <SheetDescription>
                                Update {{ selectedRole.name }} and its assigned
                                permissions.
                            </SheetDescription>
                        </SheetHeader>

                        <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">
                            <div class="grid gap-2">
                                <Label for="edit-role-name">Role name</Label>
                                <Input
                                    id="edit-role-name"
                                    name="name"
                                    :default-value="selectedRole.name"
                                    autocomplete="off"
                                    autofocus
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <fieldset class="grid gap-3">
                                <div class="space-y-1">
                                    <legend class="text-sm font-medium">
                                        Permissions
                                    </legend>
                                    <p class="text-sm text-muted-foreground">
                                        Select the abilities available to this
                                        role.
                                    </p>
                                </div>

                                <div
                                    v-if="availablePermissions.length > 0"
                                    class="relative"
                                >
                                    <Search
                                        aria-hidden="true"
                                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                    />
                                    <Input
                                        v-model="permissionSearchQuery"
                                        type="search"
                                        aria-label="Search permissions"
                                        placeholder="Search permissions…"
                                        autocomplete="off"
                                        class="pl-9"
                                    />
                                </div>

                                <div
                                    v-if="availablePermissions.length > 0"
                                    class="grid gap-5"
                                >
                                    <section
                                        v-for="group in categorizedPermissions"
                                        v-show="
                                            group.permissions.some(
                                                matchesPermissionSearch,
                                            )
                                        "
                                        :key="group.category"
                                        class="grid gap-2"
                                    >
                                        <h3
                                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                        >
                                            {{ group.category }}
                                        </h3>
                                        <div class="grid gap-2 sm:grid-cols-2">
                                            <Label
                                                v-for="permission in group.permissions"
                                                v-show="
                                                    matchesPermissionSearch(
                                                        permission,
                                                    )
                                                "
                                                :key="permission.id"
                                                :for="`edit-permission-${permission.id}`"
                                                class="cursor-pointer rounded-lg border border-border p-3 transition-colors hover:bg-muted/50"
                                            >
                                                <Checkbox
                                                    :id="`edit-permission-${permission.id}`"
                                                    name="permissions[]"
                                                    :value="permission.id"
                                                    :default-value="
                                                        selectedRole.permission_ids.includes(
                                                            permission.id,
                                                        )
                                                    "
                                                />
                                                <span
                                                    class="min-w-0 break-words"
                                                >
                                                    {{ permission.name }}
                                                </span>
                                            </Label>
                                        </div>
                                    </section>

                                    <p
                                        v-if="matchingPermissionCount === 0"
                                        class="rounded-lg border border-dashed border-border px-4 py-6 text-center text-sm text-muted-foreground"
                                    >
                                        No permissions match your search.
                                    </p>
                                </div>
                                <p
                                    v-else
                                    class="rounded-lg border border-dashed border-border px-4 py-6 text-center text-sm text-muted-foreground"
                                >
                                    No {{ activePermissionGuard }} permissions
                                    are available yet.
                                </p>
                                <InputError
                                    :message="
                                        errors.permissions ||
                                        errors['permissions.0']
                                    "
                                />
                            </fieldset>
                        </div>

                        <SheetFooter
                            class="border-t border-border px-6 py-4 sm:flex-row sm:justify-end"
                        >
                            <SheetClose as-child>
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </SheetClose>
                            <Button type="submit" :disabled="processing">
                                {{ processing ? 'Saving…' : 'Save changes' }}
                            </Button>
                        </SheetFooter>
                    </Form>
                </SheetContent>
            </Sheet>

            <div class="overflow-x-auto">
                <table class="w-full min-w-xl text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-border/80 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                        >
                            <th scope="col" class="px-3 py-3">Role</th>
                            <th scope="col" class="px-3 py-3">Guard</th>
                            <th scope="col" class="px-3 py-3 text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr
                            v-for="role in roles.data"
                            :key="role.id"
                            class="transition-colors hover:bg-muted/35"
                        >
                            <td class="px-2 py-3">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/8 text-primary dark:bg-primary/15"
                                    >
                                        <ShieldCheck
                                            aria-hidden="true"
                                            class="size-4.5"
                                        />
                                    </span>
                                    <span class="font-medium text-foreground">
                                        {{ role.name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <span
                                    class="inline-flex rounded-md bg-muted px-2 py-1 text-xs font-medium text-muted-foreground"
                                >
                                    {{ role.guard_name }}
                                </span>
                            </td>
                            <td class="px-2 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        :aria-label="`Edit ${role.name}`"
                                        @click="openEditSheet(role)"
                                    >
                                        <Pencil aria-hidden="true" />
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        :aria-label="`Delete ${role.name}`"
                                        @click="openDeleteDialog(role)"
                                    >
                                        <Trash2 aria-hidden="true" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="roles.data.length === 0">
                            <td colspan="3" class="px-3 py-14 text-center">
                                <p class="font-medium text-foreground">
                                    No roles found
                                </p>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Try another search or guard filter.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <TablePagination
                :from="roles.from"
                :to="roles.to"
                :total="roles.total"
                :links="roles.links"
                :previous-page-url="roles.prev_page_url"
                :next-page-url="roles.next_page_url"
            />
        </div>

        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent v-if="rolePendingDeletion">
                <Form
                    v-bind="destroy.form(String(rolePendingDeletion.id))"
                    v-slot="{ processing }"
                    class="space-y-6"
                    :options="{ preserveScroll: true }"
                    @success="closeDeleteDialog"
                >
                    <DialogHeader class="space-y-3">
                        <DialogTitle>Delete role?</DialogTitle>
                        <DialogDescription>
                            The {{ rolePendingDeletion.name }} role will be
                            permanently deleted. This action cannot be undone.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button type="button" variant="secondary">
                                Cancel
                            </Button>
                        </DialogClose>
                        <Button
                            type="submit"
                            variant="destructive"
                            :disabled="processing"
                        >
                            {{ processing ? 'Deleting…' : 'Delete role' }}
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>
