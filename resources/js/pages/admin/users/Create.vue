<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/users';
import { Form, Head, router } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Plus } from '@lucide/vue';


const isCreateSheetOpen = ref(false);

const props = defineProps<{

}>();
</script>

<template>
    <Sheet v-model:open="isCreateSheetOpen">
        <SheetTrigger as-child>
            <Button type="button" size="sm">
                <Plus aria-hidden="true" />
                Create
            </Button>
        </SheetTrigger>

        <SheetContent side="right" class="w-full gap-0 p-0 sm:max-w-lg">
            <Form v-slot="{ errors, processing }" reset-on-success
                class="flex min-h-0 flex-1 flex-col" :options="{ preserveScroll: true }"
                @success="isCreateSheetOpen = false">
                <SheetHeader class="border-b border-border px-6 py-5 text-left">
                    <SheetTitle>Create User</SheetTitle>
                    <SheetDescription>
                        Add a user and choose the
                        role it should have.
                    </SheetDescription>
                </SheetHeader>

                <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">
                    <div class="grid gap-2">
                        <Label for="role-name">Role name</Label>
                        <Input id="role-name" name="name" placeholder="e.g. Instructor" autocomplete="off" autofocus />
                        <InputError :message="errors.name" />
                    </div>
                </div>

                <SheetFooter class="border-t border-border px-6 py-4 sm:flex-row sm:justify-end">
                    <SheetClose as-child>
                        <Button type="button" variant="outline">
                            Cancel
                        </Button>
                    </SheetClose>
                    <Button type="submit" :disabled="processing">
                        {{
                            processing
                                ? 'Creating…'
                                : 'Create role'
                        }}
                    </Button>
                </SheetFooter>
            </Form>
        </SheetContent>
    </Sheet>
</template>