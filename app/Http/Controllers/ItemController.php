<?php

namespace App\Http\Controllers;

use App\Item;
use App\Category;
use App\EncryptedStore;
use App\Http\Controllers\Controller;
use App\IpSubnet;
use App\Permission;
use Exception;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index($categoryId)
    {
        //try {
            $category = Category::findOrFail($categoryId);
            $items = $category->items;
            $colspan = 1;

            try {
                $filters = json_decode(request()->cookie('device-filters'), true);
            } catch (Exception $e) {
                $filters = [];
            }

            if (is_array($category->fields)) {
                foreach ($category->fields as $field) {
                    if ($field->getType() === 'Status') {
                        $id = $field->getKey();
                        $preselected = $field->getOption('preselected');

                        if ($preselected && !isset($filters[$id])) {
                            $filters[$id] = explode(',', $preselected);
                        }
                    }
                    if ($field->showInDeviceList()) {
                        $colspan++;
                    }
                }
            }

            return view('item.index', compact('category', 'items', 'colspan', 'filters'));
        //} catch (Exception $e) {
        //    return redirect()
        //        ->back()
        //        ->withError('Could not find category');
        //}
    }

    public function create($type)
    {
        try {
            $category = Category::findOrFail($type);

            $this->authorize('create', $category);

            return view('item.create', compact('category'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find category');
        }
    }

    private function setCategory(Item $device, Request $request, $save = true)
    {
        $category = $request->input('category_id');

        if (empty($category)) {
            $device->category_id = null;

            if ($save) {
                $device->save();
            }

            return;
        }

        $validIds = collect($device->section->categories)->pluck('id');

        if (!$validIds->contains($category)) {
            throw new Exception('Invalid device category');
        }

        $device->category_id = $category;

        if ($save) {
            $device->save();
        }
    }

    public function store($categoryId, Request $request)
    {
        try {
            $section = Category::findOrFail($categoryId);

            $this->authorize('create', $section);

            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $device = Item::create([
                'category_id' => $categoryId,
                'created_by' => $request->user()->id,
            ]);

            EncryptedStore::upsert($device, $data);

            $this->setCategory($device, $request);

            $ips = (array) $request->input('ips');
            IpSubnet::assignIps($device->id, $ips, $request->user());

            if ($request->user()->cannot('update', $section)) {
                // if user has permission to create entries but not edit entries, he will no longer be
                // able to access his device, so we'll assign rwd permission for the newly created device

                $request->user()->permissions()->create([
                    'resource_type' => Permission::RESOURCE_TYPE_ITEM,
                    'resource_id' => $device->id,
                    'grant_type' => [
                        Permission::GRANT_TYPE_READ,
                        Permission::GRANT_TYPE_UPDATE,
                        Permission::GRANT_TYPE_DELETE,
                    ],
                ]);
            }

            $params = $device->category_id
                ? ['category_id' => $categoryId]
                : $categoryId;

            return redirect()
                ->route('item.index', $params)
                ->withSuccess('Device has been added');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving device');
        }
    }

    public function edit($id)
    {
        try {
            $device = Item::findOrFail($id);
            $category = $device->section;

            $this->authorize('update', $device);

            return view('item.edit', compact('category', 'device'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device');
        }
    }

    public function update($id, Request $request)
    {
        try {
            $device = Item::findOrFail($id);

            $this->authorize('update', $device);

            $data = $request->input();
            if (isset($data['ips']) && is_array($data['ips'])) {
                $data['ips'] = array_filter($data['ips'], function ($ip) {
                    return filter_var($ip, FILTER_VALIDATE_IP);
                });
            }

            unset($data['_token']);
            unset($data['_method']);
            EncryptedStore::upsert($device, $data);

            $this->setCategory($device, $request);

            $ips = (array) $request->input('ips');
            IpSubnet::assignIps($device->id, $ips, $request->user());

            $params = $device->category_id
                ? ['type' => $device->section_id, 'category' => $device->category_id]
                : $device->section_id;

            return redirect()
                ->route('item.index', $params)
                ->withSuccess('Device has been updated');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error updating device: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $device = Item::findOrFail($id);

            $this->authorize('delete', $device);

            EncryptedStore::destroy($device);

            $device->delete();

            IpSubnet::deviceDestroyed($id);

            return redirect()
                ->back()
                ->withSuccess('Device has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Error deleting device: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $device = Item::findOrFail($id);
            $category = $device->section;

            $this->authorize('view', $device);

            return view('item.show', compact('device', 'category'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device');
        }
    }
}
