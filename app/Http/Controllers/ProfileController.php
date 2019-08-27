<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesUserProfiles;
use App\Http\Requests;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ManagesUserProfiles;

    public function index()
    {
        $user = auth()->user();

        return view('users.profile', compact('user'));
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            $password = $request->input('password');
            $data = $request->only(['password', 'name', 'email']);

            $data['profile'] = $this->profileSettings($request, $user->profile);

            $user->update($data);

            if (!$password) {
                return redirect()
                    ->back()
                    ->withSuccess('Your profile has been updated');
            }

            // update password below
            if ($password !== $request->input('password_confirmed')) {
                throw new ValidationException('Passwords do not match');
            }

            DB::beginTransaction();

            $user->public_key = app('encrypt')->changePassword($data['password']);

            auth()->logoutOtherDevices($data['password']);

            DB::commit();

            return redirect()
                ->back()
                ->withSuccess('Your profile has been updated')
                ->withCookie(cookie()->forever('key', $data['password']));
        } catch (QueryException $e) {
            $error = $e->getMessage();

            if (strpos($error, 'users_email_unique') !== false) {
                $error = 'Email address already exists';
            }

            return redirect()
                ->back()
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
