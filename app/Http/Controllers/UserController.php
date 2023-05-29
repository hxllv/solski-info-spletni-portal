<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users', 
            [
                'roles' => Role::all(),
                'users' => User::all(),
                'can' => [
                    'invite' => auth()->user()->can('invite', User::class)
                ]
            ]
        );
    }
}
