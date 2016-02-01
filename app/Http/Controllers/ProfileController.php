<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('users.profile', compact('user'));
    }

    public function update()
    {
        try
        {
            $row = auth()->user();

            $data = Input::except(['_method', '_token', 'role']);

            if (isset($data['password']) && empty($data['password']))
            {
                unset($data['password']);
            }

            $row->update($data);

            return redirect()
                ->back()
                ->withSuccess('Your profile has been updated');
        }
        catch (QueryException $e)
        {
            $error = $e->getMessage();

            if (strpos($error, 'users_email_unique') !== false)
            {
                $error = 'Email address already exists';
            }

            return redirect()
                ->back()
                ->withError($error);
        }
    }

}
