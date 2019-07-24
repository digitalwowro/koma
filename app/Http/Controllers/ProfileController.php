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
            $data = $request->only(['password', 'name', 'email']);

            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                if ($data['password'] !== $request->input('password_confirmed')) {
                    throw new ValidationException('Passwords do not match');
                }

                DB::beginTransaction();
                $user->public_key = app('encrypt')->changePassword($data['password']);
            }

            $data['profile'] = $this->profileSettings($request, $user->profile);

            $user->update($data);

            if (empty($data['password'])) {
                return redirect()
                    ->back()
                    ->withSuccess('Your profile has been updated');
            } else {
                DB::commit();

                auth()->logoutOtherDevices($data['password']);

                return redirect()
                    ->back()
                    ->withSuccess('Your profile has been updated')
                    ->withCookie(cookie()->forever('key', $data['password']));
            }
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
