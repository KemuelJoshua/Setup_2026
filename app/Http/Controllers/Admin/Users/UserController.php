<?php

namespace App\Http\Controllers\Admin\Users;

use App\Actions\User\IndexUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request, IndexUserAction $action)
    {
        $filters = [
            'per_page' => $request->input('per_page', 15),
            'search' => $request->string('search')->trim()->toString() ?: null,
        ];

        $users = $action->execute($filters);

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'filters' => $request->only(['per_page', 'search']),
        ]);
    }
    
}
