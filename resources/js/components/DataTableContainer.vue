<template>
    <section class="overflow-hidden rounded-xl border bg-card shadow-sm">
        <slot />

        <div v-if="$slots.footer" class="border-t px-4 py-3">
            <slot name="footer" />
        </div>
    </section>
</template>
