<?php

namespace App\Http\Controllers;

use App\IpField;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IpFieldController extends Controller
{
    public function index()
    {
        $fields = IpField::all();

        return view('ip-fields.index', compact('fields'));
    }

    public function create()
    {
        return view('ip-fields.create');
    }

    public function store(Request $request)
    {
        try {
            IpField::create($request->input());

            return redirect()
                ->route('ip-fields.index')
                ->withSuccess('Field has been added');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError($e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $field = IpField::findOrFail($id);

            return view('ip-fields.edit', compact('field'));
        } catch (\Exception $e) {
            return redirect()
                ->route('ip-fields.index')
                ->withError('Could not find field');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $field = IpField::findOrFail($id);

            $field->update($request->input());

            $field->save();

            return redirect()
                ->route('ip-fields.index')
                ->withSuccess('Field has been saved');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError($e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('st') as $key => $value) {
            $row = IpField::find($value);

            if ($row) {
                $row->sort = $key;
                $row->save();
            }
        }
    }

    public function destroy($id)
    {
        try {
            $field = IpField::findOrFail($id);

            $field->delete();

            return redirect()
                ->route('ip-fields.index')
                ->withSuccess('Field has been deleted');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find field');
        }
    }

}
