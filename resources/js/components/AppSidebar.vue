<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpenCheck,
    User2,
    BookA,
    School,
    CalendarRange,
    GraduationCap,
    Users,
    BookOpen,
    ScrollText,
    CalendarDays,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import type { NavGroup } from '@/components/NavMain.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarRail,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes/admin';
import { index as IndexAcademicTermStructure } from '@/routes/admin/academics/academic-term-structures';
import { index as IndexCurriculum } from '@/routes/admin/academics/curriculum';
import { index as IndexGradeLevel } from '@/routes/admin/academics/grade-level';
import { index as IndexProgram } from '@/routes/admin/academics/program';
import { index as IndexSection } from '@/routes/admin/academics/section';
import { index as IndexSubject } from '@/routes/admin/academics/subject';
import { index as IndexSchoolYear } from '@/routes/admin/school-years';
import { index as userIndex } from '@/routes/admin/users';
import type { NavItem } from '@/types';

const primaryNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: BookOpenCheck,
    },
    {
        title: 'Users',
        href: userIndex(),
        icon: User2,
    },
    {
        title: 'Academics',
        href: '#',
        icon: BookA,
        children: [
            {
                title: 'School Years',
                href: IndexSchoolYear(),
                icon: School,
            },
            {
                title: 'Academic Periods',
                href: IndexAcademicTermStructure(),
                icon: CalendarRange,
            },
            {
                title: 'Grade Levels',
                href: IndexGradeLevel(),
                icon: GraduationCap,
            },
            {
                title: 'Sections',
                href: IndexSection(),
                icon: Users,
            },
            {
                title: 'Subjects',
                href: IndexSubject(),
                icon: BookOpen,
            },
            {
                title: 'Programs',
                href: IndexProgram(),
                icon: BookA,
            },
            {
                title: 'Curriculum',
                href: IndexCurriculum(),
                icon: ScrollText,
            },
            {
                title: 'Class Scheduling',
                href: '#',
                icon: CalendarDays,
            },
        ],
    },
];

const navigationGroups: NavGroup[] = [
    {
        title: 'Learning',
        items: primaryNavItems,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar" class="bg-transparent">
        <SidebarHeader
            class="border-b border-sidebar-border/70 px-4 py-4 group-data-[collapsible=icon]:px-1"
        >
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="h-13 rounded-md px-2.5 text-sidebar-foreground shadow-none transition-colors duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] group-data-[collapsible=icon]:size-10! group-data-[collapsible=icon]:p-0! hover:bg-sidebar-accent/70 hover:text-sidebar-foreground"
                    >
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent
            class="gap-3 px-4 py-5 group-data-[collapsible=icon]:px-1 group-data-[collapsible=icon]:py-4"
        >
            <NavMain :groups="navigationGroups" />
        </SidebarContent>

        <SidebarRail />
    </Sidebar>
    <slot />
</template>
