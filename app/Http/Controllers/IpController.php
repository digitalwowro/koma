<?php

namespace App\Http\Controllers;

use App\Device;
use App\EncryptedStore;
use App\Exceptions\SubnetTooLargeException;
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

            $subnets = $this->model
                ->getSubnetsFor($category)
                ->filter(function ($subnet) use ($request) {
                    return $request->user()->can('view', $subnet);
                });

            return view('ip.index', compact('ipCategory', 'subnets'));
        } catch (Exception $e) {
            return redirect()
                ->home()
                ->withError('Could not find IP Address');
        }
    }

    public function create($category)
    {
        return view('ip.create', compact('category'));
    }

    public function store($category, Request $request)
    {
        try {
            $ipCategory = $this->ipCategory->findOrFail($category);

            $this->authorize('create', $ipCategory);

            $first = $this->model->createSubnet($request->input('subnet'), $category, $request->user()->id);

            EncryptedStore::upsert($first, [
                'name' => $request->input('name') ?: null,
            ]);

            return redirect()
                ->route('ip.index', $category)
                ->withSuccess('IP Address has been added');
        } catch (SubnetTooLargeException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Subnet too large');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving IP Address');
        }
    }

    public function edit($category, $id)
    {
        try {
            $subnet = IpAddress::where('category_id', $category)
                ->where('id', $id)
                ->firstOrFail();

            $first = $subnet->firstInSubnet();

            $data = $first->data;
            $name = $data['name'] ?? '';

            $allInSubnet = IpAddress::where('subnet', $first->subnet)
                ->where('category_id', $category)
                ->pluck('ip', 'id')
                ->toArray();

            $allReserved = IpAddress::where('subnet', $first->subnet)
                ->where('category_id', $category)
                ->where('is_reserved', true)
                ->whereNull('device_id')
                ->pluck('id')
                ->toArray();

            return view('ip.edit', [
                'ip' => $first,
                'category' => $category,
                'allInSubnet' => $allInSubnet,
                'allReserved' => $allReserved,
                'name' => $name,
            ]);
        } catch (Exception $e) {
            return redirect()
                ->route('ip.index', $category)
                ->withError('Invalid IP subnet');
        }
    }

    public function update($id, Request $request)
    {
        try {
            $ip = IpAddress::findOrFail($id);

            $first = $ip->firstInSubnet();

            EncryptedStore::upsert($first, [
                'name' => $request->input('name') ?: null,
            ]);

            $reserved = $request->input('reserved');

            if (is_array($reserved)) {
                IpAddress::where('subnet', $first->subnet)
                    ->where('category_id', $first->category_id)
                    ->whereIn('id', $request->input('reserved'))
                    ->update(['is_reserved' => true]);

                IpAddress::where('subnet', $first->subnet)
                    ->where('category_id', $first->category_id)
                    ->whereNotIn('id', $request->input('reserved'))
                    ->update(['is_reserved' => false]);
            }

            return redirect()
                ->route('ip.index', $first->category_id)
                ->withSuccess('Subnet has been saved');
        } catch (Exception $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withError('Error saving IP Subnet');
        }
    }

    public function destroy($id)
    {
        try {
            $subnet = $this->model->findOrFail($id);

            $this->authorize('delete', $subnet);

            EncryptedStore::destroy($subnet);

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

            return view('ip.subnet', compact('subnet', 'ips', 'ipCategory', 'allDevices', 'ipFields'));
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
            ->where('is_reserved', false)
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
