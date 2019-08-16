<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ManagesUserProfiles;
use App\Group;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    use ManagesUserProfiles;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('admin');

        return view('groups.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('admin');

        return view('groups.create');
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

        $name = $request->input('name');

        if (empty($name)) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Group name is required');
        }

        $group = Group::create(compact('name'));

        $request->user()->groups()->attach($group->id);

        return redirect()
            ->route('groups.index')
            ->withSuccess('Group has been added');
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

            $group = Group::findOrFail($id);

            return view('groups.edit', compact('group'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Group not found');
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

            $group = Group::findOrFail($id);
            $name = $request->input('name');

            if (empty($name)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withError('Group name is required');
            }

            $group->name = $name;
            $group->save();

            return redirect()
                ->route('groups.index')
                ->withSuccess('The group has been saved');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Invalid group');
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

            Group::findOrFail($id)->delete();

            return redirect()
                ->back()
                ->withSuccess('Group has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('There was an error deleting the group');
        }
    }
}
