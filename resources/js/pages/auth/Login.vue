<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    ArrowRight,
    CheckCircle2,
    LockKeyhole,
    Mail,
} from '@lucide/vue';
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
        description:
            'Enter your official account details to continue to your workspace.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Sign in to PEMS" />

    <div
        v-if="status"
        role="status"
        class="mb-5 flex items-start gap-2.5 rounded-xl border border-emerald-500/20 bg-emerald-500/8 p-3.5 text-sm font-medium text-emerald-700 dark:text-emerald-400"
    >
        <CheckCircle2 class="mt-0.5 size-4 shrink-0" />
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-2">
            <Label for="email" class="text-sm font-semibold">
                Email address
            </Label>
            <div class="relative">
                <Mail
                    class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-muted-foreground"
                    :stroke-width="1.8"
                />
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="username"
                    placeholder="name@agency.gov.ph"
                    :aria-invalid="Boolean(errors.email)"
                    class="h-12 rounded-xl bg-background pl-11 shadow-none"
                />
            </div>
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <div class="flex items-center justify-between gap-4">
                <Label for="password" class="text-sm font-semibold">
                    Password
                </Label>
                <TextLink
                    v-if="canResetPassword"
                    :href="request()"
                    class="text-xs font-semibold text-primary no-underline hover:text-[var(--primary-hover)] hover:underline"
                    :tabindex="5"
                >
                    Forgot password?
                </TextLink>
            </div>
            <div class="relative">
                <LockKeyhole
                    class="pointer-events-none absolute top-1/2 left-4 z-10 size-4 -translate-y-1/2 text-muted-foreground"
                    :stroke-width="1.8"
                />
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    :aria-invalid="Boolean(errors.password)"
                    class="h-12 rounded-xl bg-background pl-11 shadow-none"
                />
            </div>
            <InputError :message="errors.password" />
        </div>

        <Label
            for="remember"
            class="flex w-fit cursor-pointer items-center gap-2.5 text-sm font-normal text-muted-foreground"
        >
            <Checkbox id="remember" name="remember" :tabindex="3" />
            <span>Keep me signed in on this device</span>
        </Label>

        <Button
            type="submit"
            class="mt-1 h-12 w-full rounded-xl bg-primary font-bold text-primary-foreground shadow-lg shadow-[var(--shadow-primary)] transition hover:-translate-y-0.5 hover:bg-[var(--primary-hover)] active:translate-y-0"
            :tabindex="4"
            :disabled="processing"
            data-test="login-button"
        >
            <Spinner v-if="processing" />
            <template v-else>
                Sign in securely
                <ArrowRight class="size-4" />
            </template>
            <span v-if="processing">Verifying account...</span>
        </Button>
    </Form>
</template>
