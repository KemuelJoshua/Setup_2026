<script setup lang="ts">
import { FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { columns, type User } from './columns';

const props = defineProps<{
    users: User[];
}>();

const table = useVueTable({
    get data() {
        return props.users;
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow
                v-for="headerGroup in table.getHeaderGroups()"
                :key="headerGroup.id"
            >
                <TableHead
                    v-for="header in headerGroup.headers"
                    :key="header.id"
                >
                    <FlexRender
                        v-if="!header.isPlaceholder"
                        :render="header.column.columnDef.header"
                        :props="header.getContext()"
                    />
                </TableHead>
            </TableRow>
        </TableHeader>

        <TableBody>
            <template v-if="table.getRowModel().rows.length">
                <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
                    <TableCell
                        v-for="cell in row.getVisibleCells()"
                        :key="cell.id"
                    >
                        <FlexRender
                            :render="cell.column.columnDef.cell"
                            :props="cell.getContext()"
                        />
                    </TableCell>
                </TableRow>
            </template>

            <TableEmpty v-else :colspan="columns.length">
                No users found.
            </TableEmpty>
        </TableBody>
    </Table>
</template>
