<?php

use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('requires central authentication to manage categories', function () {
    $this->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->get(route('central.categories.index'))
        ->assertRedirect(route('login'));
});

it('lists and searches categories', function () {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);
    Category::factory()->create(['name' => 'Basic Education']);
    Category::factory()->create(['name' => 'Higher Education']);

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->get(route('central.categories.index', ['search' => 'Higher', 'per_page' => 10]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('central/categories/Index')
            ->where('filters.search', 'Higher')
            ->where('filters.per_page', 10)
            ->has('categories.data', 1)
            ->where('categories.data.0.name', 'Higher Education'));
});

it('creates and updates a category', function () {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->post(route('central.categories.store'), [
            'name' => 'Technical Schools',
            'is_active' => true,
        ])
        ->assertRedirect(route('central.categories.index', absolute: false));

    $category = Category::query()->where('name', 'Technical Schools')->firstOrFail();

    expect($category->is_active)->toBeTrue();

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->patch(route('central.categories.update', $category), [
            'name' => 'Technical and Vocational Schools',
            'is_active' => false,
        ])
        ->assertRedirect(route('central.categories.index', absolute: false));

    expect($category->refresh())
        ->name->toBe('Technical and Vocational Schools')
        ->is_active->toBeFalse();
});

it('validates category fields', function () {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);
    Category::factory()->create(['name' => 'College']);

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->post(route('central.categories.store'), [
            'name' => 'College',
            'is_active' => 'invalid',
        ])
        ->assertSessionHasErrors(['name', 'is_active']);
});

it('connects tenants to categories and prevents deleting an assigned category', function () {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);
    $category = Category::factory()->create();

    $tenant = new Tenant;
    $tenant->forceFill([
        'id' => 'school-a',
        'category_id' => $category->getKey(),
        'school_code' => 'SCHOOL-A',
        'school_name' => 'School A',
    ])->saveQuietly();

    expect(Tenant::query()->firstOrFail()->category->is($category))->toBeTrue()
        ->and($category->tenants()->count())->toBe(1);

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->delete(route('central.categories.destroy', $category))
        ->assertSessionHasErrors('category');

    expect($category->fresh())->not->toBeNull();

    Tenant::withoutEvents(fn () => Tenant::query()->delete());

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->delete(route('central.categories.destroy', $category))
        ->assertRedirect(route('central.categories.index', absolute: false));

    expect($category->fresh())->toBeNull();
});
