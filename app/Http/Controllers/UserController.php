<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ManagesUserProfiles;
use Validator;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use ManagesUserProfiles;

    protected function ensureGroups(Request $request, User $user)
    {
        // get desired groups from request
        $groups = $request->input('groups', []);

        if (!is_array($groups)) {
            $groups = [];
        }

        // I can only grant access to groups I belong to
        $groups = $request->user()
            ->groups()
            ->whereIn('groups.id', $groups)
            ->get();

        $user->groups()->sync($groups);

        // update encrypted store
        app('share')->refreshUserPermissions($user);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('admin');

        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('admin');

        return view('users.create');
    }

    protected function validator($data, $updateId = 0)
    {
        $this->authorize('admin');

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('admin');

        $data = $request->only(['name', 'email', 'password', 'role']);

        $validator = $this->validator($data);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withError($validator->errors()->first());
        }

        $key = app('encrypt')->generateEncryptionKey($data['password']);

        $user = User::create($data);

        $user->salt = $key['salt'];
        $user->public_key = $key['publicKey'];

        $user->save();

        $this->ensureGroups($request, $user);

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
            $this->authorize('admin');

            $user = User::findOrFail($id)->load('permissions');

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
     * @param  int    $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {
            $this->authorize('admin');

            $user = User::findOrFail($id);
            $data = $request->only(['name', 'email', 'role']);

            $this->ensureGroups($request, $user);

            $validator = $this->validator($data, $id);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withError($validator->errors()->first());
            }

            if ($id == auth()->id()) {
                unset($data['role']);
            }

            $user->update($data);

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
            $this->authorize('admin');

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
