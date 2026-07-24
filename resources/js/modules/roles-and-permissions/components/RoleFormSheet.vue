<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
import { store, update } from '@/routes/admin/settings/roles';
import type { Permission, Role } from './columns';
import PermissionPicker from './PermissionPicker.vue';

const props = defineProps<{
    mode: 'create' | 'edit';
    permissions: Permission[];
    role?: Role | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.mode === 'edit' && props.role
        ? update.form(props.role.id)
        : store.form(),
);

const title = computed(() =>
    props.mode === 'edit' ? 'Edit role' : 'Create role',
);

const description = computed(() =>
    props.mode === 'edit' && props.role
        ? `Update ${props.role.name} and its assigned permissions.`
        : 'Add a web role and choose the permissions it should receive.',
);

const guardName = computed(() => props.role?.guard_name ?? 'web');
const submitLabel = computed(() =>
    props.mode === 'edit' ? 'Save changes' : 'Create role',
);
const processingLabel = computed(() =>
    props.mode === 'edit' ? 'Saving...' : 'Creating...',
);

const closeSheet = (): void => {
    isOpen.value = false;
};
</script>

<template>
    <Sheet v-model:open="isOpen">
        <SheetTrigger v-if="$slots.trigger" as-child>
            <slot name="trigger" />
        </SheetTrigger>

        <SheetContent side="right" class="w-full gap-0 p-0 sm:max-w-lg">
            <Form
                v-if="mode === 'create' || role"
                :key="role?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                reset-on-success
                class="flex min-h-0 flex-1 flex-col"
                :options="{ preserveScroll: true }"
                @success="closeSheet"
            >
                <SheetHeader class="border-b border-border px-6 py-5 text-left">
                    <SheetTitle>{{ title }}</SheetTitle>
                    <SheetDescription>{{ description }}</SheetDescription>
                </SheetHeader>

                <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">
                    <div class="grid gap-2">
                        <Label :for="`${mode}-role-name`">Role name</Label>
                        <Input
                            :id="`${mode}-role-name`"
                            name="name"
                            :default-value="role?.name"
                            placeholder="e.g. Instructor"
                            autocomplete="off"
                            autofocus
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <PermissionPicker
                            :permissions="permissions"
                            :guard-name="guardName"
                            :selected-permission-ids="
                                role?.permission_ids ?? []
                            "
                            :checkbox-id-prefix="`${mode}-permission`"
                        />
                        <InputError
                            :message="
                                errors.permissions || errors['permissions.0']
                            "
                        />
                    </div>
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
                        {{ processing ? processingLabel : submitLabel }}
                    </Button>
                </SheetFooter>
            </Form>
        </SheetContent>
    </Sheet>
</template>
