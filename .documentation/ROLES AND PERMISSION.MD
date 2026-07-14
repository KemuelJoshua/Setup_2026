# Roles And Permissions Guide

## 1. Current Permissions

```text
admin view roles
admin create roles
admin update roles
admin delete roles
```

## 2. Current Default Roles

```text
Superadmin
Teacher
Student
```

## 3. Add New Permissions

Edit:

```text
database/seeders/permissions/PermissionSeeder.php
```

Add permissions:

```php
$permissions = [
    'admin view roles',
    'admin create roles',
    'admin update roles',
    'admin delete roles',

    'admin view courses',
    'admin create courses',
    'admin update courses',
    'admin delete courses',
];
```

## 4. Run Seeders

```bash
php artisan db:seed --class="Database\\Seeders\\permissions\\PermissionSeeder"
php artisan db:seed --class="Database\\Seeders\\permissions\\RoleSeeder"
php artisan permission:cache-reset
```

## 5. Protect A Controller Action

```php
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

public function index(): Response
{
    Gate::authorize('admin view courses');

    return Inertia::render('Courses/Index');
}
```

## 6. Use One Permission Per Action

```php
Gate::authorize('admin view courses');
Gate::authorize('admin create courses');
Gate::authorize('admin update courses');
Gate::authorize('admin delete courses');
```

## 7. Assign Permissions To A Role

```php
use Spatie\Permission\Models\Role;

$role = Role::findByName('Teacher', 'web');

$role->givePermissionTo([
    'admin view courses',
    'admin create courses',
    'admin update courses',
]);
```

## 8. Assign Role To A User

```php
$user->assignRole('Teacher');
```

## 9. Give Permission Directly To A User

```php
$user->givePermissionTo('admin view courses');
```

## 10. Check A User Permission

```php
if ($user->can('admin view courses')) {
    // Allowed.
}
```

## 11. Check A User Role

```php
if ($user->hasRole('Teacher')) {
    // User is a teacher.
}
```

## 12. Create Role Routes

Current role routes:

```php
use App\Http\Controllers\Admin\RolesAndPermissions\RoleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('administration')->name('administration.')->group(function () {
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });
});
```

## 13. Use Role Route Names

```php
route('administration.roles.index');
route('administration.roles.store');
route('administration.roles.update', $role);
route('administration.roles.destroy', $role);
```

## 14. Use Wayfinder In Vue

```ts
import { destroy, index, store, update } from '@/routes/administration/roles';
```

```vue
<Form v-bind="store.form()">
    <!-- fields -->
</Form>
```

```vue
<Form v-bind="update.form(role.id)">
    <!-- fields -->
</Form>
```

```ts
router.visit(index({ query: { search: searchQuery.value } }));
```

## 15. Create A Role From A Request

```php
use App\Actions\Roles\CreateRole;
use App\Http\Requests\RolesAndPermissions\StoreRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

public function store(StoreRoleRequest $request, CreateRole $createRole): RedirectResponse
{
    Gate::authorize('admin create roles');

    $createRole->execute($request->validated());

    return redirect()
        ->route('administration.roles.index')
        ->with('success', 'Role created successfully.');
}
```

## 16. Update A Role From A Request

```php
use App\Actions\Roles\UpdateRole;
use App\Http\Requests\RolesAndPermissions\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

public function update(UpdateRoleRequest $request, Role $role, UpdateRole $updateRole): RedirectResponse
{
    Gate::authorize('admin update roles');

    $updateRole->execute($role, $request->validated());

    return redirect()
        ->route('administration.roles.index')
        ->with('success', 'Role updated successfully.');
}
```

## 17. Delete A Role

```php
use App\Actions\Roles\DeleteRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

public function destroy(Role $role, DeleteRole $deleteRole): RedirectResponse
{
    Gate::authorize('admin delete roles');

    $deleteRole->execute($role);

    return redirect()
        ->route('administration.roles.index')
        ->with('success', 'Role deleted successfully.');
}
```

## 18. Store Role Request Rules

```php
return [
    'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
    'permissions' => ['nullable', 'array'],
    'permissions.*' => [
        'integer',
        Rule::exists(Permission::class, 'id')->where('guard_name', 'web'),
    ],
];
```

## 19. Update Role Request Rules

```php
return [
    'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique(Role::class, 'name')
            ->where('guard_name', $guardName)
            ->ignore($role),
    ],
    'permissions' => ['nullable', 'array'],
    'permissions.*' => [
        'integer',
        Rule::exists(Permission::class, 'id')->where('guard_name', $guardName),
    ],
];
```

## 20. Test Permission Access

```php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('authorized users can view courses', function () {
    Permission::create([
        'name' => 'admin view courses',
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('admin view courses');

    $this
        ->actingAs($user)
        ->get(route('courses.index'))
        ->assertOk();
});
```

## 21. Test Forbidden Access

```php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users without permission cannot view courses', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('courses.index'))
        ->assertForbidden();
});
```

## 22. Run Tests

```bash
php artisan test --compact tests/Feature/RoleControllerTest.php
php artisan test --compact tests/Feature/RoleControllerAuthorizationTest.php
php artisan test --compact tests/Feature/RoleIndexTest.php
php artisan test --compact tests/Feature/RoleStoreTest.php
php artisan test --compact tests/Feature/RolePermissionAssignmentTest.php
```

## 23. Current Files

```text
app/Http/Controllers/Admin/RolesAndPermissions/RoleController.php
app/Http/Requests/RolesAndPermissions/StoreRoleRequest.php
app/Http/Requests/RolesAndPermissions/UpdateRoleRequest.php
app/Actions/Roles/CreateRole.php
app/Actions/Roles/UpdateRole.php
app/Actions/Roles/DeleteRole.php
database/seeders/permissions/PermissionSeeder.php
database/seeders/permissions/RoleSeeder.php
resources/js/pages/RolesAndPermissions/Index.vue
resources/js/components/AppSidebar.vue
routes/web.php
```
