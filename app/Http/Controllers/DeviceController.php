<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DeviceStatus;
use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query("q", ""));
        $status = (string) $request->query("status", "");

        $devices = Device::query()
            ->visibleTo($request->user())
            ->withCount("sensors")
            ->when($search !== "", function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where("device_code", "like", "%{$search}%")
                        ->orWhere("name", "like", "%{$search}%")
                        ->orWhere("location", "like", "%{$search}%");
                });
            })
            ->when(
                in_array(
                    $status,
                    array_column(DeviceStatus::cases(), "value"),
                    true,
                ),
                fn($query) => $query->where("status", $status),
            )
            ->orderBy("device_code")
            ->paginate(15)
            ->withQueryString();

        return view("device.index", [
            "devices" => $devices,
            "search" => $search,
            "status" => $status,
            "statuses" => DeviceStatus::cases(),
            "total" => Device::query()->visibleTo($request->user())->count(),
        ]);
    }

    public function create(): View
    {
        return view("device.create", [
            "statuses" => DeviceStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Device::create($data);

        return redirect()
            ->route("devices")
            ->with(
                "status",
                "Device {$data["device_code"]} berhasil ditambahkan.",
            );
    }

    public function edit(Device $device): View
    {
        return view("device.edit", [
            "device" => $device,
            "statuses" => DeviceStatus::cases(),
        ]);
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $data = $this->validated($request, $device);

        $device->update($data);

        return redirect()
            ->route("devices")
            ->with(
                "status",
                "Device {$device->device_code} berhasil diperbarui.",
            );
    }

    public function destroy(Device $device): RedirectResponse
    {
        $code = $device->device_code;
        $device->delete();

        return redirect()
            ->route("devices")
            ->with("status", "Device {$code} berhasil dihapus.");
    }

    /**
     * Aturan validasi bersama untuk store & update.
     * Saat update, aturan unique kode mengabaikan record sendiri.
     */
    private function validated(Request $request, ?Device $device = null): array
    {
        return $request->validate(
            [
                "device_code" => [
                    "required",
                    "string",
                    "max:64",
                    'regex:/^[A-Za-z0-9_-]+$/',
                    Rule::unique("devices", "device_code")->ignore(
                        $device?->id,
                    ),
                ],
                "name" => ["required", "string", "max:255"],
                "location" => ["nullable", "string", "max:255"],
                "latitude" => ["nullable", "numeric", "between:-90,90"],
                "longitude" => ["nullable", "numeric", "between:-180,180"],
                "status" => ["required", Rule::enum(DeviceStatus::class)],
            ],
            [
                "device_code.regex" =>
                    "Kode device hanya boleh huruf, angka, tanda hubung, dan garis bawah.",
            ],
            [
                "device_code" => "kode device",
                "name" => "nama",
                "location" => "lokasi",
            ],
        );
    }
}
