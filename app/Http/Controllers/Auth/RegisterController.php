<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use ThrottlesLogins;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show user registration form
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.register');
    }

    /**
     * Register the user
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function post(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $password = $request->input('password');
        $recoveryString = Str::random(32);

        $key = app('encrypt')->generateEncryptionKey($password);

        User::forceCreate([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($password),
            'role' => User::ROLE_USER,
            'public_key' => $key['publicKey'],
            'salt' => $key['salt'],
            'recovery_string' => app('encrypt')->recoveryString($recoveryString, $password, base64_decode($key['salt'])),
        ]);

        return view('auth.register-success')
            ->withRecoveryString($recoveryString);
    }

    /**
     * Show registration successful page
     *
     * @return \Illuminate\View\View
     */
    public function success()
    {
        return view('auth.register-success', [
            'recoveryString' => session('recovery_string'),
        ]);
    }
}
