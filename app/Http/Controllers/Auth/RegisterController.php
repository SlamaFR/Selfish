<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        if (Setting::get('app.registrations') == '1') {
            return view('auth.register');
        }
        flash(__('auth.registrations.disabled'))->error();
        return redirect('/login');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        if (Setting::get('app.registrations') == '1') {
            dd(true);
            return $this->register($request);
        }
        flash(__('auth.registrations.disabled'))->error();
        return redirect($this->redirectTo);
    }

    public static function registrationValidator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'alpha', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        return $this::registrationValidator($data);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        do {
            $code = Str::random(5);
        } while (User::where('code', '=', $code)->exists());

        do {
            $token = Str::random(32);
        } while (User::where('access_token', '=', $token)->exists());

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'code' => $code,
            'password' => Hash::make($data['password']),
            'admin' => false,
            'access_token' => $token,
            'disk_quota' => 0,
            'max_disk_quota' => 0
        ]);

        $user->settings()->setMultiple([
            'display.image' => 'default',
            'display.video' => 'default',
            'display.audio' => 'default',
            'display.text' => 'default',
            'display.pdf' => 'default',
            'display.zip' => 'default',
            'display.file' => 'default',
            'disk.max_quota' => 'default',
            'disk.auto_delete' => '0',
        ]);
        $user->save();

        return $user;
    }
}
