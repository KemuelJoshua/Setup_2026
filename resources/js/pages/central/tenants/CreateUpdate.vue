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
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { store, update } from '@/routes/central/tenants';

import type { Tenant } from './columns';

const props = defineProps<{
    tenant: Tenant | null;
    categories: Array<{ id: number; name: string; is_active: boolean }>;
}>();

const isOpen = defineModel<boolean>('open', { default: false });

const formAction = computed(() =>
    props.tenant ? update(props.tenant.id) : store(),
);

const handleSuccess = (): void => {
    toast.success(
        props.tenant
            ? 'School updated successfully.'
            : 'School created successfully.',
    );
    isOpen.value = false;
};

const handleError = (): void => {
    toast.error('Please fix the validation errors.');
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-3xl">
            <Form
                :key="tenant?.id ?? 'create'"
                :action="formAction"
                v-slot="{ errors, processing }"
                reset-on-success
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleSuccess"
                @error="handleError"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{ tenant ? 'Edit school' : 'Create school' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            tenant
                                ? 'Update the school profile, domain, and access status.'
                                : 'Provision an isolated school database and its first administrator.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <Tabs default-value="school" class="w-full">
                    <TabsList
                        class="grid h-auto w-full"
                        :class="tenant ? 'grid-cols-2' : 'grid-cols-3'"
                    >
                        <TabsTrigger value="school">
                            School information
                        </TabsTrigger>
                        <TabsTrigger value="contact">
                            Contact & profile
                        </TabsTrigger>
                        <TabsTrigger v-if="!tenant" value="administrator">
                            Administrator
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent
                        value="school"
                        force-mount
                        class="mt-6 data-[state=inactive]:hidden"
                    >
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="tenant-school-code"
                                    >School code</Label
                                >
                                <Input
                                    id="tenant-school-code"
                                    name="school_code"
                                    :default-value="tenant?.school_code"
                                    placeholder="SCHOOL-A"
                                    autofocus
                                />
                                <InputError :message="errors.school_code" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-school-name"
                                    >School name</Label
                                >
                                <Input
                                    id="tenant-school-name"
                                    name="school_name"
                                    :default-value="tenant?.school_name"
                                    placeholder="School A"
                                />
                                <InputError :message="errors.school_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-domain">Domain</Label>
                                <Input
                                    id="tenant-domain"
                                    name="domain"
                                    :default-value="tenant?.domain ?? ''"
                                    placeholder="school-a.localhost"
                                />
                                <InputError :message="errors.domain" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-category">Category</Label>
                                <Select
                                    name="category_id"
                                    :default-value="
                                        tenant?.category_id?.toString()
                                    "
                                >
                                    <SelectTrigger
                                        id="tenant-category"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select a category"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="category in categories"
                                            :key="category.id"
                                            :value="category.id.toString()"
                                        >
                                            {{ category.name }}
                                            {{
                                                category.is_active
                                                    ? ''
                                                    : '(Inactive)'
                                            }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.category_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-status">Status</Label>
                                <Select
                                    name="is_active"
                                    :default-value="
                                        tenant?.is_active === false ? '0' : '1'
                                    "
                                >
                                    <SelectTrigger
                                        id="tenant-status"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select a status"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1"
                                            >Active</SelectItem
                                        >
                                        <SelectItem value="0"
                                            >Inactive</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.is_active" />
                            </div>

                            <div class="grid gap-2 sm:col-span-2">
                                <Label for="tenant-address-line-1"
                                    >Address line 1</Label
                                >
                                <Input
                                    id="tenant-address-line-1"
                                    name="school_address_line_1"
                                    :default-value="
                                        tenant?.school_address_line_1 ?? ''
                                    "
                                    placeholder="Building, house or lot number, and street"
                                />
                                <InputError
                                    :message="errors.school_address_line_1"
                                />
                            </div>

                            <div class="grid gap-2 sm:col-span-2">
                                <Label for="tenant-address-line-2"
                                    >Address line 2</Label
                                >
                                <Input
                                    id="tenant-address-line-2"
                                    name="school_address_line_2"
                                    :default-value="
                                        tenant?.school_address_line_2 ?? ''
                                    "
                                    placeholder="Subdivision, sitio, purok, or unit"
                                />
                                <InputError
                                    :message="errors.school_address_line_2"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-barangay">Barangay</Label>
                                <Input
                                    id="tenant-barangay"
                                    name="school_barangay"
                                    :default-value="
                                        tenant?.school_barangay ?? ''
                                    "
                                    placeholder="Barangay"
                                />
                                <InputError :message="errors.school_barangay" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-city-municipality"
                                    >City / municipality</Label
                                >
                                <Input
                                    id="tenant-city-municipality"
                                    name="school_city_municipality"
                                    :default-value="
                                        tenant?.school_city_municipality ?? ''
                                    "
                                    placeholder="City or municipality"
                                />
                                <InputError
                                    :message="errors.school_city_municipality"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-province">Province</Label>
                                <Input
                                    id="tenant-province"
                                    name="school_province"
                                    :default-value="
                                        tenant?.school_province ?? ''
                                    "
                                    placeholder="Province"
                                />
                                <InputError :message="errors.school_province" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-region">Region</Label>
                                <Input
                                    id="tenant-region"
                                    name="school_region"
                                    :default-value="tenant?.school_region ?? ''"
                                    placeholder="Region"
                                />
                                <InputError :message="errors.school_region" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-postal-code"
                                    >Postal code</Label
                                >
                                <Input
                                    id="tenant-postal-code"
                                    name="school_postal_code"
                                    :default-value="
                                        tenant?.school_postal_code ?? ''
                                    "
                                    inputmode="numeric"
                                    placeholder="Postal code"
                                />
                                <InputError
                                    :message="errors.school_postal_code"
                                />
                            </div>
                        </div>
                    </TabsContent>

                    <TabsContent
                        value="contact"
                        force-mount
                        class="mt-6 data-[state=inactive]:hidden"
                    >
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="tenant-email">School email</Label>
                                <Input
                                    id="tenant-email"
                                    name="school_email"
                                    type="email"
                                    :default-value="tenant?.school_email ?? ''"
                                    placeholder="school@example.com"
                                />
                                <InputError :message="errors.school_email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-contact"
                                    >Contact number</Label
                                >
                                <Input
                                    id="tenant-contact"
                                    name="school_contact_number"
                                    :default-value="
                                        tenant?.school_contact_number ?? ''
                                    "
                                    placeholder="Contact number"
                                />
                                <InputError
                                    :message="errors.school_contact_number"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-director"
                                    >School director</Label
                                >
                                <Input
                                    id="tenant-director"
                                    name="school_director"
                                    :default-value="
                                        tenant?.school_director ?? ''
                                    "
                                    placeholder="Director name"
                                />
                                <InputError :message="errors.school_director" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-website">Website</Label>
                                <Input
                                    id="tenant-website"
                                    name="school_website"
                                    type="url"
                                    :default-value="
                                        tenant?.school_website ?? ''
                                    "
                                    placeholder="https://school.example.com"
                                />
                                <InputError :message="errors.school_website" />
                            </div>

                            <div class="grid gap-2 sm:col-span-2">
                                <Label for="tenant-motto">School motto</Label>
                                <Input
                                    id="tenant-motto"
                                    name="school_motto"
                                    :default-value="tenant?.school_motto ?? ''"
                                    placeholder="School motto"
                                />
                                <InputError :message="errors.school_motto" />
                            </div>
                        </div>
                    </TabsContent>

                    <TabsContent
                        v-if="!tenant"
                        value="administrator"
                        force-mount
                        class="mt-6 data-[state=inactive]:hidden"
                    >
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <h3 class="font-medium">First administrator</h3>
                                <p class="text-sm text-muted-foreground">
                                    These credentials are created inside the new
                                    tenant database.
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-admin-name">Name</Label>
                                <Input
                                    id="tenant-admin-name"
                                    name="admin_name"
                                    autocomplete="name"
                                />
                                <InputError :message="errors.admin_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-admin-email">Email</Label>
                                <Input
                                    id="tenant-admin-email"
                                    name="admin_email"
                                    type="email"
                                    autocomplete="email"
                                />
                                <InputError :message="errors.admin_email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-admin-password">
                                    Temporary password
                                </Label>
                                <Input
                                    id="tenant-admin-password"
                                    name="admin_password"
                                    type="password"
                                    autocomplete="new-password"
                                />
                                <InputError :message="errors.admin_password" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tenant-admin-password-confirmation">
                                    Confirm password
                                </Label>
                                <Input
                                    id="tenant-admin-password-confirmation"
                                    name="admin_password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                />
                            </div>
                        </div>
                    </TabsContent>
                </Tabs>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="outline">
                            Cancel
                        </Button>
                    </DialogClose>

                    <Button type="submit" :disabled="processing">
                        {{
                            processing
                                ? tenant
                                    ? 'Saving...'
                                    : 'Provisioning...'
                                : tenant
                                  ? 'Save changes'
                                  : 'Create school'
                        }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
