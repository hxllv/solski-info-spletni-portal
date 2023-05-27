<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class InviteController extends Controller
{
    public function create()
    {
        $this->authorize('invite', User::class);
        return view('userinvites.create');
    }

    public function store()
    {

    }
}
