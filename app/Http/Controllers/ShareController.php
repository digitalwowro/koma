<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\Group;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IpAddress;
use App\IpCategory;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                if ($user = $permission->user) {
                    return [
                        'user_id' => $user->id ?? '',
                        'name' => $user->name ?? '',
                        'email' => $user->email ?? '',
                        'permissions' => $permission['grant_type'] ?? [],
                        'avatar' => gravatar($user->email ?? '', 40, 'retro'),
                    ];
                } elseif ($group = $permission->group) {
                    $memberCount = $group->users()->count();

                    return [
                        'group_id' => $group->id ?? '',
                        'name' => $group->name ?? '',
                        'email' => $memberCount . ' group '. Str::plural('member', $memberCount),
                        'permissions' => $permission['grant_type'] ?? [],
                        'avatar' => gravatar("group_id_{$group->id}", 40, 'retro'),
                    ];
                }
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
                if ($permission['user_id']) {
                    $old["u_{$permission['user_id']}"] = $permission['grant_type'];
                } elseif ($permission['group_id']) {
                    $old["g_{$permission['group_id']}"] = $permission['grant_type'];
                }
            });

            foreach ((array) $request->input('permissions', []) as $permission) {
                $sanitized = $permission['permissions'];
                $sanitized = array_map('intval', $sanitized);

                if (!empty($permission['user_id'])) {
                    $new["u_{$permission['user_id']}"] = $sanitized;
                } elseif (!empty($permission['group_id'])) {
                    $new["g_{$permission['group_id']}"] = $sanitized;
                }
            }

            list($toDelete, $toRefresh, $toAdd) = $this->diff($old, $new);

            $allIds = array_unique(array_merge($toDelete, $toRefresh, $toAdd));

            $userIds = array_map(function ($item) {
                return substr($item, 2);
            }, array_filter($allIds, function ($item) {
                return substr($item, 0, 2) === 'u_';
            }));

            $groupIds = array_map(function ($item) {
                return substr($item, 2);
            }, array_filter($allIds, function ($item) {
                return substr($item, 0, 2) === 'g_';
            }));

            $users = User::whereIn('id', $userIds)->get()->keyBy('id');
            $groups = Group::whereIn('id', $groupIds)->get()->keyBy('id');

            $sharer = app('share');

            foreach ($toDelete as $key) {
                $id = intval(substr($key, 2));

                if ($key{0} === 'u' && isset($users[$id])) {
                    $sharer->share($users[$id], $resource);
                } elseif ($key{0} === 'g' && isset($groups[$id])) {
                    $sharer->share($groups[$id], $resource);
                }
            }

            foreach ($toRefresh as $key) {
                $id = intval(substr($key, 2));

                if ($key{0} === 'u' && isset($users[$id])) {
                    $sharer->share($users[$id], $resource, $new[$key]);
                } elseif ($key{0} === 'g' && isset($groups[$id])) {
                    $sharer->share($groups[$id], $resource, $new[$key]);
                }
            }

            foreach ($toAdd as $key) {
                $id = intval(substr($key, 2));

                if ($key{0} === 'u' && isset($users[$id])) {
                    $sharer->share($users[$id], $resource, $new[$key]);
                } elseif ($key{0} === 'g' && isset($groups[$id])) {
                    $sharer->share($groups[$id], $resource, $new[$key]);
                }
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not share resource',
            ]);
        }
    }
}
