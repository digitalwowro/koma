<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IpAddress;
use App\IpCategory;
use App\User;
use Exception;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    private function getResource(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        switch ($type) {
            case 'device':
                return Device::findOrFail($id);
            case 'section':
                return DeviceSection::findOrFail($id);
            case 'subnet':
                return IpAddress::findOrFail($id);
            case 'category':
                return IpCategory::findOrFail($id);
        }

        throw new Exception('Invalid resource type');
    }

    public function with(Request $request)
    {
        $resource = $this->getResource($request);

        $this->authorize('share', $resource);

        $data = $resource
            ->sharedWith()
            ->map(function($permission) {
                return [
                    'id' => $permission['user']['id'] ?? '',
                    'name' => $permission['user']['name'] ?? '',
                    'email' => $permission['user']['email'] ?? '',
                    'permissions' => $permission['grant_type'] ?? [],
                    'avatar' => gravatar($permission['user']['email'] ?? '', 40),
                ];
            });

        return response()->json($data);
    }

    /**
     * Permissions diff
     *
     * @param array $old
     * @param array $new
     * @return array
     */
    private function diff(array $old, array $new): array
    {
        $toDelete = [];
        $toRefresh = [];
        $toAdd = [];

        foreach ($old as $key => $value) {
            if (!isset($new[$key])) {
                $toDelete[] = $key;
            } else {
                $a = $new[$key];
                $b = $old[$key];

                sort($a);
                sort($b);

                if ($a !== $b) {
                    $toRefresh[] = $key;
                }
            }
        }

        foreach($new as $key => $value) {
            if (!isset($old[$key])) {
                $toAdd[] = $key;
            }
        }

        return [$toDelete, $toRefresh, $toAdd];
    }

    /**
     * Save share settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(Request $request)
    {
        try {
            $resource = $this->getResource($request);

            $this->authorize('share', $resource);

            $old = [];
            $new = [];

            $resource->sharedWith()->each(function ($permission) use (&$old) {
                $old[$permission['user_id']] = $permission['grant_type'];
            });

            foreach ($request->input('permissions') as $permission) {
                $sanitized = $permission['permissions'];
                $sanitized = array_map('intval', $sanitized);
                $new[$permission['id']] = $sanitized;
            }

            list($toDelete, $toRefresh, $toAdd) = $this->diff($old, $new);

            $userIds = array_unique(array_merge($toDelete, $toRefresh, $toAdd));
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');
            $sharer = app('share');

            foreach ($toDelete as $key) {
                if (isset($users[$key])) {
                    $sharer->share($users[$key], $resource);
                }
            }

            foreach ($toRefresh as $key) {
                if (isset($users[$key])) {
                    $sharer->share($users[$key], $resource, $new[$key]);
                }
            }

            foreach ($toAdd as $key) {
                if (isset($users[$key])) {
                    $sharer->share($users[$key], $resource, $new[$key]);
                }
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not share device',
            ]);
        }
    }
}
