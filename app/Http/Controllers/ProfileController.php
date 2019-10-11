<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesUserProfiles;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    use ManagesUserProfiles;

    public function index()
    {
        $user = auth()->user();

        return view('users.profile', compact('user'));
    }

    /**
     * Change user password
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    protected function changePassword(Request $request)
    {
        $user = $request->user();
        $password = $request->input('password');
        $recoveryString = Str::random(32);

        if ($password !== $request->input('password_confirmed')) {
            throw new ValidationException('Passwords do not match');
        }

        DB::beginTransaction();

        $user->public_key = app('encrypt')->changePassword($password, $request->user());
        $user->recovery_string = app('encrypt')->recoveryString($recoveryString, $password, $user);

        auth()->logoutOtherDevices($password);

        DB::commit();

        return redirect()
            ->route('profile')
            ->withSuccess('Your profile has been updated')
            ->withCookie(cookie()->forever('key', $password))
            ->withRecoveryString($recoveryString);
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            $data = $request->only(['name', 'email']);

            $data['profile'] = $this->profileSettings($request, $user->profile);

            $user->update($data);

            if ($request->filled('password')) {
                return $this->changePassword($request);
            }

            return redirect()
                ->route('profile')
                ->withSuccess('Your profile has been updated');
        } catch (QueryException $e) {
            $error = $e->getMessage();

            if (strpos($error, 'users_email_unique') !== false) {
                $error = 'Email address already exists';
            }

            return redirect()
                ->route('profile')
                ->withInput()
                ->withError($error);
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError($e->getMessage());
        }
    }

}
