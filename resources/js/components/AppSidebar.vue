<script setup lang="ts">
import {
    Activity,
    Archive,
    Barcode,
    Bell,
    Boxes,
    ClipboardCheck,
    FileChartColumn,
    FileStack,
    Gauge,
    GitPullRequest,
    PackageCheck,
    PackageOpen,
    RefreshCw,
    Settings,
    ShoppingCart,
    SlidersHorizontal,
    Warehouse,
    User2,
} from '@lucide/vue';
import type { NavGroup } from '@/components/NavMain.vue';
import NavMain from '@/components/NavMain.vue';
import { Sidebar, SidebarContent, SidebarRail } from '@/components/ui/sidebar';
import { dashboard } from '@/routes/admin';
import { index as userIndex } from '@/routes/admin/users';
import type { NavItem } from '@/types';

const navigationGroups: NavGroup[] = [
    {
        title: 'Overview',
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: Gauge,
            },
        ],
    },
    {
        title: 'Inventory Setup',
        items: [
            {
                title: 'Master File Management',
                href: '/admin/master-files',
                icon: FileStack,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/master-files',
                    },
                ],
            },
            {
                title: 'Supply Inventory Module',
                href: '/admin/supply-inventory',
                icon: Boxes,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/supply-inventory',
                    },
                ],
            },
            {
                title: 'Warehouse Management',
                href: '/admin/warehouses',
                icon: Warehouse,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/warehouses',
                    },
                ],
            },
            {
                title: 'Barcode & QR Management',
                href: '/admin/barcode-qr',
                icon: Barcode,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/barcode-qr',
                    },
                ],
            },
        ],
    },
    {
        title: 'Inventory Operations',
        items: [
            {
                title: 'Receiving Management',
                href: '/admin/receiving',
                icon: PackageOpen,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/receiving',
                    },
                ],
            },
            {
                title: 'Stock Issuance',
                href: '/admin/stock-issuance',
                icon: PackageCheck,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/stock-issuance',
                    },
                ],
            },
            {
                title: 'Inventory Transactions',
                href: '/admin/inventory-transactions',
                icon: RefreshCw,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/inventory-transactions',
                    },
                ],
            },
            {
                title: 'Stock Adjustment',
                href: '/admin/stock-adjustments',
                icon: SlidersHorizontal,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/stock-adjustments',
                    },
                ],
            },
            {
                title: 'Physical Inventory',
                href: '/admin/physical-inventory',
                icon: ClipboardCheck,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/physical-inventory',
                    },
                ],
            },
        ],
    },
    {
        title: 'Monitoring & Planning',
        items: [
            {
                title: 'Stock Monitoring',
                href: '/admin/stock-monitoring',
                icon: Activity,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/stock-monitoring',
                    },
                ],
            },
            {
                title: 'Reorder Management',
                href: '/admin/reorders',
                icon: ShoppingCart,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/reorders',
                    },
                ],
            },
            {
                title: 'Reports',
                href: '/admin/reports',
                icon: FileChartColumn,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/reports',
                    },
                ],
            },
        ],
    },
    {
        title: 'Workflow & Integration',
        items: [
            {
                title: 'Approval Workflow',
                href: '/admin/approval-workflow',
                icon: GitPullRequest,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/approval-workflow',
                    },
                ],
            },
            {
                title: 'Asset Integration',
                href: '/admin/asset-integration',
                icon: Archive,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/asset-integration',
                    },
                ],
            },
            {
                title: 'Notifications',
                href: '/admin/notifications',
                icon: Bell,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/notifications',
                    },
                ],
            },
        ],
    },
    {
        title: 'Administration',
        items: [
            {
                title: 'Settings',
                href: '/admin/settings',
                icon: Settings,
                children: [
                    {
                        title: 'Overview',
                        href: '/admin/settings',
                    },
                    {
                        title: 'Users',
                        href: userIndex(),
                        icon: User2,
                    },
                ],
            },
        ],
    },
];

</script>

<template>
    <Sidebar
        collapsible="offcanvas"
        variant="sidebar"
        class="dark top-16 h-[calc(100svh-4rem)] border-t-0 bg-sidebar"
    >
        <SidebarContent
            class="scrollbar-none gap-3 px-3 py-4 group-data-[collapsible=icon]:px-1 group-data-[collapsible=icon]:py-4 [&::-webkit-scrollbar]:hidden"
        >
            <NavMain :groups="navigationGroups" />
        </SidebarContent>

        <SidebarRail />
    </Sidebar>
    <slot />
</template>
