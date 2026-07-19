<!-- resources/js/modules/users/components/UserRowActions.vue -->
<script setup lang="ts">
import { Pencil, Trash } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import type { User } from './columns';

defineProps<{
    user: User;
}>();
</script>

<template>
    <div class="flex justify-end gap-2">
        <Button
            type="button"
            variant="outline"
            size="icon-sm"
            :aria-label="`Edit ${user.name}`"
        >
            <Pencil aria-hidden="true" />
        </Button>
        <Button
            type="button"
            variant="destructive"
            size="icon-sm"
            :aria-label="`Archived ${user.name}`"
        >
            <Trash aria-hidden="true" />
        </Button>
    </div>
</template>
