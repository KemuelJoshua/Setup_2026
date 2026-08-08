<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { store, update } from '@/routes/admin/users';

import type { User } from './columns';

export interface UserRoleOption {
    id: number;
    name: string;
}

const props = defineProps<{
    roles: UserRoleOption[];
    user: User | null;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAttributes = computed(() =>
    props.user ? update.form(props.user.id) : store.form(),
);

const selectedRole = computed(() => props.user?.roles[0] ?? '__none');

const handleSuccess = (): void => {
    toast.success(
        props.user
            ? 'User updated successfully.'
            : 'User created successfully.',
    );

    isOpen.value = false;
};

const handleError = (): void => {
    toast.error('Please fix the validation errors.');
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent>
            <Form
                :key="user?.id ?? 'create'"
                v-bind="formAttributes"
                v-slot="{ errors, processing }"
                reset-on-success
                class="space-y-6"
                :options="{ preserveScroll: true }"
                :transform="
                    (data) => ({
                        ...data,
                        role_name:
                            data.role_name === '__none' ? null : data.role_name,
                    })
                "
                @success="handleSuccess"
                @error="handleError"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{ user ? 'Edit user' : 'Create user' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            user
                                ? 'Update the account details, password, or role.'
                                : 'Add a user account and assign an optional role.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="user-name">Name</Label>
                        <Input
                            id="user-name"
                            name="name"
                            :default-value="user?.name"
                            placeholder="Jane Doe"
                            autocomplete="name"
                            autofocus
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="user-email">Email</Label>
                        <Input
                            id="user-email"
                            name="email"
                            type="email"
                            :default-value="user?.email"
                            placeholder="jane@example.com"
                            autocomplete="email"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="user-role">Role</Label>
                        <Select name="role_name" :default-value="selectedRole">
                            <SelectTrigger id="user-role" class="w-full">
                                <SelectValue placeholder="Select a role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="__none">No role</SelectItem>
                                <SelectItem
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.role_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-password">Password</Label>
                        <Input
                            id="user-password"
                            name="password"
                            type="password"
                            :placeholder="
                                user
                                    ? 'Leave blank to keep current password'
                                    : 'Enter a password'
                            "
                            autocomplete="new-password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user-password-confirmation">
                            Confirm password
                        </Label>
                        <Input
                            id="user-password-confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">Cancel</Button>
                    </DialogClose>

                    <Button type="submit" :disabled="processing">
                        {{
                            processing
                                ? 'Saving...'
                                : user
                                  ? 'Save changes'
                                  : 'Create user'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
