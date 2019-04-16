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

    protected function validator($data, $updateId = 0)
    {
        $updateSelf = intval($updateId) === auth()->id();

        return Validator::make($data, [
            'email' => "required|unique:users,email,{$updateId}|email",
            'role' => ($updateSelf ? '' : 'required|') . 'between:1,3',
            'password' => 'min:8',
            'devices_per_page' => 'in:10,25,50,100',
        ], [
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'email.email' => 'Invalid email address',
            'role.required' => 'Role is required',
            'role.between' => 'Role is invalid',
            'password.min' => 'Password must be at least 8 characters long',
        ]);
    }

    protected function profileSettings(Request $request, array $current = [])
    {
        if (in_array($request->input('devices_per_page'), [10, 25, 50, 100])) {
            $current['devices_per_page'] = $request->input('devices_per_page');
        }

        return $current;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Input::only(['name', 'email', 'password', 'role']);

        $permissions = $request->input('permissions');

        $validator = $this->validator($data);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withError($validator->errors()->first());
        }

        $data['profile'] = $this->profileSettings($request);

        User::unguard();
        $row = User::create($data);
        User::reguard();

        if ( ! is_array($permissions)) {
            $permissions = [];
        }

        $row->syncPermissions($permissions);

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
        try {
            $user = $this->model->findOrFail($id)->load('permissions');

            return view('users.edit', compact('user'));
        } catch (\Exception $e) {
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
    public function update($id, Request $request)
    {
        try {
            $row = User::findOrFail($id);

            $data = Input::only(['name', 'email', 'password', 'role']);
            $permissions = Input::get('permissions');
            $validator = $this->validator($data, $id);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withError($validator->errors()->first());
            }

            if (isset($data['password']) && empty($data['password'])) {
                unset($data['password']);
            }

            if ($id == auth()->id()) {
                unset($data['role']);
            }

            $row->unguard();

            $data['profile'] = $this->profileSettings($request, $row->profile);

            $row->update($data);

            if (!is_array($permissions)) {
                $permissions = [];
            }

            $row->syncPermissions($permissions);

            return redirect()
                ->route('users.index')
                ->withSuccess('The user has been saved');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if ($id == auth()->id()) {
                throw new \Exception('You can\'t delete yourself');
            }

            User::findOrFail($id)->delete();

            return redirect()
                ->back()
                ->withSuccess('User has been deleted');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError('There was an error deleting the user');
        }
    }
}
