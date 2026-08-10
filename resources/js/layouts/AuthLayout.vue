<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';

const page = usePage();

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <slot v-if="page.component === 'auth/Login'" />
    <AuthLayout v-else :title="title" :description="description">
        <slot />
    </AuthLayout>
</template>
