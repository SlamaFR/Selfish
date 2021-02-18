<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function loginForm()
    {
        if (auth()->check())
        {
            return redirect('/');
        }

        return view('auth.login');
    }

    public function login()
    {
        $auth = auth()->attempt([
            'username' => request('username'),
            'password' => request('password')
        ], request('remember_me'));

        if (!$auth) {
            return view('auth.login', ['fail' => true]);
        }

        return redirect('/');
    }

    public function signupForm()
    {
        return view('auth.signup');
    }

    public function signup()
    {
        request()->validate([
            'email' => ['email', 'required'],
            'username' => ['alpha', 'required'],
            'password' => ['min:8', 'required', 'confirmed']
        ]);

        if (\App\Models\User::where('username', '=', request('username'))->exists())
        {
            return view('auth.signup', ['usernameExists' => true]);
        }

        if (\App\Models\User::where('email', '=', request('email'))->exists())
        {
            return view('auth.signup', ['emailExists' => true]);
        }

        \App\Models\User::create([
            'email' => request('email'),
            'username' => request('username'),
            'password' => bcrypt(request('password'))
        ]);
    }
}
