@extends('layouts.app')

@section('title', 'Monitoring')
@section('page-title', 'Monitoring')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Monitoring']
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <x-page-header
        title="Peta Monitoring"
        description="Visualisasi realtime semua titik sensor di peta interaktif"
    />

    <x-card padding="p-0">
        <div id="map" class="h-[600px] w-full rounded-lg" style="height: 600px;"></div>
    </x-card>

    {{-- Legend --}}
    <x-card title="Legenda Status" subtitle="Warna marker menunjukkan status risiko">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded-full" style="background-color: #22c55e;"></div>
                <span class="text-sm">Aman</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded-full" style="background-color: #eab308;"></div>
                <span class="text-sm">Waspada</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded-full" style="background-color: #f97316;"></div>
                <span class="text-sm">Siaga</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded-full" style="background-color: #ef4444;"></div>
                <span class="text-sm">Bahaya</span>
            </div>
        </div>
    </x-card>
</div>

{{-- Leaflet CSS & JS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<script>
    const devices = @json($devices);

    const map = L.map('map').setView([-6.2, 107], 10);

    // Basemap layers
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
        name: 'OpenStreetMap',
    });

    const satelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            attribution: 'Tiles © Esri',
            maxZoom: 19,
            name: 'Satelit',
        }
    );

    // Set default layer
    osmLayer.addTo(map);

    // Topographic layer
    const topoLayer = L.tileLayer(
        'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
        {
            attribution: '© OpenTopoMap',
            maxZoom: 17,
            name: 'Topografi',
        }
    );

    // Layer control
    const baseLayers = {
        'OpenStreetMap': osmLayer,
        'Satelit': satelliteLayer,
        'Topografi': topoLayer,
    };

    L.control.layers(baseLayers, null, {
        collapsed: false,
        position: 'topright',
    }).addTo(map);

    const riskColors = {
        'aman': '#22c55e',
        'waspada': '#eab308',
        'siaga': '#f97316',
        'bahaya': '#ef4444',
    };

    const validDevices = [];

    devices.forEach(device => {
        if (!device.latitude || !device.longitude) return;

        validDevices.push(device);

        const color = riskColors[device.risk] || '#666';

        const marker = L.circleMarker([device.latitude, device.longitude], {
            radius: 10,
            fillColor: color,
            color: color,
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8,
        }).addTo(map);

        const waterLevelText = device.water_level ? device.water_level.toFixed(1) + ' cm' : '—';
        const riskScoreText = Math.round(device.risk_score);

        const popupContent = `
            <div class="min-w-48">
                <h3 class="font-bold text-sm">${device.code}</h3>
                <p class="text-xs text-gray-600 mb-2">${device.name}</p>
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between">
                        <span>Status:</span>
                        <span class="font-mono">${device.status}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ketinggian Air:</span>
                        <span class="font-mono">${waterLevelText}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Risiko:</span>
                        <span class="font-mono uppercase" style="color: ${color}; font-weight: bold;">${device.risk}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Score:</span>
                        <span class="font-mono">${riskScoreText}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Evaluasi:</span>
                        <span class="font-mono">${device.evaluated_at || '—'}</span>
                    </div>
                    <a href="/dashboard?device=${device.code}" class="mt-2 inline-block text-blue-600 hover:underline text-xs">
                        Lihat detail →
                    </a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);

        marker.on('click', function() {
            map.setView([device.latitude, device.longitude], 14);
        });
    });

    // Auto-navigate to device location(s)
    if (validDevices.length === 1) {
        const device = validDevices[0];
        map.setView([device.latitude, device.longitude], 14);
    } else if (validDevices.length > 1) {
        const bounds = L.latLngBounds(validDevices.map(d => [d.latitude, d.longitude]));
        map.fitBounds(bounds, { padding: [50, 50] });
    }
</script>

<style>
    .leaflet-popup-content {
        font-family: inherit;
    }

    .leaflet-popup-content h3 {
        margin: 0;
    }

    .leaflet-popup-content p {
        margin: 0;
    }
</style>
@endsection
