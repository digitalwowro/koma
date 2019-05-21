<?php

namespace App\Http\Controllers;

use App\Device;
use App\IpCategory;
use App\IpAddress;
use App\IpField;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

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
        $this->model = $model;
        $this->ipCategory = $ipCategory;
        $this->fields = $fields;
    }

    public function index($category, Request $request)
    {
        try {
            $ipCategory = $this->ipCategory->findOrFail($category);

            $this->authorize('list', $ipCategory);

            $subnets = $this->model
                ->getSubnetsFor($category)
                ->filter(function ($subnet) use ($request) {
                    return $request->user()->can('view', $subnet);
                });

            return view('ips.index', compact('ipCategory', 'subnets'));
        } catch (Exception $e) {
            return redirect()
                ->home()
                ->withError('Could not find IP Address');
        }
    }

    public function store($category, Request $request)
    {
        try {
            $ipCategory = $this->ipCategory->findOrFail($category);

            $this->authorize('create', $ipCategory);

            $this->model->createSubnet($request->input('subnet'), $category);

            return redirect()
                ->route('ip.index', $category)
                ->withSuccess('IP Address has been added');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving IP Address');
        }
    }

    public function destroy($id)
    {
        try {
            $subnet = $this->model->findOrFail($id);

            $this->authorize('delete', $subnet);

            $this->model->where([
                'subnet' => $subnet->subnet,
                'category_id' => $subnet->category_id,
            ])->delete();

            return redirect()
                ->back()
                ->withSuccess('Subnet has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find subnet');
        }
    }

    public function subnet($subnet, Device $deviceModel)
    {
        try {
            $subnet = str_replace('-', '/', $subnet);

            $ips = $this->model->getIPsForSubnet($subnet);
            $first = $ips->first();

            if (!$first) {
                throw new Exception('No IPs found for given subnet');
            }

            $this->authorize('view', $first);

            $ipCategory = $this->ipCategory->findOrFail($first->category_id);
            $devices = $deviceModel->orderBy('section_id')->get();
            $allDevices = [];
            $ipFields = $this->fields->orderBy('sort')->get();

            foreach ($devices as $device) {
                $allDevices[$device->section->title][] = $device->title;
            }

            return view('ips.subnet', compact('subnet', 'ips', 'ipCategory', 'allDevices', 'ipFields'));
        } catch (Exception $e) {
          return redirect()
              ->home()
              ->withError($e->getMessage());
        }
    }

    public function subnetList($subnet)
    {
        $subnet = str_replace('-', '/', $subnet);
        $return = [];
        $rows = $this->model
            ->where('subnet', $subnet)
            ->whereNull('device_id')
            ->orderBy('id')
            ->get();

        $first = $rows->first();

        if (!$first) {
            app()->abort(404);
        }

        $this->authorize('view', $first);

        foreach ($rows as $row) {
            $return[] = [
                'id' => $row->id,
                'ip' => $row->ip,
                'assigned' => $row->assigned(),
            ];
        }

        return response()->json($return);
    }

}
