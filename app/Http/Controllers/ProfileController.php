<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ManagesUserProfiles;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
            $row = auth()->user();

            $data = $request->input();

            unset($data['role']);

            if (isset($data['password']) && empty($data['password'])) {
                unset($data['password']);
            }

            $data['profile'] = $this->profileSettings($request, $row->profile);

            $row->update($data);

            return redirect()
                ->back()
                ->withSuccess('Your profile has been updated');
        } catch (QueryException $e) {
            $error = $e->getMessage();

            if (strpos($error, 'users_email_unique') !== false) {
                $error = 'Email address already exists';
            }

            return redirect()
                ->back()
                ->withError($error);
        }
    }

}
