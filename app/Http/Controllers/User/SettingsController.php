<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages.settings');
    }

    public function updateInfo()
    {
        $user = Auth::user();

        request()->validate([
            'username' => ['required', Rule::unique('users', 'username')->ignore($user->id), 'alpha'],
            'email' => ['required', Rule::unique('users', 'email')->ignore($user->id)]
        ]);

        $user->username = request('username');
        $user->email = request('email');
        $user->save();
        flash('Successfully updated username and email address.')->success();
        return back();
    }

    public function updatePassword()
    {
        request()->validate([
            'old_password' => ['required', new MatchOldPassword],
            'new_password' => ['required', 'confirmed', 'min:8']
        ]);

        $user = Auth::user();
        $user->password = Hash::make(request('new_password'));
        $user->save();
        flash('Successfully updated password.')->success();
        return back();
    }

    public function updateDisplaySettings()
    {
        $values = ['default', 'raw'];

        request()->validate([
            "display_image" => [Rule::in($values)],
            "display_video" => [Rule::in($values)],
            "display_audio" => [Rule::in($values)],
            "display_text" => [Rule::in($values)],
            "display_pdf" => [Rule::in($values)],
            "display_file" => [Rule::in($values)],
        ]);

        foreach (request()->all() as $key => $value) {
            $key = str_replace('_', '.', $key);
            if (Auth::user()->settings()->has($key)) {
                Auth::user()->settings()->set($key, $value);
            }
        }
        Auth::user()->save();
        flash('Successfully updated display settings.')->success();
        return back();
    }

    public function regenerateToken()
    {
        do {
            $token = Str::random(32);
        } while (User::where('access_token', '=', $token)->exists());

        $user = Auth::user();
        $user->access_token = $token;
        $user->save();
        return response($token, 200);
    }

    public function ShareX()
    {
        $user = Auth::user();
        return response($user->sharexConfig(), 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename=' . $user->username . '.sxcu'
        ]);
    }
}
