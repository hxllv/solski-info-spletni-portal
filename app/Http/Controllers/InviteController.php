<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Invite;

class InviteController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|numeric'
        ]);

        Notification::route('mail', $data['email'])->notify(new Invite());
    }
}
