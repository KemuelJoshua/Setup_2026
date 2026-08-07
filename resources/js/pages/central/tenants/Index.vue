<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { Building2, Plus, Sparkles } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import DataTableContainer from '@/components/DataTableContainer.vue';
import PageHero from '@/components/PageHero.vue';
import { Button } from '@/components/ui/button';
import DefaultContainer from '@/components/ui/containers/DefaultContainer.vue';
import {
    DataTable,
    DataTablePageSizeSelect,
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { destroy, index, updateStatus } from '@/routes/central/tenants';
import type { LengthAwarePaginator } from '@/types';

import { createColumns } from './columns';
import type { Tenant, TenantFilters } from './columns';
import CreateUpdate from './CreateUpdate.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Schools',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    tenants: LengthAwarePaginator<Tenant>;
    filters: TenantFilters;
}>();

const searchQuery = ref(props.filters.search ?? '');
const isFormDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedTenant = ref<Tenant | null>(null);

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchTenants = (
    perPage: string | number | undefined = props.filters.per_page,
): void => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
                per_page: perPage,
            },
        }),
        {
            only: ['tenants', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const handleSearch = (): void => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => fetchTenants(), 300);
};

const openCreateDialog = (): void => {
    selectedTenant.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (tenant: Tenant): void => {
    selectedTenant.value = tenant;
    isFormDialogOpen.value = true;
};

const openDeleteDialog = (tenant: Tenant): void => {
    selectedTenant.value = tenant;
    isDeleteDialogOpen.value = true;
};

const toggleTenantStatus = (tenant: Tenant): void => {
    router.patch(
        updateStatus.url(tenant.id),
        { is_active: !tenant.is_active },
        {
            preserveScroll: true,
            onSuccess: () =>
                toast.success(
                    tenant.is_active
                        ? 'School deactivated successfully.'
                        : 'School activated successfully.',
                ),
            onError: () => toast.error('Unable to update the school status.'),
        },
    );
};

const columns = createColumns({
    edit: openEditDialog,
    toggleStatus: toggleTenantStatus,
    delete: openDeleteDialog,
});

const handleDeleted = (): void => {
    toast.success('School deleted successfully.');
    isDeleteDialogOpen.value = false;
};

const handleDeleteError = (errors: Record<string, string>): void => {
    toast.error(
        errors.tenant ??
            'Unable to delete the school. Deactivate it before deleting.',
    );
};

watch(searchQuery, handleSearch);

watch([isFormDialogOpen, isDeleteDialogOpen], ([formOpen, deleteOpen]) => {
    if (!formOpen && !deleteOpen) {
        selectedTenant.value = null;
    }
});

onBeforeUnmount(() => window.clearTimeout(searchTimer));
</script>

<template>
    <Head title="Schools" />

    <DefaultContainer>
        <PageHero>
            <template #icon>
                <Building2 class="size-5" aria-hidden="true" />
            </template>

            <template #badge>
                <Sparkles class="size-3.5" aria-hidden="true" />
                Tenant administration
            </template>

            <template #title> School workspace </template>

            <template #description>
                Provision and manage every school's isolated application,
                domain, and access status.
            </template>

            <template #actions>
                <Button type="button" @click="openCreateDialog">
                    <Plus class="size-4" aria-hidden="true" />
                    Create school
                </Button>
            </template>
        </PageHero>

        <DataTableContainer>
            <DataTableToolbar
                v-model="searchQuery"
                :count="tenants.total"
                item-label="school"
                search-placeholder="Search name, code, domain, or email..."
                search-label="Search schools"
                class="border-b px-4 py-4 sm:px-5"
            >
                <template #filters>
                    <DataTablePageSizeSelect
                        :model-value="tenants.per_page"
                        @update:model-value="fetchTenants"
                    />
                </template>
            </DataTableToolbar>

            <DataTable
                :columns="columns"
                :data="tenants.data"
                empty-message="No schools found."
            />

            <template #footer>
                <DataTablePagination
                    :from="tenants.from"
                    :to="tenants.to"
                    :total="tenants.total"
                    :links="tenants.links"
                    :previous-page-url="tenants.prev_page_url"
                    :next-page-url="tenants.next_page_url"
                />
            </template>
        </DataTableContainer>
    </DefaultContainer>

    <CreateUpdate v-model:open="isFormDialogOpen" :tenant="selectedTenant" />

    <Dialog v-model:open="isDeleteDialogOpen">
        <DialogContent v-if="selectedTenant">
            <Form
                v-bind="destroy.form(selectedTenant.id)"
                v-slot="{ processing }"
                class="space-y-6"
                :options="{ preserveScroll: true }"
                @success="handleDeleted"
                @error="handleDeleteError"
            >
                <DialogHeader>
                    <DialogTitle>Delete school?</DialogTitle>
                    <DialogDescription>
                        <strong>{{ selectedTenant.school_name }}</strong>
                        and its tenant database will be permanently deleted.
                        This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="processing"
                        >
                            Cancel
                        </Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ processing ? 'Deleting...' : 'Delete school' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
