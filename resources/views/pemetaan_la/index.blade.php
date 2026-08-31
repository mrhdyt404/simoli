@extends('layouts.simoli')

@section('title', 'Pemetaan Spasial GIS Land Application')
@section('page-title', 'Pemetaan Spasial GIS Land Application')
@section('page-description', 'Peta Interaktif Spasial Blok Land Application, Titik Penaatan IPAL & Sumur Pantau Tiap PKS')

@section('breadcrumb')
    <li>Monitoring</li>
    <li class="separator">/</li>
    <li>Pemetaan LA</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('perizinan-la.index') }}" class="btn-ptpn btn-ptpn-outline">
            <i class="feather-shield" style="font-size:15px;"></i>
            <span>Regulasi & Izin SK</span>
        </a>
        @if($pksAktif)
        <a href="{{ route('pemetaan-la.peta-digital', $pksAktif->id_pks) }}" target="_blank" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-printer" style="font-size:15px;"></i>
            <span>Layout Peta Resmi GIS</span>
        </a>
        @endif
    </div>
@endsection

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Full Screen Map Container */
    .gis-wrapper {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        border: 1.5px solid rgba(22, 163, 74, 0.2);
        box-shadow: 0 8px 32px rgba(5, 46, 22, 0.12);
        height: calc(100vh - 220px);
        min-height: 560px;
        display: flex;
    }

    #simoli-gis-map {
        width: 100%;
        height: 100%;
        z-index: 10;
        background: #e2e8f0;
    }

    /* Floating Side Control Panel */
    .gis-sidebar-panel {
        position: absolute;
        top: 14px;
        left: 14px;
        bottom: 14px;
        width: 340px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 14px;
        border: 1px solid rgba(22, 163, 74, 0.25);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .gis-sidebar-panel.collapsed {
        transform: translateX(-360px);
    }

    .gis-panel-header {
        padding: 14px 16px;
        background: linear-gradient(135deg, #052e16 0%, #166534 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gis-panel-body {
        padding: 14px 16px;
        overflow-y: auto;
        flex-grow: 1;
    }

    .gis-toggle-btn {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 999;
        background: #166534;
        color: #fff;
        border: none;
        border-radius: 10px;
        width: 38px;
        height: 38px;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        cursor: pointer;
    }

    .gis-sidebar-panel.collapsed + .gis-toggle-btn {
        display: flex;
    }

    /* Legend Indicator in Map */
    .gis-legend-overlay {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(22, 163, 74, 0.25);
        border-radius: 12px;
        padding: 12px 16px;
        z-index: 1000;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        font-size: 11.5px;
        max-width: 260px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
    }
    .legend-item:last-child { margin-bottom: 0; }

    .legend-dot {
        width: 14px; height: 14px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    /* Custom Leaflet Popups */
    .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        border: 1px solid rgba(22, 163, 74, 0.3);
        padding: 0;
        overflow: hidden;
    }
    .leaflet-popup-content {
        margin: 0;
        line-height: 1.4;
    }
    .popup-header {
        background: linear-gradient(135deg, #052e16 0%, #15803d 100%);
        color: #fff;
        padding: 10px 14px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
    }
    .popup-body {
        padding: 12px 14px;
        font-size: 12px;
    }

    /* Pulsing Pin for IPAL Outlet */
    .pulse-marker {
        display: block;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #dc2626;
        border: 3px solid #ffffff;
        box-shadow: 0 0 0 rgba(220, 38, 38, 0.6);
        animation: pulsePin 1.8s infinite;
    }
    @keyframes pulsePin {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        70% { box-shadow: 0 0 0 14px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }

    .well-marker {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px; height: 24px;
        border-radius: 50%;
        background: #2563eb;
        color: #ffffff;
        border: 2px solid #ffffff;
        font-size: 11px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
</style>
@endsection

@section('content')

{{-- TOP BAR: PKS SELECTOR & QUICK STATS --}}
<div class="row g-3 mb-3">
    <div class="col-lg-4">
        <form method="GET" action="{{ route('pemetaan-la.index') }}" id="pksForm">
            <div class="input-group">
                <span class="input-group-text bg-success text-white border-success fw-bold font-12">
                    <i class="feather-map-pin me-1"></i> Pilih PKS
                </span>
                <select name="id_pks" class="form-select border-success" onchange="document.getElementById('pksForm').submit()" {{ !Auth::user()->isAdmin() ? 'disabled' : '' }}>
                    @foreach($daftarPks as $pks)
                    <option value="{{ $pks->id_pks }}" {{ $selectedPksId == $pks->id_pks ? 'selected' : '' }}>
                        {{ $pks->nama }} ({{ $pks->kode }})
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="col-lg-8">
        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
            <div class="badge bg-white text-dark border p-2 font-12 shadow-sm d-flex align-items-center gap-2">
                <i class="feather-grid text-success fs-6"></i>
                <span><strong>{{ $blokList->count() }}</strong> Blok Terpetakan</span>
            </div>
            <div class="badge bg-white text-dark border p-2 font-12 shadow-sm d-flex align-items-center gap-2">
                <i class="feather-layers text-primary fs-6"></i>
                <span><strong>{{ number_format($totalLuasHa, 2, ',', '.') }}</strong> Ha Terizin</span>
            </div>
            <div class="badge bg-white text-dark border p-2 font-12 shadow-sm d-flex align-items-center gap-2">
                <i class="feather-radio text-danger fs-6"></i>
                <span>Titik IPAL: <strong>{{ $perizinan->nama_titik_penaatan ?? 'Kolam Anaerob 4' }}</strong></span>
            </div>
            <div class="badge bg-white text-dark border p-2 font-12 shadow-sm d-flex align-items-center gap-2">
                <i class="feather-droplet text-info fs-6"></i>
                <span><strong>{{ $totalSumurPantau }}</strong> Sumur Pantau</span>
            </div>
        </div>
    </div>
</div>

{{-- GIS MAP CONTAINER --}}
<div class="gis-wrapper">
    
    {{-- Floating Info & Control Sidebar --}}
    <div class="gis-sidebar-panel" id="gisPanel">
        <div class="gis-panel-header">
            <div>
                <h6 class="mb-0 fw-bold font-14">
                    <i class="feather-map me-1 text-success"></i> GIS Land Application
                </h6>
                <small class="text-white-50 font-11">PKS {{ $pksAktif->nama ?? 'Unit' }}</small>
            </div>
            <button type="button" class="btn btn-sm btn-link text-white p-0" onclick="toggleGisSidebar()" title="Sembunyikan Panel">
                <i class="feather-chevrons-left fs-5"></i>
            </button>
        </div>

        <div class="gis-panel-body">
            
            {{-- Legalitas SK Box --}}
            <div class="p-2 bg-light rounded-3 border mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted font-11">Nomor SK DLH:</span>
                    <span class="badge bg-success font-10">{{ $perizinan->status_label ?? 'Aktif' }}</span>
                </div>
                <div class="fw-bold text-dark font-12 text-truncate" title="{{ $perizinan->nomor_sk ?? 'N/A' }}">
                    {{ $perizinan->nomor_sk ?? 'Belum ada SK terdaftar' }}
                </div>
                <div class="d-flex justify-content-between font-11 text-muted mt-1">
                    <span>Debit Maks: <strong>{{ $perizinan ? number_format($perizinan->debit_maksimal_harian, 0) : 0 }} m³/hr</strong></span>
                    <span>BOD: <strong>{{ $perizinan ? number_format($perizinan->bod_maksimal, 0) : 0 }} mg/L</strong></span>
                </div>
            </div>

            {{-- Layer Visibility Controls --}}
            <h6 class="fw-bold font-12 text-secondary text-uppercase mb-2">Layer Peta:</h6>
            <div class="mb-3">
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" id="layerIpal" checked onchange="toggleLayer('ipal')">
                    <label class="form-check-label font-12" for="layerIpal">Titik Penaatan IPAL (Outlet)</label>
                </div>
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" id="layerSumur" checked onchange="toggleLayer('sumur')">
                    <label class="form-check-label font-12" for="layerSumur">Titik Sumur Pantau Air Tanah</label>
                </div>
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" id="layerBlok" checked onchange="toggleLayer('blok')">
                    <label class="form-check-label font-12" for="layerBlok">Blok-Blok Land Application</label>
                </div>
            </div>

            {{-- Daftar Blok List with Quick Zoom --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold font-12 text-secondary text-uppercase mb-0">Daftar Blok ({{ $blokList->count() }}):</h6>
                <span class="badge bg-light text-dark border font-10">Klik untuk Zoom</span>
            </div>

            <div class="list-group list-group-flush font-12 border rounded-3 overflow-auto" style="max-height: 240px;">
                @forelse($blokList as $b)
                @php $st = $blokStatus[$b->id] ?? null; @endphp
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3" 
                        onclick="zoomToBlok({{ $b->latitude_center ?? 0 }}, {{ $b->longitude_center ?? 0 }}, '{{ $b->nama_blok }}')">
                    <div>
                        <strong class="text-success">Blok {{ $b->nama_blok }}</strong>
                        <small class="text-muted d-block font-11">{{ $b->afdeling }} &bull; {{ $b->luas_ha }} Ha &bull; {{ $b->jumlah_flat_bed }} Bed</small>
                    </div>
                    <div>
                        @if($st && $st['is_active_7d'])
                        <span class="badge bg-success font-10" title="Aktif dialiri limbah minggu ini">Aktif Dialiri</span>
                        @else
                        <span class="badge bg-light text-secondary border font-10">Standby</span>
                        @endif
                    </div>
                </button>
                @empty
                <div class="p-3 text-center text-muted font-11">Belum ada blok terpetakan.</div>
                @endforelse
            </div>

            <div class="mt-3">
                <a href="{{ route('pemetaan-la.peta-digital', $selectedPksId) }}" target="_blank" class="btn btn-sm btn-outline-success w-100 font-12">
                    <i class="feather-external-link me-1"></i> Buka Layout Kartografi Cetak
                </a>
            </div>

        </div>
    </div>

    {{-- Re-open Button when panel is collapsed --}}
    <button type="button" class="gis-toggle-btn" onclick="toggleGisSidebar()" title="Buka Panel Kontrol">
        <i class="feather-layers"></i>
    </button>

    {{-- Leaflet Map Canvas --}}
    <div id="simoli-gis-map"></div>

    {{-- Legend Overlay Bottom-Right --}}
    <div class="gis-legend-overlay">
        <div class="fw-bold font-12 mb-2 text-dark border-bottom pb-1">Legenda Spasial LA</div>
        <div class="legend-item">
            <span class="pulse-marker" style="width:14px;height:14px;"></span>
            <span>Titik Penaatan IPAL (Outlet)</span>
        </div>
        <div class="legend-item">
            <span class="well-marker" style="width:14px;height:14px;font-size:8px;"><i class="feather-droplet"></i></span>
            <span>Sumur Pantau Air Tanah</span>
        </div>
        <div class="legend-item">
            <span class="legend-dot" style="background:#16a34a;border:1px solid #14532d;"></span>
            <span>Blok Aktif Dialiri (&le; 7 hari)</span>
        </div>
        <div class="legend-item">
            <span class="legend-dot" style="background:#f59e0b;border:1px solid #b45309;"></span>
            <span>Blok Standby / Rotasi</span>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map;
    let layerGroupIpal = L.layerGroup();
    let layerGroupSumur = L.layerGroup();
    let layerGroupBlok = L.layerGroup();

    // Data PKS dari Backend
    const pksData = {
        id: {{ $pksAktif->id_pks ?? 1 }},
        nama: "{{ $pksAktif->nama ?? 'PKS' }}",
        kode: "{{ $pksAktif->kode ?? 'PKS' }}",
        latTitikPenaatan: {{ $perizinan->lat_titik_penaatan ?? 1.742525 }},
        longTitikPenaatan: {{ $perizinan->long_titik_penaatan ?? 100.510011 }},
        namaTitikPenaatan: "{{ $perizinan->nama_titik_penaatan ?? 'IPAL Kolam Anaerob 4' }}",
        koordinatText: "{{ $perizinan->koordinat_penaatan_text ?? '' }}",
        debitMax: "{{ $perizinan->debit_maksimal_harian ?? 400 }}",
        bodMax: "{{ $perizinan->bod_maksimal ?? 5000 }}",
        sumurPantau: [
            @if($perizinan && $perizinan->sumurPantau)
            @foreach($perizinan->sumurPantau as $sp)
            {
                nama: "{{ $sp->nama_sumur }}",
                jenis: "{{ $sp->jenis_sumur }}",
                blok: "{{ $sp->lokasi_blok ?? '-' }}",
                lat: {{ $sp->latitude ?? 0 }},
                lng: {{ $sp->longitude ?? 0 }},
                koordinatText: "{{ $sp->koordinat_text ?? '' }}",
                frekuensi: "{{ $sp->frekuensi_pantau }}"
            },
            @endforeach
            @endif
        ],
        bloks: [
            @foreach($blokList as $b)
            @php $st = $blokStatus[$b->id] ?? null; @endphp
            {
                id: {{ $b->id }},
                nama: "{{ $b->nama_blok }}",
                afdeling: "{{ $b->afdeling }}",
                luas: "{{ $b->luas_ha }}",
                flatbed: "{{ $b->jumlah_flat_bed }}",
                parit: "{{ $b->panjang_parit_meter }}",
                lat: {{ $b->latitude_center ?? 0 }},
                lng: {{ $b->longitude_center ?? 0 }},
                isActive7d: {{ ($st && $st['is_active_7d']) ? 'true' : 'false' }},
                lastFlowDate: "{{ $st['last_date'] ?? '-' }}",
                volLimbah: "{{ $st['vol_limbah'] ?? 0 }}"
            },
            @endforeach
        ]
    };

    document.addEventListener('DOMContentLoaded', function() {
        initLeafletMap();
    });

    function initLeafletMap() {
        // Center koordinat awal: titik penaatan IPAL PKS
        const defaultLat = pksData.latTitikPenaatan || 1.742525;
        const defaultLng = pksData.longTitikPenaatan || 100.510011;

        map = L.map('simoli-gis-map', {
            center: [defaultLat, defaultLng],
            zoom: 14,
            zoomControl: false
        });

        // Top-Right Zoom Control
        L.control.zoom({ position: 'topright' }).addTo(map);

        // Basemaps: OpenStreetMap & Esri Satellite HD
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        });

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        const topoLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data: &copy; OpenTopoMap (CC-BY-SA)'
        });

        // Default to satellite layer for true plantation landscape view
        satelliteLayer.addTo(map);

        const baseMaps = {
            "Satelit HD (Esri)": satelliteLayer,
            "Peta Jalan (OSM)": osmLayer,
            "Topografi Relief": topoLayer
        };

        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        // Add layer groups to map
        layerGroupIpal.addTo(map);
        layerGroupSumur.addTo(map);
        layerGroupBlok.addTo(map);

        // Render markers
        renderIpalMarker();
        renderSumurMarkers();
        renderBlokPolygons();
    }

    // 1. Render Marker IPAL Outlet Effluent
    function renderIpalMarker() {
        if (!pksData.latTitikPenaatan || !pksData.longTitikPenaatan) return;

        const ipalIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="pulse-marker" title="Titik Penaatan IPAL"></div>`,
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        const marker = L.marker([pksData.latTitikPenaatan, pksData.longTitikPenaatan], { icon: ipalIcon });
        
        const popupContent = `
            <div class="popup-header bg-danger">
                <i class="feather-radio me-1"></i> ${pksData.namaTitikPenaatan}
            </div>
            <div class="popup-body">
                <div class="mb-1 text-muted font-11">Titik Penaatan Effluent Outlet PKS</div>
                <div class="mb-2"><strong>Koordinat:</strong> ${pksData.koordinatText || (pksData.latTitikPenaatan + ', ' + pksData.longTitikPenaatan)}</div>
                <div class="d-flex justify-content-between py-1 border-top border-bottom font-11 mb-2">
                    <span>Debit Maks Izin:</span>
                    <strong class="text-success">${pksData.debitMax} m³/hari</strong>
                </div>
                <div class="d-flex justify-content-between font-11">
                    <span>BOD Maksimum:</span>
                    <strong>${pksData.bodMax} mg/L</strong>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);
        layerGroupIpal.addLayer(marker);
    }

    // 2. Render Markers Sumur Pantau Air Tanah
    function renderSumurMarkers() {
        pksData.sumurPantau.forEach((sp, idx) => {
            if (!sp.lat || !sp.lng) return;

            const wellIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="well-marker" title="${sp.nama}"><i class="feather-droplet"></i></div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            const marker = L.marker([sp.lat, sp.lng], { icon: wellIcon });
            
            const popupContent = `
                <div class="popup-header" style="background:#1d4ed8;">
                    <i class="feather-droplet me-1"></i> ${sp.nama}
                </div>
                <div class="popup-body">
                    <div class="mb-1"><span class="badge bg-primary-subtle text-primary border">${sp.jenis}</span></div>
                    <div class="mb-1"><strong>Lokasi Blok:</strong> ${sp.blok}</div>
                    <div class="mb-1"><strong>Koordinat:</strong> ${sp.koordinatText || (sp.lat + ', ' + sp.lng)}</div>
                    <div class="small text-muted border-top pt-1 mt-1">Frekuensi Pantau: ${sp.frekuensi} (Laboratorium Terakreditasi)</div>
                </div>
            `;

            marker.bindPopup(popupContent);
            layerGroupSumur.addLayer(marker);
        });
    }

    // 3. Render Blok-Blok Land Application
    function renderBlokPolygons() {
        pksData.bloks.forEach(blok => {
            if (!blok.lat || !blok.lng) return;

            // Generate polygon shape around center coordinate (~ 250m x 250m box per blok)
            const d = 0.0022; // ~240 meter offset
            const bounds = [
                [blok.lat - d, blok.lng - d],
                [blok.lat + d, blok.lng - d],
                [blok.lat + d, blok.lng + d],
                [blok.lat - d, blok.lng + d]
            ];

            const fillColor = blok.isActive7d ? '#22c55e' : '#f59e0b';
            const strokeColor = blok.isActive7d ? '#15803d' : '#b45309';

            const polygon = L.polygon(bounds, {
                color: strokeColor,
                weight: 2,
                fillColor: fillColor,
                fillOpacity: 0.55
            });

            // Blok Center Label Marker
            const labelIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background:rgba(0,0,0,0.75);color:#fff;font-size:10px;font-weight:bold;padding:1px 5px;border-radius:4px;white-space:nowrap;transform:translate(-50%,-50%);">${blok.nama}</div>`,
                iconSize: [0, 0]
            });
            const labelMarker = L.marker([blok.lat, blok.lng], { icon: labelIcon });

            const popupContent = `
                <div class="popup-header">
                    <i class="feather-grid me-1"></i> Blok ${blok.nama} (${blok.afdeling})
                </div>
                <div class="popup-body">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Luas Blok:</span>
                        <strong>${blok.luas} Ha</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Jumlah Flatbed:</span>
                        <strong>${blok.flatbed} Bed</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                        <span class="text-muted">Panjang Parit:</span>
                        <span>${blok.parit ? blok.parit + ' m' : '-'}</span>
                    </div>
                    <div class="p-2 rounded-2 ${blok.isActive7d ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-secondary'} font-11">
                        <div class="fw-bold">${blok.isActive7d ? '🟢 Sedang Aktif Dialiri' : '🟡 Standby / Rotasi'}</div>
                        <div>Terakhir dialiri: <strong>${blok.lastFlowDate || '-'}</strong> (${blok.volLimbah} m³)</div>
                    </div>
                </div>
            `;

            polygon.bindPopup(popupContent);
            labelMarker.bindPopup(popupContent);

            layerGroupBlok.addLayer(polygon);
            layerGroupBlok.addLayer(labelMarker);
        });
    }

    // Toggle layer controls
    function toggleLayer(layerName) {
        if (layerName === 'ipal') {
            const chk = document.getElementById('layerIpal').checked;
            if (chk) map.addLayer(layerGroupIpal); else map.removeLayer(layerGroupIpal);
        } else if (layerName === 'sumur') {
            const chk = document.getElementById('layerSumur').checked;
            if (chk) map.addLayer(layerGroupSumur); else map.removeLayer(layerGroupSumur);
        } else if (layerName === 'blok') {
            const chk = document.getElementById('layerBlok').checked;
            if (chk) map.addLayer(layerGroupBlok); else map.removeLayer(layerGroupBlok);
        }
    }

    // Zoom to specific block on list click
    function zoomToBlok(lat, lng, namaBlok) {
        if (lat && lng) {
            map.flyTo([lat, lng], 16, { animate: true, duration: 1.2 });
        }
    }

    // Toggle floating sidebar
    function toggleGisSidebar() {
        const panel = document.getElementById('gisPanel');
        panel.classList.toggle('collapsed');
    }
</script>
@endsection
