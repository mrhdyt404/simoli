const CACHE_NAME = 'simoli-pwa-v7';

const STATIC_ASSETS = [
    '/manifest.webmanifest',
    '/favicon.ico',
    '/logo/Icon%20SIMOLI.png',
    '/logo/Logo%20SIMOLI.png',
    '/js/simoli-offline-db.js',
    '/js/simoli-sync-manager.js',
    '/js/simoli-notifications.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.css',
    'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js',
    'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap'
];

// Fallback HTML Generator for Standalone Offline Form
function generateOfflineCreateFormHTML() {
    return `<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0F52BA">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>Input Laporan Kerja - SIMOLI (Offline)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.css">
    <style>
        :root { --op-primary: #0F52BA; --op-bg: #F4F6FB; --op-card-bg: #FFFFFF; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--op-bg); color: #1E293B; padding-bottom: 85px; -webkit-tap-highlight-color: transparent; }
        .operator-topbar { background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%); color: #FFFFFF; padding: 12px 16px; position: sticky; top: 0; z-index: 1030; box-shadow: 0 4px 20px rgba(15,82,186,0.25); }
        .op-card { background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; margin-bottom: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .op-card-header { padding: 14px 16px; border-bottom: 1px solid #F1F5F9; background: #FAFAFC; font-weight: 700; font-size: 0.92rem; }
        .op-card-body { padding: 16px; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 6px; }
        .form-control, .form-select { border-radius: 12px; border: 1.5px solid #CBD5E1; padding: 12px 14px; font-size: 0.95rem; }
        .btn-op-primary { background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%); color: #FFFFFF; border: none; border-radius: 14px; padding: 14px 20px; font-weight: 700; width: 100%; box-shadow: 0 4px 14px rgba(15,82,186,0.3); }
        .photo-preview-box { width: 100%; height: 160px; border: 2px dashed #CBD5E1; border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #F8FAFC; cursor: pointer; position: relative; overflow: hidden; }
        .photo-preview-box img { width: 100%; height: 100%; object-fit: cover; }
        .operator-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #FFFFFF; height: 68px; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #E2E8F0; z-index: 1040; }
        .nav-item-link { display: flex; flex-direction: column; align-items: center; color: #64748B; text-decoration: none; font-size: 0.75rem; font-weight: 600; }
        .nav-item-link.active { color: var(--op-primary); font-weight: 700; }
        #simoli-toast-container { position: fixed; top: 75px; left: 50%; transform: translateX(-50%); z-index: 1060; width: 90%; max-width: 480px; }
    </style>
</head>
<body>
    <header class="operator-topbar d-flex justify-content-between align-items-center">
        <a href="/operator" class="text-white text-decoration-none fw-bold"><i class="feather-truck me-1"></i> SIMOLI OPERATOR</a>
        <span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="feather-wifi-off me-1"></i> Offline Lapangan</span>
    </header>

    <div id="simoli-toast-container"></div>

    <main class="container px-3 pt-3" style="max-width: 720px;">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold m-0"><i class="feather-plus-circle me-2 text-primary"></i>Input Laporan Kerja (Mode Offline)</h5>
            <a href="/operator" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="feather-arrow-left me-1"></i>Beranda</a>
        </div>

        <div class="alert alert-warning border-0 rounded-3 p-2.5 mb-3 d-flex align-items-center gap-2 fs-12">
            <i class="feather-info text-warning fs-5"></i>
            <span>Mode Offline aktif. Laporan & foto akan langsung tersimpan di memori HP dan otomatis tersinkron saat terhubung sinyal.</span>
        </div>

        <form id="formOperatorReport">
            <!-- 1. Informasi Unit -->
            <div class="op-card mb-3">
                <div class="op-card-header"><i class="feather-truck me-2 text-primary"></i>1. Informasi Unit & Pekerjaan</div>
                <div class="op-card-body">
                    <div class="mb-3">
                        <label class="form-label">Unit Alat Berat <span class="text-danger">*</span></label>
                        <select name="alat_berat_id" id="select_alat_berat" class="form-select" required>
                            <option value="">-- Memuat Alat Berat dari Memori HP... --</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="${new Date().toISOString().split('T')[0]}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Operator <span class="text-danger">*</span></label>
                            <input type="text" name="operator" id="input_operator" class="form-control" required placeholder="Nama Operator">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kegiatan / Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="kegiatan" class="form-control" required placeholder="Contoh: Pengerukan kolam / Aplikasi Lahan">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Lokasi / Blok Pekerjaan</label>
                        <input type="text" name="lokasi_blok" class="form-control" placeholder="Contoh: Kolam 2 / Blok C18">
                    </div>
                </div>
            </div>

            <!-- 2. Lokasi GPS -->
            <div class="op-card mb-3">
                <div class="op-card-header"><i class="feather-map-pin me-2 text-primary"></i>2. Lokasi GPS Kerja</div>
                <div class="op-card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fs-12 text-primary">GPS Awal</label>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-pill mb-2 py-1 fs-11" onclick="getGps('latitude_awal', 'longitude_awal')"><i class="feather-crosshair me-1"></i>Kunci GPS Awal</button>
                            <input type="text" name="latitude_awal" id="latitude_awal" class="form-control form-control-sm bg-light" readonly placeholder="Lat Awal">
                            <input type="text" name="longitude_awal" id="longitude_awal" class="form-control form-control-sm bg-light mt-1" readonly placeholder="Long Awal">
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-12 text-success">GPS Akhir</label>
                            <button type="button" class="btn btn-sm btn-outline-success w-100 rounded-pill mb-2 py-1 fs-11" onclick="getGps('latitude_akhir', 'longitude_akhir')"><i class="feather-crosshair me-1"></i>Kunci GPS Akhir</button>
                            <input type="text" name="latitude_akhir" id="latitude_akhir" class="form-control form-control-sm bg-light" readonly placeholder="Lat Akhir">
                            <input type="text" name="longitude_akhir" id="longitude_akhir" class="form-control form-control-sm bg-light mt-1" readonly placeholder="Long Akhir">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Foto Dokumentasi -->
            <div class="op-card mb-3">
                <div class="op-card-header"><i class="feather-camera me-2 text-primary"></i>3. Foto Dokumentasi Kerja</div>
                <div class="op-card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label text-center d-block fs-12">Foto Sebelum</label>
                            <div class="photo-preview-box" onclick="document.getElementById('foto_sebelum').click()">
                                <div id="ph_sebelum" class="text-center p-2"><i class="feather-camera fs-3 text-muted"></i><span class="fs-11 text-muted d-block">Ambil Foto</span></div>
                                <img id="img_sebelum" src="" style="display:none;">
                            </div>
                            <input type="file" name="foto_sebelum" id="foto_sebelum" accept="image/*" capture="environment" style="display:none;" onchange="previewImg(this, 'img_sebelum', 'ph_sebelum', 'hm_awal')">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-center d-block fs-12">Foto Sesudah</label>
                            <div class="photo-preview-box" onclick="document.getElementById('foto_sesudah').click()">
                                <div id="ph_sesudah" class="text-center p-2"><i class="feather-camera fs-3 text-muted"></i><span class="fs-11 text-muted d-block">Ambil Foto</span></div>
                                <img id="img_sesudah" src="" style="display:none;">
                            </div>
                            <input type="file" name="foto_sesudah" id="foto_sesudah" accept="image/*" capture="environment" style="display:none;" onchange="previewImg(this, 'img_sesudah', 'ph_sesudah', 'hm_akhir')">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. HM Jam Kerja -->
            <div class="op-card mb-3">
                <div class="op-card-header"><i class="feather-clock me-2 text-primary"></i>4. Jam Kerja (Hour Meter)</div>
                <div class="op-card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Jam Awal</label>
                            <input type="text" name="hm_awal" id="hm_awal" class="form-control bg-light" placeholder="HH:MM" oninput="calcHm()">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Jam Akhir</label>
                            <input type="text" name="hm_akhir" id="hm_akhir" class="form-control bg-light" placeholder="HH:MM" oninput="calcHm()">
                        </div>
                    </div>
                    <div class="p-2 bg-light rounded text-center border mt-2">
                        <span class="text-muted fs-12">Total HM Kerja:</span>
                        <span id="total_hm_label" class="fw-bold fs-5 text-success ms-2">00:00 Jam</span>
                        <input type="hidden" name="total_hm" id="total_hm" value="0">
                    </div>
                </div>
            </div>

            <!-- 5. Aplikasi Bed -->
            <div class="op-card mb-3">
                <div class="op-card-header"><i class="feather-grid me-2 text-primary"></i>5. Hasil Aplikasi Bed</div>
                <div class="op-card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Flat Bed</label>
                            <input type="number" name="flat_bed" id="flat_bed" class="form-control" value="0" min="0" oninput="calcBed()">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Long Bed</label>
                            <input type="number" name="long_bed" id="long_bed" class="form-control" value="0" min="0" oninput="calcBed()">
                        </div>
                    </div>
                    <div class="p-2 bg-light rounded text-center border mt-1">
                        <span class="text-muted fs-12">Total Bed:</span>
                        <span id="total_bed_label" class="fw-bold fs-5 text-primary ms-2">0</span> Bed
                        <input type="hidden" name="jumlah_bed" id="jumlah_bed" value="0">
                    </div>
                </div>
            </div>

            <!-- 6. BBM & Kondisi -->
            <div class="op-card mb-3">
                <div class="op-card-header"><i class="feather-check-square me-2 text-primary"></i>6. BBM & Kondisi Alat</div>
                <div class="op-card-body">
                    <div class="mb-3">
                        <label class="form-label">Pengisian BBM (Liter)</label>
                        <input type="number" step="0.1" name="bbm_liter" class="form-control" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kondisi Alat Berat</label>
                        <select name="kondisi_alat" class="form-select">
                            <option value="Normal">Normal / Baik</option>
                            <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                            <option value="Breakdown">Breakdown (Rusak)</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Catatan Operasional</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan kondisi lapangan..."></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" id="btnSubmit" class="btn btn-op-primary mb-4 py-3 shadow-lg">
                <i class="feather-save me-2"></i>SIMPAN LAPORAN KERJA (OFFLINE)
            </button>
        </form>
    </main>

    <nav class="operator-bottom-nav">
        <a href="/operator" class="nav-item-link"><i class="feather-home fs-5"></i><span>Beranda</span></a>
        <a href="/operator/create" class="nav-item-link active"><i class="feather-file-plus fs-5"></i><span>Input Laporan</span></a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="/js/simoli-offline-db.js"></script>
    <script src="/js/simoli-sync-manager.js"></script>
    <script src="/js/simoli-notifications.js"></script>

    <script>
        feather.replace();

        function calcBed() {
            const f = parseInt(document.getElementById('flat_bed').value) || 0;
            const l = parseInt(document.getElementById('long_bed').value) || 0;
            document.getElementById('total_bed_label').innerText = f + l;
            document.getElementById('jumlah_bed').value = f + l;
        }

        function calcHm() {
            const a = document.getElementById('hm_awal').value;
            const b = document.getElementById('hm_akhir').value;
            if (!a || !b) return;
            const parseMins = (str) => {
                const parts = str.split(':');
                return (parseInt(parts[0]||0)*60) + (parseInt(parts[1]||0));
            };
            let diff = parseMins(b) - parseMins(a);
            if (diff < 0) diff += 1440;
            const h = Math.floor(diff/60);
            const m = diff%60;
            document.getElementById('total_hm_label').innerText = (h<10?'0'+h:h) + ':' + (m<10?'0'+m:m) + ' Jam';
            document.getElementById('total_hm').value = (diff/60).toFixed(2);
        }

        function getGps(latId, longId) {
            if (!navigator.geolocation) {
                alert('Browser ini tidak mendukung GPS.');
                return;
            }

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span>Kunci GPS...';

            function handleSuccess(pos, source) {
                document.getElementById(latId).value = pos.coords.latitude.toFixed(7);
                document.getElementById(longId).value = pos.coords.longitude.toFixed(7);
                btn.innerHTML = '✓ GPS Terkunci (' + source + ')';
                btn.className = btn.className.replace('btn-outline-', 'btn-');
            }

            function handleError(err) {
                let msg = 'Gagal merekam GPS. Pastikan GPS/Lokasi HP aktif & berada di luar ruangan.';
                if (err.code === 1) msg = 'Izin lokasi (GPS) ditolak. Aktifkan izin lokasi browser/HP.';
                alert(msg);
                btn.innerHTML = originalText;
            }

            // Tier 1: Try cached position
            navigator.geolocation.getCurrentPosition(
                (pos) => handleSuccess(pos, 'Cache'),
                () => {
                    // Tier 2: Real-time satellite fix
                    navigator.geolocation.getCurrentPosition(
                        (pos) => handleSuccess(pos, 'Satelit'),
                        (err) => {
                            // Tier 3: Low-accuracy fallback
                            navigator.geolocation.getCurrentPosition(
                                (pos) => handleSuccess(pos, 'Perangkat'),
                                (errFinal) => handleError(errFinal),
                                { enableHighAccuracy: false, timeout: 20000, maximumAge: 600000 }
                            );
                        },
                        { enableHighAccuracy: true, timeout: 25000, maximumAge: 120000 }
                    );
                },
                { enableHighAccuracy: false, timeout: 4000, maximumAge: 300000 }
            );
        }

        function previewImg(input, imgId, phId, hmId) {
            if (input.files && input.files[0]) {
                const r = new FileReader();
                r.onload = (e) => {
                    document.getElementById(imgId).src = e.target.result;
                    document.getElementById(imgId).style.display = 'block';
                    document.getElementById(phId).style.display = 'none';
                    const now = new Date();
                    const hh = String(now.getHours()).padStart(2, '0');
                    const mm = String(now.getMinutes()).padStart(2, '0');
                    document.getElementById(hmId).value = hh + ':' + mm;
                    calcHm();
                };
                r.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await SimoliDB.init();
            
            // Populate operator name
            const username = await SimoliDB.getConfig('username');
            if (username) document.getElementById('input_operator').value = username;

            // Populate equipment list from IndexedDB
            const select = document.getElementById('select_alat_berat');
            const list = await SimoliDB.getMasterAlatBerat();
            if (list && list.length > 0) {
                select.innerHTML = '<option value="">-- Pilih Unit Alat Berat --</option>';
                list.forEach(ab => {
                    const opt = document.createElement('option');
                    opt.value = ab.id;
                    opt.textContent = '[' + ab.kode_alat + '] ' + ab.nama_alat + ' (' + (ab.jenis_alat||'Unit') + ')';
                    select.appendChild(opt);
                });
            } else {
                select.innerHTML = '<option value="1">Unit Alat Berat Utama</option>';
            }

            // Handle Submit
            const form = document.getElementById('formOperatorReport');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan ke Memori HP...';

                try {
                    await SimoliSync.saveReportFromForm(form);
                    alert('✓ Laporan Berhasil Disimpan di HP (Mode Offline Lapangan). Otomatis disinkronkan saat ada sinyal.');
                    window.location.href = '/operator';
                } catch (err) {
                    alert('Gagal simpan: ' + err.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="feather-save me-2"></i>SIMPAN LAPORAN KERJA (OFFLINE)';
                }
            });
        });
    </script>
</body>
</html>`;
}

// Install Event
self.addEventListener('install', (event) => {
    console.log('[SW v5] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.allSettled(
                STATIC_ASSETS.map((asset) => {
                    return fetch(asset, { cache: 'no-cache' })
                        .then((res) => {
                            if (res.ok || res.type === 'opaque') {
                                return cache.put(asset, res);
                            }
                        })
                        .catch((err) => console.warn('[SW v5] Skip cache asset:', asset, err));
                })
            );
        }).then(() => {
            console.log('[SW v5] Pre-caching complete.');
            return self.skipWaiting();
        })
    );
});

// Activate Event
self.addEventListener('activate', (event) => {
    console.log('[SW v5] Activating & Purging Old Caches...');
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Bypass API routes
    if (url.pathname.startsWith('/api/')) {
        return;
    }

    // Only process GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Strategy 1: HTML Navigation Requests
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    // Only cache valid authenticated pages
                    if (networkResponse && networkResponse.status === 200 && !networkResponse.redirected && !networkResponse.url.includes('/login')) {
                        const clone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, clone);
                            cache.put(url.pathname, networkResponse.clone());
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    const path = url.pathname;

                    // 1. Try matching cached response
                    const cached = await caches.match(event.request, { ignoreSearch: true }) || 
                                   await caches.match(path, { ignoreSearch: true });
                    if (cached && !cached.url.includes('/login')) {
                        return cached;
                    }

                    // 2. Specific routes
                    if (path.includes('/operator/create')) {
                        const createPage = await caches.match('/operator/create', { ignoreSearch: true }) ||
                                           await caches.match(url.origin + '/operator/create', { ignoreSearch: true });
                        if (createPage && !createPage.url.includes('/login')) return createPage;
                    }

                    if (path.includes('/operator')) {
                        const opPage = await caches.match('/operator', { ignoreSearch: true }) ||
                                       await caches.match(url.origin + '/operator', { ignoreSearch: true });
                        if (opPage && !opPage.url.includes('/login')) return opPage;
                    }

                    // 3. Fallback to Standalone Offline Create Form HTML if navigating to create
                    if (path.includes('create') || path.includes('operator')) {
                        return new Response(generateOfflineCreateFormHTML(), {
                            headers: { 'Content-Type': 'text/html; charset=utf-8' }
                        });
                    }

                    return new Response(generateOfflineCreateFormHTML(), {
                        headers: { 'Content-Type': 'text/html; charset=utf-8' }
                    });
                })
        );
        return;
    }

    // Strategy 2: Static Assets (Cache-First)
    event.respondWith(
        caches.match(event.request, { ignoreSearch: true }).then((cachedResponse) => {
            if (cachedResponse) return cachedResponse;

            return fetch(event.request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const clone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, clone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => new Response('', { status: 408, statusText: 'Offline' }));
        })
    );
});

// Notification Click Event (PWA Window Focus / Navigation)
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url) 
        ? event.notification.data.url 
        : '/operator';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Check if there is already an open window
            for (const client of clientList) {
                if ('focus' in client) {
                    if (client.url.includes('/operator')) {
                        client.navigate(targetUrl);
                        return client.focus();
                    }
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Push Notification Event (Web Push Fallback)
self.addEventListener('push', (event) => {
    let payload = {
        title: 'SIMOLI Monitoring',
        body: 'Pemberitahuan baru dari SIMOLI',
        icon: '/logo/Icon%20SIMOLI.png',
        url: '/operator'
    };

    if (event.data) {
        try {
            payload = event.data.json();
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(payload.title || 'SIMOLI Monitoring', {
            body: payload.body,
            icon: payload.icon || '/logo/Icon%20SIMOLI.png',
            badge: '/logo/Icon%20SIMOLI.png',
            vibrate: [200, 100, 200],
            data: { url: payload.url || '/operator' }
        })
    );
});
