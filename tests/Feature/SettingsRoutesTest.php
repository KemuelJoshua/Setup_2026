<?php

use App\Http\Controllers\Admin\RolesAndPermissions\RoleController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use Illuminate\Support\Facades\Route;

test('settings routes use the expected urls names and components', function () {
    expect(route('admin.settings.index', absolute: false))->toBe('/admin/settings')
        ->and(route('admin.settings.profile.edit', absolute: false))->toBe('/admin/settings/profile')
        ->and(route('admin.settings.profile.update', absolute: false))->toBe('/admin/settings/profile')
        ->and(route('admin.settings.profile.destroy', absolute: false))->toBe('/admin/settings/profile')
        ->and(route('admin.settings.security.edit', absolute: false))->toBe('/admin/settings/security')
        ->and(route('admin.settings.user-password.update', absolute: false))->toBe('/admin/settings/password')
        ->and(route('admin.settings.appearance.edit', absolute: false))->toBe('/admin/settings/appearance')
        ->and(route('admin.settings.roles.index', absolute: false))->toBe('/admin/settings/roles');

    expect(Route::getRoutes()->getByName('admin.settings.profile.edit')?->getActionName())
        ->toBe(ProfileController::class.'@edit')
        ->and(Route::getRoutes()->getByName('admin.settings.security.edit')?->getActionName())
        ->toBe(SecurityController::class.'@edit')
        ->and(Route::getRoutes()->getByName('admin.settings.roles.index')?->getActionName())
        ->toBe(RoleController::class.'@index')
        ->and(Route::getRoutes()->getByName('admin.settings.appearance.edit')?->defaults['component'] ?? null)
        ->toBe('admin/settings/Appearance');
});
