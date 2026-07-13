<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LockKeyhole, LogIn, Mail } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Welcome back',
        description: 'Sign in to continue your learning journey.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-2.5">
                <Label for="email" class="text-sm font-medium"
                    >Email address</Label
                >
                <div class="relative">
                    <Mail
                        class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-muted-foreground"
                        :stroke-width="1.8"
                    />
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="h-11 rounded-lg bg-background pl-10 shadow-none"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2.5">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-sm font-medium"
                        >Password</Label
                    >
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-medium text-primary no-underline hover:text-[var(--primary-hover)] hover:underline"
                        :tabindex="5"
                    >
                        Forgot your password?
                    </TextLink>
                </div>
                <div class="relative">
                    <LockKeyhole
                        class="pointer-events-none absolute top-1/2 left-3.5 z-10 size-4 -translate-y-1/2 text-muted-foreground"
                        :stroke-width="1.8"
                    />
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="h-11 rounded-lg bg-background pl-10 shadow-none"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label
                    for="remember"
                    class="flex cursor-pointer items-center gap-2.5 text-sm font-normal text-muted-foreground"
                >
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Keep me signed in</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-1 h-11 w-full rounded-lg bg-primary font-semibold text-primary-foreground shadow-[var(--shadow-primary)] shadow-md transition-all hover:-translate-y-0.5 hover:bg-[var(--primary-hover)] hover:shadow-lg active:translate-y-0 active:bg-[var(--primary-active)]"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                <LogIn v-else class="size-4" :stroke-width="2" />
                {{ processing ? 'Signing in...' : 'Sign in' }}
            </Button>
        </div>
    </Form>
</template>
