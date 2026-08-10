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

test('the sidebar always uses the application dark color variables', function () {
    $sidebar = file_get_contents(resource_path('js/components/AppSidebar.vue'));

    expect($sidebar)
        ->toContain('class="dark')
        ->toContain('bg-sidebar')
        ->not->toContain('app-sidebar');
});
