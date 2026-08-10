<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LockKeyhole, Mail, Moon, Sun, UserRound } from '@lucide/vue';

import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useAppearance } from '@/composables/useAppearance';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleAppearance(): void {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

defineOptions({
    layout: {
        title: 'Welcome Back',
        description: 'Sign in to continue to your account.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <main
        class="relative min-h-svh overflow-hidden bg-background text-foreground transition-colors duration-300"
    >
        <div
            class="grid min-h-svh w-full xl:grid-cols-[minmax(0,1.08fr)_minmax(30rem,0.92fr)]"
        >
            <!-- Left section -->
            <section
                class="relative hidden min-h-184 overflow-hidden border-r border-border/70 px-[clamp(3rem,5vw,6.5rem)] py-[clamp(2.5rem,5vh,4.5rem)] transition-colors duration-300 xl:flex xl:flex-col"
            >
                <!-- Background -->
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_65%_43%,var(--primary-muted),transparent_35%),linear-gradient(145deg,var(--card)_0%,var(--background)_58%,var(--card)_100%)]"
                ></div>

                <div
                    class="absolute top-[26%] right-[-8%] size-[34rem] rounded-full border border-border/50 bg-card/20"
                ></div>

                <div
                    class="absolute top-[33%] right-[2%] size-[24rem] rounded-full border border-border/50"
                ></div>

                <!-- Header logos -->
                <header
                    class="relative z-10 flex items-start justify-between gap-10"
                >
                    <img
                        src="/img/sims-logo.png"
                        alt="Supply and Inventory Management System"
                        class="h-auto w-[clamp(17rem,18vw,21rem)] object-contain dark:hidden"
                    />

                    <img
                        src="/img/sims-logo-dark.png"
                        alt="Supply and Inventory Management System"
                        class="hidden h-auto w-[clamp(17rem,18vw,21rem)] object-contain dark:block"
                    />

                    <img
                        src="/img/DOST-TAPI.png"
                        alt="DOST-TAPI"
                        class="size-[clamp(5rem,6vw,6.5rem)] object-contain drop-shadow-sm"
                    />
                </header>

                <!-- Hero content -->
                <div
                    class="relative z-20 mt-[clamp(2.5rem,5vh,4.5rem)] max-w-md rounded-2xl border border-border/70 bg-card/55 p-5 shadow-[0_24px_70px_-34px_var(--shadow-color)] ring-1 ring-border/50 backdrop-blur-xl sm:p-6"
                >
                    <div
                        class="mb-3 flex items-center gap-2.5 text-[0.65rem] font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        <span class="h-px w-9 bg-primary"></span>
                        Supply and Inventory
                    </div>

                    <h1
                        class="text-[clamp(2rem,2.8vw,3.15rem)] leading-[1.06] font-bold tracking-[-0.035em]"
                    >
                        Smarter Supply<br />

                        <span class="text-primary"> Better Inventory </span>
                    </h1>

                    <p
                        class="mt-4 max-w-sm text-[clamp(0.875rem,0.95vw,1rem)] leading-6 text-muted-foreground"
                    >
                        Streamline your supply chain and maximize operational
                        efficiency with real-time insights.
                    </p>
                </div>

                <!-- Warehouse image -->
                <img
                    src="/img/inventory-warehouse-transparent.png"
                    alt="SIMS inventory dashboard displayed on a laptop"
                    class="absolute bottom-0 left-0 z-10 h-auto w-[min(98%,67rem)] max-w-none object-contain object-left-bottom drop-shadow-[0_24px_28px_var(--shadow-color)] dark:brightness-[0.78] dark:contrast-[1.08]"
                />
            </section>

            <!-- Login section -->
            <section
                class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-muted px-4 py-7 transition-colors duration-300 sm:px-8 sm:py-10 xl:px-[clamp(3rem,5vw,6rem)]"
            >
                <!-- Background -->
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_22%,var(--card)_0%,transparent_62%)]"
                ></div>

                <div
                    class="pointer-events-none absolute -top-48 -right-48 size-96 rounded-full bg-primary-muted/45 blur-3xl"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-52 -left-52 size-112 rounded-full bg-muted-foreground/10 blur-3xl"
                ></div>

                <!-- Theme toggle -->
                <button
                    type="button"
                    class="absolute top-4 right-4 z-30 flex size-10 items-center justify-center rounded-xl border border-border/80 bg-card/80 text-muted-foreground shadow-sm backdrop-blur-md transition-all duration-200 hover:border-ring/50 hover:bg-card hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none sm:top-6 sm:right-6"
                    :aria-label="
                        resolvedAppearance === 'dark'
                            ? 'Use light theme'
                            : 'Use dark theme'
                    "
                    :title="
                        resolvedAppearance === 'dark'
                            ? 'Use light theme'
                            : 'Use dark theme'
                    "
                    @click="toggleAppearance"
                >
                    <Sun
                        v-if="resolvedAppearance === 'dark'"
                        class="size-4.5"
                        :stroke-width="1.8"
                    />

                    <Moon v-else class="size-4.5" :stroke-width="1.8" />
                </button>

                <!-- Mobile logos -->
                <div
                    class="relative z-10 mb-7 flex w-full max-w-lg items-center justify-between gap-4 px-1 sm:mb-9 xl:hidden"
                >
                    <img
                        src="/img/sims-logo.png"
                        alt="Supply and Inventory Management System"
                        class="h-auto w-[min(70vw,16rem)] object-contain dark:hidden"
                    />

                    <img
                        src="/img/sims-logo-dark.png"
                        alt="Supply and Inventory Management System"
                        class="hidden h-auto w-[min(70vw,16rem)] object-contain dark:block"
                    />

                    <img
                        src="/img/DOST-TAPI.png"
                        alt="DOST-TAPI"
                        class="size-12 shrink-0 object-contain drop-shadow-sm sm:size-14"
                    />
                </div>

                <!-- Login card -->
                <div
                    class="relative z-10 w-full max-w-120 overflow-hidden rounded-lg border border-border bg-card px-5 py-7 text-card-foreground shadow-[0_30px_80px_-30px_var(--shadow-color)] ring-1 ring-border/70 transition-colors duration-300 sm:px-10 sm:py-10"
                >
                    <div class="mb-7 text-center sm:mb-8">
                        <div
                            class="mx-auto mb-5 flex size-16 items-center justify-center rounded-2xl bg-primary-soft text-primary ring-1 ring-primary-muted"
                        >
                            <UserRound class="size-8" :stroke-width="1.8" />
                        </div>

                        <h2
                            class="text-2xl font-bold tracking-tight sm:text-[1.7rem]"
                        >
                            Welcome Back
                        </h2>

                        <p class="mt-2 text-sm text-muted-foreground">
                            Sign in to continue to your account
                        </p>
                    </div>

                    <!-- Status message -->
                    <div
                        v-if="status"
                        class="mb-5 rounded-lg bg-success/10 px-4 py-3 text-center text-sm font-medium text-success"
                    >
                        {{ status }}
                    </div>

                    <!-- Login form -->
                    <Form
                        v-bind="store.form()"
                        v-slot="{ errors, processing }"
                        :reset-on-success="['password']"
                        class="flex flex-col gap-5 sm:gap-5.5"
                    >
                        <!-- Email -->
                        <div class="grid gap-2">
                            <Label
                                for="email"
                                class="text-sm font-medium text-foreground"
                            >
                                Email Address
                            </Label>

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
                                    placeholder="Enter your email address"
                                    class="h-12 rounded-xl border-input bg-background/60 pl-10 text-foreground shadow-none transition-colors placeholder:text-muted-foreground hover:border-ring/50 focus-visible:border-ring focus-visible:bg-background focus-visible:ring-ring/15"
                                />
                            </div>

                            <InputError :message="errors.email" />
                        </div>

                        <!-- Password -->
                        <div class="grid gap-2">
                            <Label
                                for="password"
                                class="text-sm font-medium text-foreground"
                            >
                                Password
                            </Label>

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
                                    class="h-12 rounded-xl border-input bg-background/60 pl-10 text-foreground shadow-none transition-colors placeholder:text-muted-foreground hover:border-ring/50 focus-visible:border-ring focus-visible:bg-background focus-visible:ring-ring/15"
                                />
                            </div>

                            <InputError :message="errors.password" />
                        </div>

                        <!-- Remember and password reset -->
                        <div
                            class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2"
                        >
                            <Label
                                for="remember"
                                class="flex cursor-pointer items-center gap-2 text-sm font-normal text-muted-foreground"
                            >
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                />

                                <span>Remember me</span>
                            </Label>

                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                :tabindex="5"
                                class="text-sm font-medium text-primary no-underline transition-colors hover:text-primary-hover hover:underline"
                            >
                                Forgot Password?
                            </TextLink>
                        </div>

                        <!-- Submit -->
                        <Button
                            type="submit"
                            class="h-12 w-full rounded-xl bg-primary font-semibold text-primary-foreground shadow-[var(--shadow-primary)] shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary-hover hover:shadow-xl active:translate-y-0 active:bg-primary-active"
                            :tabindex="4"
                            :disabled="processing"
                            data-test="login-button"
                        >
                            <Spinner v-if="processing" />

                            {{ processing ? 'Signing in...' : 'Sign In' }}
                        </Button>
                    </Form>
                </div>

                <!-- Footer -->
                <footer
                    class="relative z-10 mt-8 flex w-full max-w-152 flex-col items-center justify-between gap-1 text-center text-[0.68rem] text-muted-foreground sm:flex-row sm:gap-4 sm:text-left xl:absolute xl:right-8 xl:bottom-5 xl:left-8 xl:max-w-none"
                >
                    <p>© 2026 Department of Science and Technology.</p>
                    <p class="shrink-0">SIMS v1.0.0</p>
                </footer>
            </section>
        </div>
    </main>
</template>
