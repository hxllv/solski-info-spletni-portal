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
    
    public function send(Request $request, CreatesNewUsers $creator)
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

        if (!empty($data['class']) && $data['class'] != -1 && SchoolClass::where('id', $data['class'])->get()->count() === 0)
            return redirect()->back()->withErrors([
                'class' => 'Neveljavni razred.'
            ]);

        event(new Registered($creator->invite($request->all())));

        Notification::route('mail', $data['email'])->notify(new Invite($data));
    }

    public function store(Request $request, CreatesNewUsers $creator): RegisterResponse
    {
        $userId = $request->input('userId');

        if (!User::find($userId))
            abort(403, 'Prišlo je do napake.');

        event(new Registered($creator->inviteFinal($request->all())));

        return app(RegisterResponse::class);
    }

    public function index(Request $request) 
    {
        $email = $request->input('email');
        $role = $request->input('role');
        $class = $request->input('class');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $userId = $request->input('userId');

        if (User::where('email', $email)->where('is_registered', true)->get()->count() !== 0)
            abort(403, 'Email je že registriran.');

        if (User::where('id', $userId)->get()->count() !== 1)
            abort(403, 'Prišlo je do napake.');

        $url = URL::temporarySignedRoute('invited', now()->addMinutes(10), [
            'userId' => $userId,
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
