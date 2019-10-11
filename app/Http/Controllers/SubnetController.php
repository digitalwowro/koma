<?php

namespace App\Http\Controllers;

use App\EncryptedStore;
use App\Exceptions\InvalidSubnetException;
use App\IpCategory;
use App\IpSubnet;
use App\IpField;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class SubnetController extends Controller
{
    const IPS_PER_PAGE = 50;

    public function index($category, Request $request)
    {
        try {
            app('encrypt')->disableExceptions();

            $ipCategory = IpCategory::findOrFail($category);

            $subnets = IpSubnet::where('category_id', $category)->get();

            return view('subnet.index', compact('ipCategory', 'subnets'));
        } catch (Exception $e) {
            return redirect()
                ->home()
                ->withError('Could not find IP Subnet');
        }
    }

    public function create($category)
    {
        return view('subnet.create', compact('category'));
    }

    public function store($category, Request $request)
    {
        try {
            $ipCategory = IpCategory::findOrFail($category);

            $this->authorize('create', $ipCategory);

            IpSubnet::createSubnet($request->input(), $category, $request->user()->id);

            return redirect()
                ->route('subnet.index', $category)
                ->withSuccess('Subnet has been added');
        } catch (InvalidSubnetException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Invalid subnet specification');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving subnet');
        }
    }

    public function edit($category, $id)
    {
        try {
            $subnet = IpSubnet::where('category_id', $category)
                ->where('id', $id)
                ->firstOrFail();

            $data = $subnet->data;

            list ($allInSubnet, $allReserved) = $subnet->getReserved();

            return view('subnet.edit', compact('subnet', 'category', 'allInSubnet', 'allReserved'));
        } catch (Exception $e) {
            return redirect()
                ->route('subnet.index', $category)
                ->withError('Invalid IP subnet');
        }
    }

    /**
     * @param IpSubnet $subnet
     * @param array    $ips
     * @param array    $data
     * @return array
     */
    private function markReserved(IpSubnet $subnet, array $ips, array $data) : array
    {
        $assignData = $data['assigned'] ?? [];

        foreach ($ips as $ip) {
            if ($subnet->ipBelongsToSubnet($ip)) {
                if (empty($assignData[$ip]['device_id'])) {
                    $assignData[$ip] = ['reserved' => true];
                }
            }
        }

        $data['assigned'] = $assignData;

        return $data;
    }

    public function update($id, Request $request)
    {
        try {
            $subnet = IpSubnet::findOrFail($id);
            $data = (array) ($subnet->data ?? []);
            $data['name'] = $request->input('name') ?: null;
            $data['notes'] = $request->input('notes') ?: null;

            $reserved = (array) $request->input('reserved');

            $data = $this->markReserved($subnet, $reserved, $data);
            EncryptedStore::upsert($subnet, $data);

            return redirect()
                ->route('subnet.index', $subnet->category_id)
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
            $subnet = IpSubnet::findOrFail($id);

            $this->authorize('delete', $subnet);

            $subnet->delete();

            EncryptedStore::destroy($subnet);

            return redirect()
                ->back()
                ->withSuccess('Subnet has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find subnet');
        }
    }

    public function subnet($id, Request $request)
    {
        try {
            $subnet = IpSubnet::findOrFail($id);

            $this->authorize('view', $subnet);

            $ipCategory = IpCategory::findOrFail($subnet->category_id);
            $ipFields = IpField::orderBy('sort')->get();
            $page = (int) $request->input('page', 1);
            $ips = $subnet->paginatedIps($page, $this::IPS_PER_PAGE);

            return view('subnet.subnet', compact('subnet', 'ips', 'ipCategory', 'ipFields'));
        } catch (Exception $e) {
            return redirect()
                ->home()
                ->withError($e->getMessage());
        }
    }

    public function subnetList($id)
    {
        $subnet = IpSubnet::findOrFail($id);

        $this->authorize('view', $subnet);

        return response()->json($subnet->freeIps());
    }

    public function unassign($id, Request $request)
    {
        try {
            $subnet = IpSubnet::findOrFail($id);

            $this->authorize('edit', $subnet);

            $ip = $request->input('ip');
            $data = $subnet->data;
            $assigned = $data['assigned'] ?? [];

            if ($ip && isset($assigned[$ip])) {
                unset($assigned[$ip]);
                $data['assigned'] = $assigned;

                EncryptedStore::upsert($subnet, $data);
            }

            return redirect()
                ->route('subnet.subnet', $id)
                ->withSuccess('IP address was unassigned');
        } catch (Exception $e) {
            return redirect()
                ->route('subnet.subnet', $id)
                ->withError('Error unassigning IP address');
        }
    }
}
