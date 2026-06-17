# UI Design System Standard
## Laravel 13 + Vue + Tailwind CSS v4 + shadcn/ui

**Version:** 2.0.0  
**Style Direction:** Soft Modern · Super Clean · Compact Enterprise UI

---

# 1. Purpose

This document is the **single source of truth** for the application UI.

Every page, component, form, table, modal, dashboard card, and action button must follow this standard unless there is a strong usability reason to change it.

The UI should feel:

- Soft
- Clean
- Modern
- Light
- Compact
- Professional
- Calm
- Data-friendly
- Easy to scan

The target feeling is a **soft enterprise dashboard** with clean borders, light backgrounds, rounded cards, simple tables, gentle status colors, and minimal visual noise.

---

# 2. Design Personality

## Keywords

Use these words as the visual direction:

```txt
Soft
Modern
Clean
Compact
Rounded
Light
Readable
Minimal
Calm
Professional
```

## Avoid

```txt
Heavy shadows
Dark containers
Overly bright colors
Large paddings
Crowded toolbars
Too many primary buttons
Thick borders
Fancy gradients
Excessive animations
```

---

# 3. Global UI Rules

## Golden Rule

The interface must look like it belongs to **one complete system**, not different pages designed separately.

## Main Rules

- Use soft white or near-white backgrounds.
- Use cards with light borders.
- Use compact spacing.
- Use rounded but not overly round components.
- Use muted colors for secondary text.
- Use badges for statuses.
- Use icons for actions.
- Use dropdown actions only when there are 3 or more actions.
- Keep tables readable and clean.
- Avoid heavy shadows.
- Avoid unnecessary custom component overrides.

---

# 4. Background

## App Background

Use a very light background.

```css
background: #ffffff;
```

Optional soft page background:

```css
background: #fafafa;
```

## Page Sections

Large page containers should use:

```txt
bg-white
border
rounded-xl
```

Recommended Tailwind:

```html
<div class="rounded-xl border border-neutral-200 bg-white">
  ...
</div>
```

---

# 5. Color System

The app should not feel colorful everywhere. Colors are used mainly for:

- Status
- Actions
- Important highlights
- Dashboard summary cards

## Base Colors

| Usage | Color |
|---|---|
| Page Background | `#ffffff` or `neutral-50` |
| Card Background | `#ffffff` |
| Border | `neutral-200` |
| Text Primary | `neutral-950` |
| Text Secondary | `neutral-500` |
| Text Muted | `neutral-400` |
| Hover Background | `neutral-50` |

## Primary Color

Use black/neutral for primary buttons for a very clean enterprise look.

```txt
Primary: neutral-950
Hover: neutral-800
Text: white
```

Example:

```html
<Button class="h-9 rounded-md bg-neutral-950 px-3 text-sm font-medium text-white hover:bg-neutral-800">
  Create Ticket
</Button>
```

## Status Colors

Use soft backgrounds with stronger text.

| Status | Background | Text |
|---|---|---|
| Open | `bg-teal-100` | `text-teal-700` |
| Pending | `bg-amber-100` | `text-amber-700` |
| Closed | `bg-red-100` | `text-red-700` |
| Active | `bg-emerald-100` | `text-emerald-700` |
| Inactive | `bg-neutral-100` | `text-neutral-600` |
| Completed | `bg-blue-100` | `text-blue-700` |
| Draft | `bg-slate-100` | `text-slate-600` |

Do not use strong filled colors for badges unless the action is critical.

---

# 6. Typography

## Font

Use **Poppins** for the whole application.

```css
font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
```

Tailwind v4 theme example:

```css
@theme {
  --font-sans: 'Poppins', ui-sans-serif, system-ui, sans-serif;
}
```

## Font Size Standard

| Usage | Class |
|---|---|
| Page Title | `text-xl` |
| Section Title | `text-base` or `text-lg` |
| Card Number | `text-2xl` |
| Normal Text | `text-sm` |
| Table Text | `text-sm` |
| Helper Text | `text-xs` |
| Badge | `text-xs` |

## Font Weight

| Usage | Class |
|---|---|
| Page Title | `font-semibold` |
| Card Number | `font-semibold` |
| Table Header | `font-semibold` |
| Button | `font-medium` |
| Normal Text | `font-normal` |
| Description | `font-normal` |

Use bold text only for important labels, titles, and table primary values.

---

# 7. Spacing System

The application uses an **8px-based compact spacing system**.

## Recommended Spacing

```txt
gap-2
gap-3
gap-4
p-3
p-4
p-5
space-y-3
space-y-4
```

## Avoid

```txt
gap-8
p-8
py-8
text-lg everywhere
large empty sections
```

---

# 8. Radius System

Use subtle, restrained rounded corners.

| Component      | Radius         |
| -------------- | -------------- |
| Button         | `rounded-sm`   |
| Input          | `rounded-sm`   |
| Badge          | `rounded-full` |
| Card           | `rounded-lg`   |
| Page Container | `rounded-lg`   |
| Modal          | `rounded-lg`   |
| Dropdown       | `rounded-md`   |
| Avatar         | `rounded-full` |

Avoid using `rounded-xl` and above unless there is a specific UI or marketing requirement.

---

# 9. Borders and Shadows

## Border Style

Use light borders everywhere.

```html
border border-neutral-200
```

For softer sections:

```html
border border-neutral-100
```

## Shadows

Default UI should use no shadow or very soft shadow only.

Allowed:

```txt
shadow-sm
```

Rare:

```txt
shadow-md
```

Avoid:

```txt
shadow-lg
shadow-xl
shadow-2xl
```

Clean enterprise UI should rely more on **spacing, borders, and background**, not shadows.

---

# 10. Page Layout Standard

Every page should follow this structure:

```txt
Page Header Card
- Page title
- Breadcrumb

Main Content Card
- Summary cards
- Action bar
- Search / filters
- Table / content
- Pagination
```

## Page Header Example

```html
<div class="flex items-center justify-between rounded-xl border border-neutral-200 bg-white px-5 py-4">
  <h1 class="text-xl font-semibold text-neutral-950">Tickets App</h1>

  <Breadcrumb>
    Home / Tickets App
  </Breadcrumb>
</div>
```

## Page Content Example

```html
<div class="rounded-xl border border-neutral-200 bg-white p-5">
  ...
</div>
```

---

# 11. Header and Breadcrumb

## Page Header

Use a soft bordered header card.

```txt
rounded-xl
border
bg-white
px-5
py-4
```

## Breadcrumb

Breadcrumb should be small, clean, and right aligned when possible.

```html
<div class="text-sm text-neutral-500">
  <span class="text-neutral-950">Home</span>
  <span class="mx-2 text-neutral-300">/</span>
  <span>Tickets App</span>
</div>
```

---

# 12. Dashboard Summary Cards

Summary cards should feel soft and calm.

## Card Style

```txt
rounded-xl
border-0
px-5
py-7
text-center
```

## Recommended Colors

| Type | Background | Text |
|---|---|---|
| Total | `bg-neutral-100` | `text-neutral-950` |
| Pending | `bg-amber-50` | `text-amber-600` |
| Open | `bg-teal-50` | `text-teal-600` |
| Closed | `bg-red-50` | `text-red-600` |

## Example

```html
<div class="rounded-xl bg-amber-50 px-5 py-7 text-center">
  <div class="text-2xl font-semibold text-amber-600">2</div>
  <div class="mt-1 text-sm font-semibold text-amber-600">Pending Tickets</div>
</div>
```

## Rules

- All summary cards must have equal height.
- Use soft background colors.
- Do not add heavy shadow.
- Keep text centered for metric cards.
- Use `text-2xl` for numbers.
- Use `text-sm font-semibold` for labels.

---

# 13. Buttons

## Button Standard

```txt
h-9
px-3
text-sm
font-medium
rounded-md
```

## Primary Button

Use for main page action only.

```html
<Button class="h-9 rounded-md bg-neutral-950 px-3 text-sm font-medium text-white hover:bg-neutral-800">
  Create Ticket
</Button>
```

## Secondary Button

```html
<Button variant="outline" class="h-9 rounded-md border-neutral-200 px-3 text-sm font-medium">
  Cancel
</Button>
```

## Danger Button

Use for delete or destructive actions.

```html
<Button class="h-9 rounded-md bg-red-600 px-3 text-sm font-medium text-white hover:bg-red-700">
  Delete
</Button>
```

## Soft Danger Icon Button

For table delete action:

```html
<Button size="icon" class="h-8 w-8 rounded-md bg-red-100 text-red-600 hover:bg-red-200">
  <Trash2 class="h-4 w-4" />
</Button>
```

## Button Rules

- Only one primary button per section.
- Use icons only when they improve scanning.
- Use text buttons for main actions.
- Use icon-only buttons for table row actions.
- Always add tooltip for icon-only buttons.

---

# 14. Action Buttons

Actions must be predictable and consistent.

## 1 Action

Use icon-only button with tooltip.

```txt
[Edit]
```

## 2 Actions

Use icon-only buttons with tooltip.

```txt
[Edit] [Delete]
```

## 3 or More Actions

Use dropdown menu.

```txt
Actions ▼
- View
- Edit
- Delete
- Archive
```

## Table Action Button Style

```html
<div class="flex items-center justify-end gap-2">
  <TooltipProvider>
    <Tooltip>
      <TooltipTrigger as-child>
        <Button size="icon" variant="ghost" class="h-8 w-8 rounded-md bg-red-100 text-red-600 hover:bg-red-200">
          <Trash2 class="h-4 w-4" />
        </Button>
      </TooltipTrigger>
      <TooltipContent>Delete</TooltipContent>
    </Tooltip>
  </TooltipProvider>
</div>
```

---

# 15. Inputs

## Standard Input

```txt
h-9
rounded-md
border-neutral-200
text-sm
placeholder:text-neutral-400
```

Example:

```html
<Input class="h-9 rounded-md border-neutral-200 text-sm placeholder:text-neutral-400" placeholder="Find ticket..." />
```

## Search Input

Search inputs should be compact and aligned with action buttons.

```html
<div class="relative w-full max-w-60">
  <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" />
  <Input class="h-9 rounded-md border-neutral-200 pl-9 text-sm" placeholder="Find ticket..." />
</div>
```

## Input Rules

- Labels always go above inputs.
- Placeholder text must be muted.
- Inputs should not be taller than buttons.
- Use clear error messages below the input.

---

# 16. Forms

## Form Layout

```txt
Card
- Section title
- Fields
- Footer actions
```

## Standard Form Card

```html
<Card class="rounded-xl border-neutral-200 shadow-none">
  <CardHeader class="px-5 py-4">
    <CardTitle class="text-base font-semibold">Ticket Details</CardTitle>
  </CardHeader>
  <CardContent class="grid gap-4 px-5 pb-5">
    ...
  </CardContent>
</Card>
```

## Form Rules

- Use `gap-4` between fields.
- Use 2 columns only when the form is simple.
- Stack on mobile.
- Footer buttons must be right aligned.
- Use `Cancel` then `Save`.

```txt
Cancel  Save
```

---

# 17. Tables

Tables are a core part of the system. They must be compact, readable, and clean.

## Table Style

```txt
text-sm
horizontal borders only
no full grid
soft hover
compact row height
```

## Header

```txt
text-sm
font-semibold
text-neutral-950
bg-white
border-b
```

## Rows

```txt
border-b
hover:bg-neutral-50
transition-colors
```

## Cell Padding

```txt
py-3
px-2
```

For denser tables:

```txt
py-2
```

## Example

```html
<Table>
  <TableHeader>
    <TableRow class="border-b border-neutral-200">
      <TableHead class="h-10 text-sm font-semibold text-neutral-950">Id</TableHead>
      <TableHead class="h-10 text-sm font-semibold text-neutral-950">Ticket</TableHead>
      <TableHead class="h-10 text-sm font-semibold text-neutral-950">Assigned To</TableHead>
      <TableHead class="h-10 text-sm font-semibold text-neutral-950">Status</TableHead>
      <TableHead class="h-10 text-sm font-semibold text-neutral-950">Date</TableHead>
      <TableHead class="h-10 text-right text-sm font-semibold text-neutral-950">Action</TableHead>
    </TableRow>
  </TableHeader>

  <TableBody>
    <TableRow class="border-b border-neutral-200 hover:bg-neutral-50">
      <TableCell class="py-3 text-sm">1</TableCell>
      <TableCell class="py-3">
        <div class="font-semibold text-neutral-950">Sed ut perspiciatis unde omnis iste</div>
        <div class="mt-1 text-sm text-neutral-500">ab illo inventore veritatis et quasi...</div>
      </TableCell>
      <TableCell class="py-3">Liam</TableCell>
      <TableCell class="py-3">
        <Badge class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700 hover:bg-red-100">
          Closed
        </Badge>
      </TableCell>
      <TableCell class="py-3 text-neutral-600">Mon, Feb 20</TableCell>
      <TableCell class="py-3 text-right">...</TableCell>
    </TableRow>
  </TableBody>
</Table>
```

## Table Rules

- Use horizontal borders only.
- Avoid vertical borders.
- Use soft row hover.
- Keep headers readable.
- Use badges for status.
- Use avatars only when related to people/users.
- Use right aligned action column.
- Keep action buttons compact.

---

# 18. Status Badges

Badges should look soft, small, and readable.

## Badge Standard

```txt
rounded-full
px-2.5
py-0.5
text-xs
font-medium
```

## Examples

```html
<Badge class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700 hover:bg-red-100">
  Closed
</Badge>

<Badge class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700 hover:bg-amber-100">
  Pending
</Badge>

<Badge class="rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-medium text-teal-700 hover:bg-teal-100">
  Open
</Badge>
```

---

# 19. Avatars

Use avatars for assigned users, creators, approvers, and account menus.

## Avatar Size

| Usage | Size |
|---|---|
| Table | `h-8 w-8` |
| Header | `h-8 w-8` |
| Profile | `h-16 w-16` |

## Table Avatar with Name

```html
<div class="flex items-center gap-3">
  <Avatar class="h-8 w-8">
    <AvatarImage src="/avatar.png" />
    <AvatarFallback>LM</AvatarFallback>
  </Avatar>
  <span class="text-sm font-semibold text-neutral-950">Liam</span>
</div>
```

---

# 20. Search and Filter Bar

Search and filters should sit above the table.

## Layout

```txt
Left: Main action button
Right: Search / filters
```

Example:

```html
<div class="mb-5 flex items-center justify-between gap-3">
  <Button class="h-9 rounded-md bg-neutral-950 px-3 text-sm font-medium text-white hover:bg-neutral-800">
    Create Ticket
  </Button>

  <div class="flex items-center gap-2">
    <SearchInput />
    <FilterButton />
  </div>
</div>
```

## Rules

- Search should not be too wide by default.
- Recommended width: `max-w-60` or `w-60`.
- Filters can open in a dropdown, popover, or sheet.
- Do not place too many controls above the table.

---

# 21. Cards

Cards are used for:

- Page sections
- Forms
- Tables
- Dashboard summaries
- Information blocks

## Standard Card

```html
<Card class="rounded-xl border-neutral-200 bg-white shadow-none">
  <CardContent class="p-5">
    ...
  </CardContent>
</Card>
```

## Rules

- Prefer `shadow-none` or `shadow-sm`.
- Always use light border.
- Use `rounded-xl`.
- Use `p-5` for main content cards.
- Use `p-4` for smaller cards.

---

# 22. Sidebar

The sidebar should be compact and clean.

## Sidebar Rules

- Use icons for all main navigation items.
- Use soft hover background.
- Use clear active state.
- Avoid large menu item height.
- Support collapsed sidebar if possible.

## Menu Item

```txt
h-9
px-3
rounded-md
text-sm
font-medium
```

## Active State

```txt
bg-neutral-950
text-white
```

## Hover State

```txt
hover:bg-neutral-100
```

---

# 23. App Header

Header height:

```txt
56px
```

Header contains:

- Logo or app name
- Breadcrumb or module name
- Search if needed
- Notifications
- User menu

Use a bottom border:

```txt
border-b border-neutral-200
```

---

# 24. Modals

Use modals for focused decisions and short forms.

## Width

| Size | Width |
|---|---|
| Small | `max-w-md` |
| Medium | `max-w-2xl` |
| Large | `max-w-4xl` |

## Modal Rules

- Use `rounded-xl`.
- Keep content short.
- Do not stack modals.
- Buttons must be bottom right.
- Destructive confirmations must be clear.

## Delete Confirmation

```txt
Are you sure?
This action cannot be undone.

Cancel  Delete
```

---

# 25. Drawers / Sheets

Use sheets for:

- Quick edit
- View details
- Filters
- Side forms

Rules:

- Use right-side sheet for editing/details.
- Use compact form spacing.
- Keep footer sticky if form is long.

---

# 26. Tabs

Use simple underline or soft background tabs.

## Tab Rules

- Keep labels short.
- Use `text-sm`.
- Do not use large pill tabs unless needed.
- Use tabs only when content is strongly related.

---

# 27. Alerts and Toasts

## Toast

Position:

```txt
top-right
```

Duration:

```txt
4 seconds
```

## Alert Types

| Type | Background | Text |
|---|---|---|
| Success | `bg-emerald-50` | `text-emerald-700` |
| Warning | `bg-amber-50` | `text-amber-700` |
| Error | `bg-red-50` | `text-red-700` |
| Info | `bg-blue-50` | `text-blue-700` |

---

# 28. Loading States

## Button Loading

Use spinner and disable the button.

```txt
Saving...
```

## Table Loading

Use skeleton rows.

## Page Loading

Use centered loader only when the full page is loading.

## Card Loading

Use skeleton blocks.

---

# 29. Empty States

Every empty table or module must show a clean empty state.

## Empty State Includes

- Icon
- Title
- Short description
- Optional action button

Example:

```txt
No tickets found
Create a ticket to start tracking requests.

[Create Ticket]
```

---

# 30. Icons

Preferred icon library:

```txt
Lucide Icons
```

## Icon Sizes

| Usage | Size |
|---|---|
| Inputs | `h-4 w-4` |
| Buttons | `h-4 w-4` |
| Table Actions | `h-4 w-4` |
| Sidebar | `h-5 w-5` |
| Dashboard | `h-6 w-6` |

Rules:

- Use consistent icons per action.
- Do not mix many icon styles.
- Icon-only actions require tooltip.

---

# 31. Mobile Standard

## Rules

- Stack dashboard cards into 1 column.
- Stack form fields.
- Hide non-critical table columns if needed.
- Use sheet for filters.
- Sidebar becomes drawer.
- Keep buttons full width only when needed.

## Responsive Pattern

```txt
Mobile: 1 column
Tablet: 2 columns
Desktop: 4 columns when appropriate
```

---

# 32. Accessibility

## Requirements

- Minimum clickable height: `36px`.
- Use visible focus state.
- Keep readable contrast.
- Do not rely on color alone for status.
- Use labels for inputs.
- Use proper button text or aria labels.
- Icon-only buttons must have accessible labels.

Example:

```html
<Button aria-label="Delete ticket" size="icon">
  <Trash2 class="h-4 w-4" />
</Button>
```

---

# 33. Animation

Animations should be fast and subtle.

```txt
150ms to 200ms
```

Allowed:

```txt
hover transition
fade in
popover transition
accordion open/close
```

Avoid:

```txt
bouncy effects
slow animations
large motion
animated backgrounds
```

---

# 34. Approved shadcn/ui Components

Use these components as the official base:

- Button
- Card
- Input
- Textarea
- Select
- Checkbox
- Radio Group
- Switch
- Table
- Dropdown Menu
- Dialog
- Sheet
- Popover
- Tooltip
- Badge
- Tabs
- Avatar
- Breadcrumb
- Skeleton
- Toast / Sonner
- Alert
- Pagination
- Separator
- Scroll Area
- Calendar
- Date Picker
- Combobox
- Command
- Accordion
- Collapsible
- Hover Card
- Navigation Menu

---

# 35. Components to Avoid

Avoid unless strongly needed:

- Carousel
- Heavy chart widgets
- Fancy animated cards
- Nested dialogs
- Multiple floating action buttons
- Over-designed gradients
- Glassmorphism
- Large marketing-style hero sections inside admin pages

---

# 36. Ticket Page Reference Standard

The ticket page should follow this exact feeling:

## Layout

```txt
Header Card
Main Card
  Summary Cards
  Action/Search Bar
  Clean Table
```

## Visual Rules

- Header card uses white background, light border, rounded-xl.
- Main card uses white background, light border, rounded-xl, p-5.
- Summary cards use soft background colors.
- Table has horizontal borders only.
- Search input is compact and right aligned.
- Create button is black/neutral.
- Delete action uses soft red icon button.
- Status badges use soft background with strong readable text.
- Text hierarchy is clear: title bold, description muted.

---

# 37. CRUD Standard

## List Page

Must include:

- Header card
- Breadcrumb
- Summary cards if useful
- Action button
- Search / filters
- Table
- Pagination

## Create Page

Must include:

- Header card
- Form card
- Cancel and Save buttons

## Edit Page

Same layout as create page.

## View Page

Use read-only cards and clean labels.

## Delete

Always use confirmation dialog.

---

# 38. Tailwind Utility Reference

## Page Header

```txt
rounded-xl border border-neutral-200 bg-white px-5 py-4
```

## Main Card

```txt
rounded-xl border border-neutral-200 bg-white p-5
```

## Button

```txt
h-9 rounded-md px-3 text-sm font-medium
```

## Input

```txt
h-9 rounded-md border-neutral-200 text-sm
```

## Table Row

```txt
border-b border-neutral-200 hover:bg-neutral-50
```

## Badge

```txt
rounded-full px-2.5 py-0.5 text-xs font-medium
```

## Icon Button

```txt
h-8 w-8 rounded-md
```

---

# 39. Final Official Rule

All modules must follow this soft modern UI standard.

A page is considered approved only when it is:

- Clean
- Compact
- Consistent
- Light
- Readable
- Soft in color
- Predictable in actions
- Easy to use for data-heavy workflows

Do not override this design unless it significantly improves usability, accessibility, or a specific business workflow.
