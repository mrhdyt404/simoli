<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>PETA LOKASI LAND APPLICATION (LA) — KEBUN {{ $pks->nama }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #e2e8f0;
            display: flex;
            justify-content: center;
            padding: 20px;
            color: #0f172a;
        }

        /* Printable Map Sheet Frame */
        .carto-sheet {
            width: 1120px;
            height: 780px;
            background: #ffffff;
            border: 3px solid #000000;
            padding: 10px;
            display: flex;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
        }

        /* Inner Double Border */
        .carto-sheet::before {
            content: '';
            position: absolute;
            top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 1px solid #000000;
            pointer-events: none;
        }

        /* Left Side: Map Frame */
        .map-view-box {
            flex: 1;
            border: 2px solid #000000;
            position: relative;
            background: #f1f5f9;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        #carto-leaflet-map {
            width: 100%;
            height: 100%;
        }

        /* Right Side: Information Column */
        .carto-info-column {
            width: 320px;
            border: 2px solid #000000;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            font-size: 11px;
        }

        .carto-box {
            border-bottom: 2px solid #000000;
            padding: 10px;
        }

        .carto-box:last-child {
            border-bottom: none;
        }

        /* Title Box */
        .carto-title-box {
            text-align: center;
            padding: 12px 10px;
        }
        .carto-title-box h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        .carto-title-box h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #000;
            margin-top: 3px;
        }

        /* North Arrow & Scale */
        .carto-scale-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
        }
        .north-arrow {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        .north-arrow i {
            font-size: 24px;
            display: block;
            margin-bottom: -4px;
        }
        .scale-bar-text {
            text-align: right;
            font-size: 10.5px;
        }

        /* Admin Info */
        .admin-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }

        /* Inset Map Box */
        .inset-box {
            height: 140px;
            background: #bae6fd;
            border: 1px solid #0284c7;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }
        .inset-box-title {
            position: absolute;
            top: 4px;
            left: 0; right: 0;
            font-weight: 800;
            font-size: 10.5px;
            text-transform: uppercase;
        }

        /* Metadata Box */
        .meta-list {
            list-style: none;
            font-size: 9.5px;
            line-height: 1.4;
        }
        .meta-list strong {
            display: block;
            margin-top: 3px;
        }

        /* Validation Table */
        .table-val {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            text-align: center;
            margin-top: 4px;
        }
        .table-val th, .table-val td {
            border: 1px solid #000000;
            padding: 3px 2px;
        }
        .table-val th {
            background: #f1f5f9;
            font-weight: bold;
        }

        /* Footer Issuer Box */
        .carto-issuer-box {
            margin-top: auto;
            text-align: center;
            padding: 8px;
            background: #f8fafc;
        }
        .issuer-title {
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .issuer-company {
            font-size: 10.5px;
            font-weight: 900;
            margin-top: 2px;
        }

        /* Print Controls Floating */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 9999;
        }
        .btn-print {
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #15803d; }
        .btn-close-win {
            background: #475569;
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .print-actions { display: none; }
            .carto-sheet {
                width: 100vw;
                height: 100vh;
                border: 2px solid #000;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <button type="button" class="btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> Cetak Dokumen Peta (PDF)
        </button>
        <button type="button" class="btn-close-win" onclick="window.close()">
            <i class="fa fa-times"></i> Tutup
        </button>
    </div>

    <div class="carto-sheet">
        
        {{-- SISI KIRI: KANVAS PETA SPASIAL --}}
        <div class="map-view-box">
            <div id="carto-leaflet-map"></div>
        </div>

        {{-- SISI KANAN: KOLOM KARTOGRAFI & LEGENDA --}}
        <div class="carto-info-column">
            
            {{-- 1. Judul Peta --}}
            <div class="carto-box carto-title-box">
                <h2>PETA LOKASI LAND APPLICATION (LA)</h2>
                <h1>KEBUN {{ strtoupper($pks->nama) }}</h1>
            </div>

            {{-- 2. Arah Mata Angin & Skala --}}
            <div class="carto-box carto-scale-box">
                <div class="north-arrow">
                    <i class="fa fa-compass text-danger"></i>
                    <span>U</span>
                </div>
                <div class="scale-bar-text">
                    <div><strong>Skala 1 : 50.000</strong></div>
                    <div style="font-size:9px;color:#64748b;">0 &nbsp; 250 &nbsp; 500 &nbsp; 1000 Meter</div>
                </div>
            </div>

            {{-- 3. Info Administrasi --}}
            <div class="carto-box" style="padding: 6px 10px;">
                <div class="admin-row"><span>Desa / Kel:</span><strong>-</strong></div>
                <div class="admin-row"><span>Kecamatan:</span><strong>{{ $pks->nama }}</strong></div>
                <div class="admin-row"><span>Kabupaten:</span><strong>{{ str_contains($perizinan->instansi_penerbit ?? '', 'Kampar') ? 'Kampar' : 'Rokan Hilir' }}</strong></div>
                <div class="admin-row"><span>Provinsi:</span><strong>Riau</strong></div>
            </div>

            {{-- 4. Legenda Keterangan --}}
            <div class="carto-box" style="padding: 6px 10px;">
                <strong style="font-size: 10px; text-transform: uppercase;">Keterangan / Legenda:</strong>
                <div style="margin-top: 4px; font-size: 9.5px; line-height: 1.6;">
                    <div><span style="display:inline-block;width:10px;height:10px;background:#dc2626;border-radius:50%;margin-right:4px;"></span> Titik Penaatan Effluent IPAL</div>
                    <div><span style="display:inline-block;width:10px;height:10px;background:#2563eb;border-radius:50%;margin-right:4px;"></span> Sumur Pantau Air Tanah</div>
                    <div><span style="display:inline-block;width:10px;height:10px;background:#16a34a;margin-right:4px;"></span> Blok Land Application ({{ $perizinan->luas_areal_izin ?? 250 }} Ha)</div>
                    <div><span style="display:inline-block;width:10px;height:10px;background:#f59e0b;margin-right:4px;"></span> Areal Standby / Rotasi</div>
                </div>
            </div>

            {{-- 5. Inset Peta Kebun --}}
            <div class="carto-box" style="padding: 6px 10px;">
                <div class="inset-box">
                    <div class="inset-box-title">PETA KEBUN {{ $pks->akro }}</div>
                    <div style="font-size:9px;color:#0369a1;margin-top:15px;">
                        <i class="fa fa-map-marked-alt fa-2x"></i><br>
                        [ Lokasi Land Application ]
                    </div>
                </div>
            </div>

            {{-- 6. Sistem Proyeksi Koordinat --}}
            <div class="carto-box" style="padding: 6px 10px;">
                <ul class="meta-list">
                    <li><strong>Sistem Proyeksi Koordinat:</strong></li>
                    <li>Sistem Koordinat WGS 84 (UTM Zona 47N)</li>
                    <li>Proyeksi Transverse Mercator</li>
                    <li>Datum WGS 84 &bull; Unit Meter</li>
                    <li><strong>Sumber Peta:</strong> Peta Administrasi, Peta Shapefile Kebun, Survey Pemetaan Lahan GIS PTPN</li>
                </ul>
            </div>

            {{-- 7. Tabel Pengesahan --}}
            <div class="carto-box" style="padding: 6px 10px;">
                <div style="font-size:9px;font-weight:bold;margin-bottom:2px;">
                    Nomor Peta: PTPN-IV/GIS/{{ $pks->akro }}/{{ date('Y') }}
                </div>
                <table class="table-val">
                    <thead>
                        <tr>
                            <th>URAIAN</th>
                            <th>NAMA</th>
                            <th>JABATAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Dibuat Oleh</td>
                            <td>Tim GIS</td>
                            <td>Staf GIS</td>
                        </tr>
                        <tr>
                            <td>Diperiksa Oleh</td>
                            <td>{{ $pks->manager ?? 'Manager Unit' }}</td>
                            <td>Manager PKS</td>
                        </tr>
                        <tr>
                            <td>Disahkan Oleh</td>
                            <td>Head GIS Regional</td>
                            <td>Sub Bagian GIS</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 8. Penerbit --}}
            <div class="carto-issuer-box">
                <div class="issuer-title">Dikeluarkan Oleh:</div>
                <div style="font-size:9px;font-weight:700;">Sub Bagian GIS & Digitalisasi Proses Bisnis Tanaman</div>
                <div class="issuer-company">PT. PERKEBUNAN NUSANTARA IV</div>
                <div style="font-size:8.5px;color:#64748b;">Regional III &bull; Pekanbaru - Riau</div>
            </div>

        </div>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const latCenter = {{ $perizinan->lat_titik_penaatan ?? 1.742525 }};
        const lngCenter = {{ $perizinan->long_titik_penaatan ?? 100.510011 }};

        const map = L.map('carto-leaflet-map', {
            center: [latCenter, lngCenter],
            zoom: 14,
            zoomControl: false,
            attributionControl: false
        });

        // Satellite Basemap
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 18
        }).addTo(map);

        // Marker Titik IPAL
        const ipalIcon = L.divIcon({
            className: 'custom-pin',
            html: `<div style="width:16px;height:16px;background:#dc2626;border:2px solid #fff;border-radius:50%;box-shadow:0 0 6px rgba(0,0,0,0.6);"></div>`,
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });
        L.marker([latCenter, lngCenter], { icon: ipalIcon }).addTo(map);

        // Marker Sumur Pantau
        @if($perizinan && $perizinan->sumurPantau)
        @foreach($perizinan->sumurPantau as $sp)
        @if($sp->latitude && $sp->longitude)
        const wellIcon{{ $loop->index }} = L.divIcon({
            className: 'custom-well',
            html: `<div style="width:14px;height:14px;background:#2563eb;border:2px solid #fff;border-radius:50%;box-shadow:0 0 4px rgba(0,0,0,0.6);"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });
        L.marker([{{ $sp->latitude }}, {{ $sp->longitude }}], { icon: wellIcon{{ $loop->index }} }).addTo(map);
        @endif
        @endforeach
        @endif

        // Render Blok-blok Poligon
        @foreach($blokList as $b)
        @if($b->latitude_center && $b->longitude_center)
        const d{{ $loop->index }} = 0.0022;
        const bounds{{ $loop->index }} = [
            [{{ $b->latitude_center }} - d{{ $loop->index }}, {{ $b->longitude_center }} - d{{ $loop->index }}],
            [{{ $b->latitude_center }} + d{{ $loop->index }}, {{ $b->longitude_center }} - d{{ $loop->index }}],
            [{{ $b->latitude_center }} + d{{ $loop->index }}, {{ $b->longitude_center }} + d{{ $loop->index }}],
            [{{ $b->latitude_center }} - d{{ $loop->index }}, {{ $b->longitude_center }} + d{{ $loop->index }}]
        ];
        L.polygon(bounds{{ $loop->index }}, {
            color: '#15803d',
            weight: 1.5,
            fillColor: '#22c55e',
            fillOpacity: 0.6
        }).addTo(map);

        L.marker([{{ $b->latitude_center }}, {{ $b->longitude_center }}], {
            icon: L.divIcon({
                className: 'custom-lbl',
                html: `<div style="background:rgba(0,0,0,0.7);color:#fff;font-size:9px;font-weight:bold;padding:1px 3px;border-radius:2px;transform:translate(-50%,-50%);">${'{{ $b->nama_blok }}'}</div>`
            })
        }).addTo(map);
        @endif
        @endforeach
    </script>
</body>
</html>
