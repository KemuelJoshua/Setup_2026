<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

withDefaults(
    defineProps<{
        modelValue: string | number;
        options?: number[];
    }>(),
    {
        options: () => [10, 15, 25, 50],
    },
);

const emit = defineEmits<{
    (event: 'update:modelValue', value: number): void;
}>();
</script>

<template>
    <Select
        :model-value="Number(modelValue)"
        @update:model-value="emit('update:modelValue', Number($event))"
    >
        <SelectTrigger aria-label="Rows per page">
            <SelectValue />
        </SelectTrigger>
        <SelectContent>
            <SelectItem v-for="size in options" :key="size" :value="size">
                {{ size }} rows
            </SelectItem>
        </SelectContent>
    </Select>
</template>
