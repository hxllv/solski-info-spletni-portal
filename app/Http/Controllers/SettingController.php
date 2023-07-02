<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index() : Response {
        $this->authorize('view', Setting::class);

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        return Inertia::render('Admin/Settings', 
        [
            'hours' => Setting::find('timetable.hours'),
            'permission' => $permissions
        ]);
    }

    public function update(Setting $setting, Request $request) {
        $this->authorize('edit', Setting::class);

        $input = $request->all();

        Validator::make($input, [
            'value' => ['required', 'json']
        ])->validate();

        if ($setting->name === 'timetable.hours') {
            $json = json_decode($input['value']);

            foreach ($json as $value) {
                if (!is_numeric($value->index))
                    return redirect()->back()->withErrors([
                        'value' => 'Indeks mora biti število.'
                    ]);
                if (strlen(substr(strrchr($value->index, "."), 1)) > 1)
                    return redirect()->back()->withErrors([
                        'value' => 'Indeks lahko ima največ eno decimalno mesto.'
                    ]);
            }
        }

        $setting->forceFill([
            'value' => $input['value'],
        ])->save();
    }
}
