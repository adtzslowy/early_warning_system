<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function log(Request $request): View
    {
        $query = NotificationLog::query()
            ->with('device')
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by device
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->get('device_id'));
        }

        $notifications = $query->paginate(25);
        $devices = \App\Models\Device::all(['id', 'device_code', 'name']);
        $statuses = ['pending', 'sent', 'failed'];
        $types = ['telegram', 'email', 'sms'];

        return view('notifications.log', [
            'notifications' => $notifications,
            'devices' => $devices,
            'statuses' => $statuses,
            'types' => $types,
            'selectedStatus' => $request->get('status', ''),
            'selectedType' => $request->get('type', ''),
            'selectedDevice' => $request->get('device_id', ''),
        ]);
    }
}
