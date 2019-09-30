<?php

namespace App\Http\Controllers\Auth;

use DB;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ParagonIE\Halite\Alerts\InvalidMessage;

class RecoverController extends Controller
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
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    public function index()
    {
        return view('auth.recover');
    }

    protected function recoverPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|exists:users',
            'recovery_string' => 'required',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $this->incrementLoginAttempts($request);

        $recoveryString = $request->input('recovery_string');
        $user = User::whereEmail($request->input('email'))->firstOrFail();
        $newPassword = $request->input('password');
        $newRecoveryString = Str::random(32);

        try {
            $password = app('encrypt')->recoverPassword($recoveryString, $user);
        } catch (InvalidMessage $exception) {
            throw ValidationException::withMessages([
                'recovery_string' => 'Invalid recovery string',
            ])->status(429);
        }

        app('encrypt')->setKeyPair($user, $password);

        auth()->login($user);

        DB::beginTransaction();

        $user->public_key = app('encrypt')->changePassword($newPassword, $user);
        $user->recovery_string = app('encrypt')->recoveryString($newRecoveryString, $newPassword, $user);

        auth()->logoutOtherDevices($newPassword);

        DB::commit();

        auth()->logout();

        $this->clearLoginAttempts($request);

        return redirect()
            ->route('recover.success')
            ->withRecoveryString($newRecoveryString);
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function post(Request $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        return $this->recoverPassword($request);
    }

    public function success(Request $request)
    {
        return view('auth.recover-success', [
            'recoveryString' => session('recovery_string'),
        ]);
    }
}
