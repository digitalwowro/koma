<?php

namespace App\Http\Controllers;

use App\IpField;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IpFieldController extends Controller
{
    /**
     * @var \App\IpField
     */
    private $model;

    /**
     * IpFieldController constructor.
     *
     * @param \App\IpField $model
     */
    public function __construct(IpField $model)
    {
        $this->model = $model;

        $this->authorize('admin');
    }

    public function index()
    {
        $fields = $this->model->all();

        return view('ip-fields.index', compact('fields'));
    }

    public function create()
    {
        return view('ip-fields.create');
    }

    public function store(Request $request)
    {
        try
        {
            $this->model->create($request->input());

            return redirect()
                ->route('ip-fields.index')
                ->withSuccess('Field has been added');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError($e->getMessage());
        }
    }

    public function edit($id)
    {
        try
        {
            $field = $this->model->findOrFail($id);

            return view('ip-fields.edit', compact('field'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->route('ip-fields.index')
                ->withError('Could not find field');
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $field = $this->model->findOrFail($id);

            $field->update($request->input());

            $field->save();

            return redirect()
                ->route('ip-fields.index')
                ->withSuccess('Field has been saved');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError($e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('st') as $key => $value)
        {
            $row = $this->model->find($value);

            if ($row)
            {
                $row->sort = $key;

                $row->save();
            }
        }
    }
}
