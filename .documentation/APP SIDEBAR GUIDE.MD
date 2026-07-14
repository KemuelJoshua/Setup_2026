# App Sidebar Guide

Use `resources/js/components/AppSidebar.vue` when adding sidebar items directly in the component.

The sidebar is rendered in this flow:

```text
AppSidebar.vue -> navigationGroups -> NavMain.vue -> sidebar menu
```

`AppSidebar.vue` owns the navigation arrays. `NavMain.vue` reads those arrays and automatically decides whether an item is a direct link or an expandable parent with children.

## 1. Import Routes And Icons

At the top of `resources/js/components/AppSidebar.vue`, import the route helpers and icons you need.

```ts
import { BookOpenCheck, Settings, ShieldCheck, UserRound } from '@lucide/vue';
import { dashboard } from '@/routes/admin';
import appearance from '@/routes/admin/settings/appearance';
import profile from '@/routes/admin/settings/profile';
import roles from '@/routes/admin/settings/roles';
import security from '@/routes/admin/settings/security';
```

Use Wayfinder route helpers when they exist. They return an Inertia-compatible route object for the `href` field.

## 2. Add A Direct Item Without Children

A direct item has `title`, `href`, and optionally `icon`.

```ts
const primaryNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: BookOpenCheck,
    },
    {
        title: 'Profile',
        href: profile.edit(),
        icon: UserRound,
    },
];
```

Direct items are rendered as `<Link>` elements. Clicking the item navigates immediately.

## 3. Add An Item With Children

An item with children needs a parent object with a `children` array.

```ts
const settingsNavItems: NavItem[] = [
    {
        title: 'Settings',
        href: '#',
        icon: Settings,
        children: [
            {
                title: 'Roles & Permissions',
                href: roles.index(),
                icon: ShieldCheck,
            },
            {
                title: 'Appearance',
                href: appearance.edit(),
                icon: Settings,
            },
            {
                title: 'Security',
                href: security.edit(),
                icon: ShieldCheck,
            },
        ],
    },
];
```

Parent items with children are rendered as buttons, not links. Clicking the parent expands or collapses its children. The parent is also marked active when any child route is active.

## 4. Register The Items In A Group

Every visible item must belong to a `NavGroup`.

```ts
const navigationGroups: NavGroup[] = [
    {
        title: 'Learning',
        items: primaryNavItems,
    },
    {
        title: 'Settings',
        items: settingsNavItems,
    },
];
```

`NavMain` receives those groups here:

```vue
<NavMain :groups="navigationGroups" />
```

## 5. Complete Direct Usage Example

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpenCheck, Settings, ShieldCheck, UserRound } from '@lucide/vue';
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
import appearance from '@/routes/admin/settings/appearance';
import profile from '@/routes/admin/settings/profile';
import roles from '@/routes/admin/settings/roles';
import security from '@/routes/admin/settings/security';
import type { NavItem } from '@/types';

const primaryNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: BookOpenCheck,
    },
    {
        title: 'Profile',
        href: profile.edit(),
        icon: UserRound,
    },
];

const settingsNavItems: NavItem[] = [
    {
        title: 'Settings',
        href: '#',
        icon: Settings,
        children: [
            {
                title: 'Roles & Permissions',
                href: roles.index(),
                icon: ShieldCheck,
            },
            {
                title: 'Appearance',
                href: appearance.edit(),
                icon: Settings,
            },
            {
                title: 'Security',
                href: security.edit(),
                icon: ShieldCheck,
            },
        ],
    },
];

const navigationGroups: NavGroup[] = [
    {
        title: 'Learning',
        items: primaryNavItems,
    },
    {
        title: 'Settings',
        items: settingsNavItems,
    },
];
</script>
```

## 6. NavItem Shape

`NavItem` is defined in `resources/js/types/navigation.ts`.

```ts
export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    children?: NavItem[];
};
```

Use these rules:

- `title` must be unique within the visible menu because it is used as the Vue key and expansion key.
- `href` is required, even for parents. Use `'#'` for a parent that only toggles children.
- `icon` is optional. Parent icons show beside the parent title. Child icons are accepted by the type, but the current child template only displays the title.
- `children` turns the item into an expandable parent.

## 7. Common Patterns

Add another direct link:

```ts
primaryNavItems.push({
    title: 'Reports',
    href: reports(),
    icon: ChartNoAxesColumn,
});
```

Add another child under an existing parent:

```ts
settingsNavItems[0].children?.push({
    title: 'Team Members',
    href: teamMembers(),
    icon: UsersRound,
});
```

Create a new group:

```ts
const managementNavItems: NavItem[] = [
    {
        title: 'Users',
        href: users(),
        icon: UsersRound,
    },
];

const navigationGroups: NavGroup[] = [
    {
        title: 'Learning',
        items: primaryNavItems,
    },
    {
        title: 'Management',
        items: managementNavItems,
    },
    {
        title: 'Settings',
        items: settingsNavItems,
    },
];
```

## 8. After Adding A Route

If you add a new Laravel route and need a new Wayfinder helper, regenerate routes before importing it.

```bash
php artisan wayfinder:generate
```

Then import the generated helper from `@/routes/...` or `@/actions/...`, depending on what Wayfinder generated.
