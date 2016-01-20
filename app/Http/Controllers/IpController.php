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
        try
        {
            $subnets = $this->model->getSubnetsFor($category);
            $ipCategory = $this->ipCategory->findOrFail($category);

            return view('ips.index', compact('ipCategory', 'subnets'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find IP Address');
        }
    }

    public function store($category, Request $request)
    {
        $this->authorize('admin');

        try
        {
            $this->model->createSubnet($request->input('subnet'), $category);

            return redirect()
                ->route('ip.index', $category)
                ->withSuccess('IP Address has been added');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving IP Address');
        }
    }

    public function destroy($id)
    {
        $this->authorize('admin');

        try
        {
            $ip = $this->model->findOrFail($id);

            $ip->delete();

            return redirect()
                ->back()
                ->withSuccess('IP Address has been deleted');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find IP Address');
        }
    }

    public function show($category, $id)
    {
        try
        {
            $ipCategory = $this->ipCategory->findOrFail($category);
            $ip = $this->model->findOrFail($id);

            return view('ips.show', compact('ip', 'ipCategory'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->route('ip.index')
                ->withError('Could not find IP Address');
        }
    }

    public function subnet($subnet)
    {
        try
        {
            $subnet = str_replace('-', '/', $subnet);

            $ips   = $this->model->getIPsForSubnet($subnet);
            $first = $ips->first();

            if ( ! $first)
            {
                throw new \Exception('No IPs found for given subnet');
            }

            $ipCategory = $this->ipCategory->findOrFail($first->category_id);

            return view('ips.subnet', compact('subnet', 'ips', 'ipCategory'));
        }
        catch (\Exception $e)
        {
          return redirect()
              ->back()
              ->withError($e->getMessage());
        }
    }

    public function assign($id, Request $request)
    {
        try
        {
            $deviceId = $request->input('device_id');

            $ip = $this->model->findOrFail($id);

            $ip->device_id = $deviceId;

            $ip->save();

            return redirect()
                ->back()
                ->withSuccess('IP has been assigned');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Error assigning IP');
        }
    }
}
