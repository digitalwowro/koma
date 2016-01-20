<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\IpCategory;
use App\IpAddress;
use App\IpField;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class IpController extends Controller
{
    /**
     * @var \App\IpAddress
     */
    private $model;

    /**
     * @var \App\IpCategory
     */
    private $ipCategory;

    /**
     * @var \App\IpField
     */
    private $fields;

    public function __construct(IpAddress $model, IpCategory $ipCategory, IpField $fields)
    {
        $this->model      = $model;
        $this->ipCategory = $ipCategory;
        $this->fields     = $fields;
    }

    public function index($category)
    {
        //try
        //{
            $ipCategory = $this->ipCategory->findOrFail($category);
            $ips        = $ipCategory->ips;
            $colspan    = 1;
            $fields     = $this->fields->all();

            foreach ($fields as $field)
            {
                if ($field->showInDeviceList())
                {
                    $colspan++;
                }
            }

            return view('ips.index', compact('ipCategory', 'ips', 'colspan', 'fields'));
        //}
        //catch (\Exception $e)
        //{
        //    return redirect()
        //        ->back()
        //        ->withError('Could not find IP Address');
        //}
    }

    public function create($category)
    {
        $this->authorize('admin');

        //try
        //{
            $ipCategory = $this->ipCategory->findOrFail($category);

            return view('ips.create', compact('ipCategory'));
        //}
        //catch (\Exception $e)
        //{
        //    return redirect()
        //        ->back()
        //        ->withError('Could not find IP Address');
        //}
    }

    public function store($category, Request $request)
    {
        $this->authorize('admin');

        //try
        //{
            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $this->model->create([
                'category_id' => $category,
                'data'       => $data,
            ]);

            return redirect()
                ->route('ip.index', $category)
                ->withSuccess('IP Address has been added');
        //}
        //catch (\Exception $e)
        //{
        //    return redirect()
        //        ->back()
        //        ->withInput()
        //        ->withError('Error saving IP Address');
        //}
    }

    public function edit($category, $id)
    {
        $this->authorize('admin');

        //try
        //{
            $ipCategory = $this->ipCategory->findOrFail($category);
            $ip        = $this->model->findOrFail($id);

            return view('ips.edit', compact('ipCategory', 'ip'));
        //}
        //catch (\Exception $e)
        //{
        //    return redirect()
        //        ->back()
        //        ->withError('Could not find IP Address');
        //}
    }

    public function update($id, Request $request)
    {
        $this->authorize('admin');

        //try
        //{
            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $ip = $this->model->findOrFail($id);

            $ip->data = $data;

            $ip->save();

            return redirect()
                ->route('ip.index', $ip->category_id)
                ->withSuccess('IP Address has been updated');
        //}
        //catch (\Exception $e)
        //{
        //    return redirect()
        //        ->back()
        //        ->withInput()
        //        ->withError('Error updating IP Address');
        //}
    }

    public function destroy($id)
    {
        $this->authorize('admin');

        //try
        //{
            $ip = $this->model->findOrFail($id);

            $ip->delete();

            return redirect()
                ->back()
                ->withSuccess('IP Address has been deleted');
        //}
        //catch (\Exception $e)
        //{
        //    return redirect()
        //        ->back()
        //        ->withError('Could not find IP Address');
        //}
    }

    public function show($category, $id)
    {
        //try
        //{
            $ipCategory = $this->ipCategory->findOrFail($category);
            $ip = $this->model->findOrFail($id);

            return view('ips.show', compact('ip', 'ipCategory'));
        //}
        //catch (\Exception $e)
        //{
        //    return redirect()
        //        ->route('ip.index')
        //        ->withError('Could not find IP Address');
        //}
    }
}
