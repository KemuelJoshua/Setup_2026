# Data Table Guide

Use these components when a page needs a reusable table powered by
`@tanstack/vue-table`.

## Exports

```ts
import {
    DataTable,
    DataTableColumnHeader,
    DataTablePagination,
    DataTableRowActions,
    DataTableToolbar,
} from '@/components/ui/data-table';
```

## Basic Usage

Create a typed `columns.ts` file near the feature that owns the data.

```ts
import type { ColumnDef } from '@tanstack/vue-table';

export interface Product {
    id: number;
    name: string;
    sku: string;
    price: string;
    status: 'active' | 'draft' | 'archived';
}

export const columns: ColumnDef<Product>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'sku',
        header: 'SKU',
    },
    {
        accessorKey: 'price',
        header: 'Price',
    },
];
```

Render the table from a page or feature component.

```vue
<script setup lang="ts">
import { DataTable } from '@/components/ui/data-table';
import { columns, type Product } from './columns';

defineProps<{
    products: Product[];
}>();
</script>

<template>
    <DataTable
        :columns="columns"
        :data="products"
        empty-message="No products found."
    />
</template>
```

## Designing Columns

Each column is a `ColumnDef<T>`. Keep column definitions close to the feature so
they can use that feature's types, badges, routes, and actions.

### Simple Text Column

Use `accessorKey` when the cell should display a plain property.

```ts
{
    accessorKey: 'email',
    header: 'Email',
}
```

### Sortable Header

Use `DataTableColumnHeader` when the user should be able to sort a column in the
browser.

```ts
import type { ColumnDef } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { h } from 'vue';
import { DataTableColumnHeader } from '@/components/ui/data-table';

export const columns: ColumnDef<Product>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader as Component, {
                column,
                title: 'Name',
            }),
    },
];
```

### Custom Cell Markup

Use `cell` when a column needs formatting, badges, icons, links, or nested
properties.

```ts
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';

{
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) =>
        h(
            Badge,
            { variant: row.original.status === 'active' ? 'default' : 'secondary' },
            () => row.original.status,
        ),
}
```

### Numeric Alignment

Add classes in the header and cell so numbers scan cleanly.

```ts
{
    accessorKey: 'price',
    header: () => h('div', { class: 'text-right' }, 'Price'),
    cell: ({ row }) =>
        h('div', { class: 'text-right tabular-nums' }, row.original.price),
}
```

### Action Column

Use a display column for row actions. Display columns do not map to a data
property.

```ts
import { Link } from '@inertiajs/vue3';
import { h } from 'vue';
import { DataTableRowActions } from '@/components/ui/data-table';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { edit } from '@/routes/admin/products';

{
    id: 'actions',
    enableSorting: false,
    header: () => h('span', { class: 'sr-only' }, 'Actions'),
    cell: ({ row }) =>
        h(DataTableRowActions, { label: `Open actions for ${row.original.name}` }, () => [
            h(
                DropdownMenuItem,
                { asChild: true },
                () =>
                    h(
                        Link,
                        { href: edit(String(row.original.id)).url },
                        () => 'Edit',
                    ),
            ),
        ]),
}
```

## Toolbar And Pagination

The table exposes the TanStack table instance to named slots.

```vue
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    DataTable,
    DataTablePagination,
    DataTableToolbar,
} from '@/components/ui/data-table';
import { index } from '@/routes/admin/products';
import { columns, type Product } from './columns';
import type { LengthAwarePaginator } from '@/types';

const props = defineProps<{
    products: LengthAwarePaginator<Product>;
    filters: {
        search?: string;
    };
}>();

const searchQuery = ref(props.filters.search ?? '');

watch(searchQuery, () => {
    router.visit(
        index({
            query: {
                search: searchQuery.value || undefined,
            },
        }),
        {
            only: ['products', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
});
</script>

<template>
    <DataTable
        :columns="columns"
        :data="products.data"
        empty-message="No products found."
    >
        <template #toolbar>
            <DataTableToolbar
                v-model="searchQuery"
                title="Products"
                description="Manage catalog items."
                :count="products.total"
                item-label="product"
                search-placeholder="Search products..."
                search-label="Search products"
            />
        </template>

        <template #pagination>
            <DataTablePagination
                :from="products.from"
                :to="products.to"
                :total="products.total"
                :links="products.links"
                :previous-page-url="products.prev_page_url"
                :next-page-url="products.next_page_url"
            />
        </template>
    </DataTable>
</template>
```

## Props

| Prop | Type | Purpose |
| --- | --- | --- |
| `columns` | `ColumnDef<TData, TValue>[]` | Required TanStack column definitions. |
| `data` | `TData[]` | Required rows to render. |
| `emptyMessage` | `string` | Text shown when there are no rows. |
| `class` | `HTMLAttributes['class']` | Classes for the outer wrapper. |
| `containerClass` | `HTMLAttributes['class']` | Classes for the bordered table container. |
| `tableClass` | `HTMLAttributes['class']` | Classes passed to the underlying table. |
| `rowClass` | `class \| (row) => class` | Static or per-row classes. |
| `getRowId` | `(row, index, parent?) => string` | Stable row id callback. |
| `manualSorting` | `boolean` | Set to `true` when sorting is handled by the server. |

## Slots

| Slot | Slot Props | Purpose |
| --- | --- | --- |
| `toolbar` | `{ table }` | Add search, filters, and action buttons above the table. |
| `empty` | `{ table }` | Replace the default empty state content. |
| `pagination` | `{ table }` | Add pagination below the table. |

## Row Styling

Pass a class or callback to `rowClass`.

```vue
<DataTable
    :columns="columns"
    :data="orders"
    :row-class="
        (row) =>
            row.original.status === 'cancelled'
                ? 'bg-destructive/5 text-muted-foreground'
                : undefined
    "
/>
```

## Server-Side Sorting

`DataTableColumnHeader` currently updates the table's internal sorting state, so
it is best for client-side sorting. If a page must sort through the database,
drive sorting through the page's query string instead of relying only on the
internal table state.

Use `manual-sorting` when the server already returns rows in the requested sort
order and the table should not reorder them in the browser.

```vue
<DataTable
    :columns="columns"
    :data="products.data"
    manual-sorting
/>
```

For server-side sorting, use a custom header that calls `router.visit()` with
`sort` and `direction` query params, or extend `DataTable` with a controlled
sorting prop and update event. Keep the column names aligned with safe
server-side allow-lists instead of trusting raw request input.

## Good Column Design

- Put the most identifying text first, usually name, title, email, order number,
  or SKU.
- Keep long prose out of table cells. Use details pages, sheets, or dialogs for
  deep content.
- Right-align numeric values and use `tabular-nums`.
- Use badges for low-cardinality states such as status, guard, role, or payment
  state.
- Keep action columns narrow and use icon buttons or `DataTableRowActions`.
- Avoid doing heavy formatting work inside `cell`. Prepare display values on the
  server or with small helper functions when the logic grows.
- Prefer feature-owned column files like
  `resources/js/modules/products/components/columns.ts`.

## Common Pitfalls

- Do not pass a paginator object directly to `data`; pass `paginator.data`.
- Do not define columns inline in a template. Use a typed `columns.ts` file.
- Do not hardcode URLs in action columns. Use Wayfinder route helpers from
  `@/routes` or `@/actions`.
- Do not use sortable headers for server-side sorting unless the page also
  synchronizes sorting through the URL.
- Keep Vue cells created with `h()` small. If a cell becomes complex, extract a
  dedicated Vue component and render that component from the column.
