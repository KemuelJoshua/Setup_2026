<script setup lang="ts">
import { Search } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Permission } from './columns';

const props = withDefaults(
    defineProps<{
        permissions: Permission[];
        guardName: string;
        selectedPermissionIds?: number[];
        checkboxIdPrefix?: string;
    }>(),
    {
        selectedPermissionIds: () => [],
        checkboxIdPrefix: 'permission',
    },
);

const searchQuery = ref('');

const availablePermissions = computed(() =>
    props.permissions.filter(
        (permission) => permission.guard_name === props.guardName,
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

const normalizedSearch = computed(() =>
    searchQuery.value.trim().toLocaleLowerCase(),
);

const matchesSearch = (permission: Permission): boolean => {
    if (normalizedSearch.value === '') {
        return true;
    }

    return `${permission.category} ${permission.name}`
        .toLocaleLowerCase()
        .includes(normalizedSearch.value);
};

const matchingPermissionCount = computed(
    () => availablePermissions.value.filter(matchesSearch).length,
);

watch(
    () => props.guardName,
    () => {
        searchQuery.value = '';
    },
);
</script>

<template>
    <fieldset class="grid gap-3">
        <div class="space-y-1">
            <legend class="text-sm font-medium">Permissions</legend>
            <p class="text-sm text-muted-foreground">
                Select the abilities available to this role.
            </p>
        </div>

        <div v-if="availablePermissions.length > 0" class="relative">
            <Search
                aria-hidden="true"
                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="searchQuery"
                type="search"
                aria-label="Search permissions"
                placeholder="Search permissions..."
                autocomplete="off"
                class="pl-9"
            />
        </div>

        <div v-if="availablePermissions.length > 0" class="grid gap-5">
            <section
                v-for="group in categorizedPermissions"
                v-show="group.permissions.some(matchesSearch)"
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
                        v-show="matchesSearch(permission)"
                        :key="permission.id"
                        :for="`${checkboxIdPrefix}-${permission.id}`"
                        class="cursor-pointer rounded-lg border border-border p-3 transition-colors hover:bg-muted/50"
                    >
                        <Checkbox
                            :id="`${checkboxIdPrefix}-${permission.id}`"
                            name="permissions[]"
                            :value="permission.id"
                            :default-value="
                                selectedPermissionIds.includes(permission.id)
                            "
                        />
                        <span class="min-w-0 break-words">
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
            No {{ guardName }} permissions are available yet.
        </p>
    </fieldset>
</template>
