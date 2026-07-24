<script setup lang="ts">
import { CirclePlus, Pencil, Trash2 } from '@lucide/vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

import type { AcademicPeriod, AcademicTermStructure } from './types';

defineProps<{
    structure: AcademicTermStructure;
}>();

const emit = defineEmits<{
    add: [structure: AcademicTermStructure, parent?: AcademicPeriod];
    edit: [
        structure: AcademicTermStructure,
        period: AcademicPeriod,
        parent?: AcademicPeriod,
    ];
    delete: [structure: AcademicTermStructure, period: AcademicPeriod];
}>();
</script>

<template>
    <div class="flex min-w-80 flex-col gap-2 py-1">
        <div class="flex items-center justify-between gap-3">
            <Badge variant="outline">
                {{ structure.root_periods.length }}
                {{
                    structure.root_periods.length === 1
                        ? 'root period'
                        : 'root periods'
                }}
            </Badge>
            <Button
                type="button"
                size="sm"
                variant="ghost"
                @click="emit('add', structure)"
            >
                <CirclePlus aria-hidden="true" />
                Add root
            </Button>
        </div>

        <p
            v-if="structure.root_periods.length === 0"
            class="rounded-md border border-dashed px-3 py-4 text-center text-xs text-muted-foreground"
        >
            No periods configured.
        </p>

        <div v-else class="divide-y rounded-lg border bg-background">
            <div
                v-for="rootPeriod in structure.root_periods"
                :key="rootPeriod.id"
                class="flex flex-col gap-2 px-3 py-2.5"
            >
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="truncate text-sm font-medium">
                                {{ rootPeriod.name }}
                            </span>
                            <Badge
                                v-if="rootPeriod.status === 'inactive'"
                                variant="secondary"
                            >
                                Inactive
                            </Badge>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Sequence {{ rootPeriod.sequence }}
                            <template v-if="rootPeriod.code">
                                · {{ rootPeriod.code }}
                            </template>
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-0.5">
                        <Button
                            v-if="
                                structure.type !== 'quarterly' &&
                                rootPeriod.children.length < 4
                            "
                            type="button"
                            size="icon"
                            variant="ghost"
                            :aria-label="`Add grading period under ${rootPeriod.name}`"
                            @click="emit('add', structure, rootPeriod)"
                        >
                            <CirclePlus aria-hidden="true" />
                        </Button>
                        <Button
                            type="button"
                            size="icon"
                            variant="ghost"
                            :aria-label="`Edit ${rootPeriod.name}`"
                            @click="emit('edit', structure, rootPeriod)"
                        >
                            <Pencil aria-hidden="true" />
                        </Button>
                        <Button
                            type="button"
                            size="icon"
                            variant="ghost"
                            class="text-muted-foreground hover:text-destructive"
                            :aria-label="`Delete ${rootPeriod.name}`"
                            @click="emit('delete', structure, rootPeriod)"
                        >
                            <Trash2 aria-hidden="true" />
                        </Button>
                    </div>
                </div>

                <div
                    v-if="rootPeriod.children.length"
                    class="flex flex-col gap-1 border-l pl-3"
                >
                    <div
                        v-for="child in rootPeriod.children"
                        :key="child.id"
                        class="flex items-center justify-between gap-3 py-1"
                    >
                        <div class="min-w-0">
                            <span class="truncate text-xs">
                                {{ child.name }}
                            </span>
                            <span class="ml-1 text-xs text-muted-foreground">
                                {{ child.sequence }}
                            </span>
                        </div>
                        <div class="flex shrink-0 gap-0.5">
                            <Button
                                type="button"
                                size="icon"
                                variant="ghost"
                                :aria-label="`Edit ${child.name}`"
                                @click="
                                    emit('edit', structure, child, rootPeriod)
                                "
                            >
                                <Pencil aria-hidden="true" />
                            </Button>
                            <Button
                                type="button"
                                size="icon"
                                variant="ghost"
                                class="text-muted-foreground hover:text-destructive"
                                :aria-label="`Delete ${child.name}`"
                                @click="emit('delete', structure, child)"
                            >
                                <Trash2 aria-hidden="true" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
