<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login()
    {
        /*
        request()->validate([
            'username' => ['required', 'alpha', "min:3"],
            'password' => ['required', 'min:8']
        ]);
        */

        $auth = auth()->attempt([
            'name' => request('username'),
            'password' => request('password')
        ]);

        if (!$auth) {
            return view('auth.login', ['fail' => true]);
        }

        return redirect('/');
    }
}
