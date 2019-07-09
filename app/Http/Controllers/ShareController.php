<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IpAddress;
use App\IpCategory;
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

            $permissions = $request->input('permissions');

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not share device',
            ]);
        }
    }
}
