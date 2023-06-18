<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Invite;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Role;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Actions\Fortify\PasswordValidationRules;
use App\Models\SchoolClass;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Auth\Events\Registered;

use function PHPUnit\Framework\isEmpty;

class InviteController extends Controller
{
    use PasswordValidationRules;

    protected $guard;

    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }
    
    public function send()
    {
        $this->authorize('invite', User::class);

        $data = request()->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|numeric',
            'class' => 'required|numeric'
        ]);

        if (User::where('email', $data['email'])->get()->count() !== 0)
            return redirect()->back()->withErrors([
                'email' => 'Email je že registriran.'
            ]);

        if (Role::where('id', $data['role'])->get()->count() === 0)
            return redirect()->back()->withErrors([
                'role' => 'Neveljavna skupina pravic.'
            ]);

        if (!empty($data['class']) && $$data['class'] != -1 && SchoolClass::where('id', $data['class'])->get()->count() === 0)
            return redirect()->back()->withErrors([
                'class' => 'Neveljavni razred.'
            ]);

        Notification::route('mail', $data['email'])->notify(new Invite($data));
    }

    public function store(Request $request, CreatesNewUsers $creator): RegisterResponse
    {
        $email = $request->input('email');
        $role = $request->input('role');
        $class = $request->input('class');

        if (User::where('email', $email)->get()->count() !== 0)
            abort(403, 'Email is already registered.');

        if (Role::where('id', $role)->get()->count() === 0)
            abort(403, 'Invalid role group.');

        if (!empty($class) && SchoolClass::where('id', $class)->get()->count() === 0)
            abort(403, 'Invalid class.');

        event(new Registered($creator->invite($request->all())));

        return app(RegisterResponse::class);
    }

    public function index(Request $request) 
    {
        $email = $request->input('email');
        $role = $request->input('role');
        $class = $request->input('class');
        $name = $request->input('name');
        $surname = $request->input('surname');

        if (User::where('email', $email)->get()->count() !== 0)
            abort(403, 'Email is already registered.');

        $url = URL::temporarySignedRoute('invited', now()->addMinutes(30), [
            'role' => $role,
            'class' => $class,
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
        ]);

        return Inertia::render('Auth/Invite', 
            [
                'email' => $email,
                'role' => $role,
                'class' => $class,
                'name' => $name,
                'surname' => $surname,
                'urlPost' => $url
            ]
        );
    }
}
