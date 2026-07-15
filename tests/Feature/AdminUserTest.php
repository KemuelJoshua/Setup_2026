<?php

use App\Actions\User\CreateUserAction;
use App\Actions\User\DestroyUserAction;
use App\Actions\User\IndexUserAction;
use App\Actions\User\UpdateUserAction;
use App\Data\Users\UserData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('it creates an admin user', function () {
    $data = new UserData(
        name: 'John Doe',
        email: 'johndoe@gmail.com',
        password: 'password',
    );

    $action = app(CreateUserAction::class);

    $user = $action->execute($data);

    expect($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('johndoe@gmail.com');

    $createdUser = User::where('email', 'johndoe@gmail.com')->firstOrFail();

    expect($createdUser->name)->toBe('John Doe')
        ->and(Hash::check('password', $createdUser->password))->toBeTrue();
});

test('it updates an admin user', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@gmail.com',
    ]);

    $data = new UserData(
        name: 'John Doe',
        email: 'johndoe@gmail.com',
        password: 'password',
    );

    $updatedUser = app(UpdateUserAction::class)->execute(
        id: $user->id,
        data: $data,
    );

    expect($updatedUser)
        ->name->toBe('John Doe')
        ->email->toBe('johndoe@gmail.com');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'John Doe',
        'email' => 'johndoe@gmail.com',
    ]);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
        'email' => 'original@gmail.com',
    ]);
});

test('it updates an admin user without replacing the password when one is not provided', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@gmail.com',
        'password' => 'current-password',
    ]);

    $data = new UserData(
        name: 'John Doe',
        email: 'johndoe@gmail.com',
    );

    app(UpdateUserAction::class)->execute(
        id: $user->id,
        data: $data,
    );

    $user->refresh();

    expect($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('johndoe@gmail.com')
        ->and(Hash::check('current-password', $user->password))->toBeTrue();
});

test('it indexes admin users', function () {
    User::factory()->count(3)->create();

    $users = app(IndexUserAction::class)->execute([
        'per_page' => 2,
    ]);

    expect($users->total())->toBe(3)
        ->and($users->perPage())->toBe(2)
        ->and($users->items())->toHaveCount(2);
});

test('it searches admin users by name and email', function () {
    User::factory()->create([
        'name' => 'Jane Manager',
        'email' => 'jane@example.com',
    ]);
    User::factory()->create([
        'name' => 'Support Agent',
        'email' => 'helpdesk@example.com',
    ]);
    User::factory()->create([
        'name' => 'Customer',
        'email' => 'customer@example.com',
    ]);

    $users = app(IndexUserAction::class)->execute([
        'search' => 'example',
        'per_page' => 15,
    ]);

    expect($users->total())->toBe(3);

    $users = app(IndexUserAction::class)->execute([
        'search' => 'helpdesk',
        'per_page' => 15,
    ]);

    expect($users->total())->toBe(1)
        ->and($users->first()->email)->toBe('helpdesk@example.com');
});

test('it deletes an admin user', function () {
    $user = User::factory()->create();

    $isDeleted = app(DestroyUserAction::class)->execute((string) $user->id);

    expect($isDeleted)->toBeTrue();
    $this->assertModelMissing($user);
});

test('it returns false when deleting a missing admin user', function () {
    $isDeleted = app(DestroyUserAction::class)->execute('999');

    expect($isDeleted)->toBeFalse();
});
