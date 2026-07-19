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

        // Filter by device
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->get('device_id'));
        }

        $notifications = $query->paginate(25);
        $devices = \App\Models\Device::query()
            ->visibleTo($request->user())
            ->orderBy('device_code')
            ->get(['id', 'device_code', 'name']);
        $statuses = ['pending', 'sent', 'failed'];

        return view('notifications.log', [
            'notifications' => $notifications,
            'devices' => $devices,
            'statuses' => $statuses,
            'selectedStatus' => $request->get('status', ''),
            'selectedDevice' => $request->get('device_id', ''),
        ]);
    }
}
