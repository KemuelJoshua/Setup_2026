<?php

test('the application navbar spans above the sidebar and content', function () {
    $layout = file_get_contents(resource_path('js/layouts/app/AppSidebarLayout.vue'));
    $header = file_get_contents(resource_path('js/components/AppSidebarHeader.vue'));
    $sidebar = file_get_contents(resource_path('js/components/AppSidebar.vue'));

    expect($layout)
        ->toContain('<AppSidebarHeader :breadcrumbs="breadcrumbs" />')
        ->toContain('data-test="app-body"')
        ->and(strpos($layout, '<AppSidebarHeader'))
        ->toBeLessThan(strpos($layout, 'data-test="app-body"'))
        ->and($header)
        ->toContain('data-test="app-navbar"')
        ->toContain('<AppLogo />')
        ->toContain('Search items, transactions, documents...')
        ->and($sidebar)
        ->toContain('collapsible="offcanvas"')
        ->not->toContain('collapsible="icon"')
        ->not->toContain('<SidebarHeader')
        ->not->toContain('<AppLogo');
});

test('the sidebar always uses its dark color variables', function () {
    $styles = file_get_contents(resource_path('css/app.css'));
    $sidebar = file_get_contents(resource_path('js/components/AppSidebar.vue'));

    expect($styles)
        ->toContain('.app-sidebar {')
        ->toContain('--sidebar-background: hsl(220 16% 10%);')
        ->toContain('--sidebar-foreground: hsl(220 20% 96%);')
        ->and($sidebar)
        ->toContain('class="app-sidebar')
        ->toContain('bg-sidebar')
        ->not->toContain('dark:bg-sidebar');
});
