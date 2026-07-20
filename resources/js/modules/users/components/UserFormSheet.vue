<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner'
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
import { store, update } from '@/routes/admin/users';
import type { User } from './columns';

export interface UserRoleOption {
    id: number;
    name: string;
}

const props = defineProps<{
    mode: 'create' | 'edit';
    roles: UserRoleOption[];
    user?: User | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.mode === 'edit' && props.user
        ? update.form(props.user.id)
        : store.form(),
);

const title = computed(() =>
    props.mode === 'edit' ? 'Edit user' : 'Create user',
);

const description = computed(() =>
    props.mode === 'edit'
        ? 'Change the user details, password, or role.'
        : 'Add a user with a password and one role.',
);

const selectedRole = computed(() => props.user?.roles[0] ?? '');
const submitLabel = computed(() =>
    props.mode === 'edit' ? 'Save changes' : 'Create user',
);
const processingLabel = computed(() =>
    props.mode === 'edit' ? 'Saving...' : 'Creating...',
);

const onSuccess = () => {
    toast.success(
        props.mode === 'create'
            ? 'User created successfully.'
            : 'User updated successfully.'
    );

    closeSheet();
};

const onError = () => {
    toast.error('Please fix the validation errors.');
};

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
                v-if="mode === 'create' || user"
                :key="user?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                reset-on-success
                class="flex min-h-0 flex-1 flex-col"
                :options="{ preserveScroll: true }"
                @success="onSuccess"
                @error="onError"
            >
                <SheetHeader class="border-b border-border px-6 py-5 text-left">
                    <SheetTitle>{{ title }}</SheetTitle>
                    <SheetDescription>{{ description }}</SheetDescription>
                </SheetHeader>

                <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5">
                    <div class="grid gap-2">
                        <Label :for="`${mode}-user-name`">Name</Label>
                        <Input
                            :id="`${mode}-user-name`"
                            name="name"
                            :default-value="user?.name"
                            placeholder="Jane Doe"
                            autocomplete="name"
                            autofocus
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`${mode}-user-email`">Email</Label>
                        <Input
                            :id="`${mode}-user-email`"
                            name="email"
                            type="email"
                            :default-value="user?.email"
                            placeholder="jane@example.com"
                            autocomplete="email"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`${mode}-user-role`">Role</Label>
                        <select
                            :id="`${mode}-user-role`"
                            name="role_name"
                            :value="selectedRole"
                            class="h-9 w-full rounded-lg border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/20 dark:bg-input/20"
                        >
                            <option value="">No role</option>
                            <option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </option>
                        </select>
                        <InputError :message="errors.role_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`${mode}-user-password`">Password</Label>
                        <Input
                            :id="`${mode}-user-password`"
                            name="password"
                            type="password"
                            :placeholder="
                                mode === 'edit'
                                    ? 'Leave blank to keep current password'
                                    : 'Enter a password'
                            "
                            autocomplete="new-password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`${mode}-user-password-confirmation`">
                            Confirm password
                        </Label>
                        <Input
                            :id="`${mode}-user-password-confirmation`"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
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
