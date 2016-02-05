<?php

namespace App\Http\Controllers;

use Input, Validator;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @var \App\User
     */
    private $model;

    /**
     * @param \App\User $model
     */
    public function __construct(User $model)
    {
        $this->authorize('superadmin');

        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();

        unset($data['_token']);

        $validator = Validator::make($data, [
            'email' => 'required|unique:users|email',
            'role' => 'required|between:1,2',
            'password' => 'min:8',
        ], [
            'email.required'    => 'Email is required',
            'email.unique'      => 'Email already exists',
            'email.email'       => 'Invalid email address',
            'role.required'     => 'Role is required',
            'role.between'      => 'Role is invalid',
            'password.min'      => 'Password must be at least 8 characters long',
        ]);

        if ($validator->fails())
        {
            return redirect()
                ->back()
                ->withInput()
                ->withError($validator->errors()->first());
        }

        User::unguard();
        User::create($data);
        User::reguard();

        return redirect()
            ->route('users.index')
            ->withSuccess('User has been added');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try
        {
            $user = $this->model->findOrFail($id);

            return view('users.edit', compact('user'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('User not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        try
        {
            $row = User::findOrFail($id);

            $data = Input::except(['_method', '_token']);

            if (isset($data['password']) && empty($data['password']))
            {
                unset($data['password']);
            }

            if ($id == auth()->id())
            {
                unset($data['role']);
            }

            $row->unguard();

            $row->update($data);

            return redirect()
                ->route('users.index')
                ->withSuccess('The user has been saved');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            if ($id == auth()->id())
            {
                throw new \Exception('You can\'t delete yourself');
            }

            User::findOrFail($id)->delete();

            return redirect()
                ->back()
                ->withSuccess('User has been deleted');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('There was an error deleting the user');
        }
    }
}
