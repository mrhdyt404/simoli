/**
 * SIMOLI PWA Sync Manager (v4)
 * Handles Offline Form Submissions, Client-Side Image Compression & Auto Background Push/Pull
 */
const SimoliSync = (() => {
    let isSyncing = false;
    let isOnlineState = navigator.onLine;

    // Helper: Convert File to DataURL fallback
    function fileToDataURL(file) {
        return new Promise((resolve) => {
            if (!file || !(file instanceof Blob)) return resolve(null);
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.onerror = () => resolve(null);
            reader.readAsDataURL(file);
        });
    }

    // --- 1. Client-Side Image Compression ---
    async function compressImage(file, maxDimension = 1024, quality = 0.7) {
        if (!file || !(file instanceof Blob)) return null;

        try {
            return await new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        try {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;

                            if (width > height) {
                                if (width > maxDimension) {
                                    height = Math.round((height * maxDimension) / width);
                                    width = maxDimension;
                                }
                            } else {
                                if (height > maxDimension) {
                                    width = Math.round((width * maxDimension) / height);
                                    height = maxDimension;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            const compressedBase64 = canvas.toDataURL('image/jpeg', quality);
                            resolve(compressedBase64);
                        } catch (canvasErr) {
                            console.warn('[SimoliSync] Canvas compression fallback:', canvasErr);
                            resolve(event.target.result);
                        }
                    };
                    img.onerror = () => resolve(event.target.result);
                };
                reader.onerror = () => resolve(null);
            });
        } catch (err) {
            console.warn('[SimoliSync] Compress error, fallback to raw dataURL:', err);
            return await fileToDataURL(file);
        }
    }

    // --- 2. Update Status Bar & UI Badges ---
    async function updateNetworkStatusUI() {
        const isOnline = navigator.onLine;
        isOnlineState = isOnline;

        const statusIndicator = document.getElementById('simoli-network-indicator');
        const queueBadge = document.getElementById('simoli-queue-badge');
        const queueCountEl = document.getElementById('simoli-queue-count');

        const pendingCount = await SimoliDB.getQueueCount();

        if (statusIndicator) {
            if (isOnline) {
                statusIndicator.className = 'badge bg-success d-inline-flex align-items-center gap-1 px-2.5 py-1.5 shadow-sm rounded-pill';
                statusIndicator.innerHTML = '<span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px;" role="status"></span> Online';
            } else {
                statusIndicator.className = 'badge bg-danger d-inline-flex align-items-center gap-1 px-2.5 py-1.5 shadow-sm rounded-pill';
                statusIndicator.innerHTML = '<i class="feather-wifi-off" style="font-size: 11px;"></i> Offline Lapangan';
                if (window.feather) feather.replace();
            }
        }

        if (queueBadge && queueCountEl) {
            queueCountEl.textContent = pendingCount;
            if (pendingCount > 0) {
                queueBadge.style.display = 'inline-flex';
            } else {
                queueBadge.style.display = 'none';
            }
        }
    }

    // --- 3. Pre-cache Operator Pages into Service Worker Cache ---
    async function precacheOperatorPages() {
        // Skip pre-cache jika berada di halaman login atau belum terautentikasi
        if (window.location.pathname === '/login' || window.location.pathname === '/') {
            return;
        }

        if ('caches' in window && navigator.onLine) {
            try {
                const cache = await caches.open('simoli-pwa-v6');
                const origin = window.location.origin;
                const pages = ['/operator', '/operator/create'];

                for (const page of pages) {
                    try {
                        const fullUrl = origin + page;
                        const res = await fetch(fullUrl, { credentials: 'same-origin', cache: 'reload' });
                        if (res.ok && !res.redirected && !res.url.includes('/login')) {
                            await cache.put(page, res.clone());
                            await cache.put(fullUrl, res.clone());
                            console.log('[SimoliSync] Pre-cached page for offline:', page);
                        }
                    } catch (e) {
                        console.warn('[SimoliSync] Failed to cache page:', page, e);
                    }
                }
            } catch (err) {
                console.warn('[SimoliSync] Pre-cache error:', err);
            }
        }
    }

    // --- 4. Pull Master Data from Server (Delta Sync) ---
    async function pullMasterData() {
        if (!navigator.onLine) return;
        // Hanya jalankan sync master data di halaman operator/PWA, bukan dashboard admin
        if (!window.location.pathname.startsWith('/operator')) return;

        try {
            const lastSync = await SimoliDB.getConfig('last_sync_time');
            const token = await SimoliDB.getConfig('auth_token');
            const userPks = await SimoliDB.getConfig('user_pks_id');

            const params = new URLSearchParams();
            if (lastSync) params.append('last_sync', lastSync);
            if (userPks) params.append('id_pks', userPks);

            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = 'Bearer ' + token;

            const res = await fetch('/api/sync/pull?' + params.toString(), { headers });
            if (!res.ok) return;

            const json = await res.json();
            if (json.success && json.data) {
                if (json.data.alat_berat && json.data.alat_berat.length > 0) {
                    await SimoliDB.saveMasterAlatBerat(json.data.alat_berat);
                }
                if (json.data.pks && json.data.pks.length > 0) {
                    await SimoliDB.saveMasterPks(json.data.pks);
                }
                if (json.data.recent_reports && json.data.recent_reports.length > 0) {
                    await SimoliDB.saveCachedReports(json.data.recent_reports);
                }
                if (json.server_time) {
                    await SimoliDB.setConfig('last_sync_time', json.server_time);
                }
                console.log('[SimoliSync] Pull data master berhasil diperbarui ke IndexedDB');
            }
        } catch (err) {
            console.warn('[SimoliSync] Gagal pull data:', err);
        }
    }

    // --- 5. Push Pending Offline Queue to Server ---
    async function pushPendingQueue(showToast = true) {
        if (isSyncing) return;
        if (!navigator.onLine) {
            if (showToast && window.showToastNotification) {
                showToastNotification('Perangkat sedang Offline. Data tetap aman di HP dan akan disinkronkan saat ada sinyal.', 'warning');
            }
            return;
        }

        const pendingItems = await SimoliDB.getPendingQueue();
        if (!pendingItems || pendingItems.length === 0) {
            if (showToast && window.showToastNotification) {
                showToastNotification('Semua data sudah tersinkron ke database server.', 'info');
            }
            return;
        }

        isSyncing = true;
        const syncBtn = document.getElementById('simoli-sync-btn');
        if (syncBtn) syncBtn.classList.add('syncing');

        try {
            const deviceId = await SimoliDB.getOrCreateDeviceId();
            const token = await SimoliDB.getConfig('auth_token');

            const payload = {
                items: pendingItems,
                device_id: deviceId
            };

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Device-ID': deviceId
            };
            if (token) headers['Authorization'] = 'Bearer ' + token;

            const response = await fetch('/api/sync/push', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                const syncedUuids = result.synced_uuids || [];
                await SimoliDB.markAsSynced(syncedUuids);

                if (window.SimoliNotify && syncedUuids.length > 0) {
                    SimoliNotify.notifySyncSuccess(syncedUuids.length);
                } else if (window.showToastNotification) {
                    showToastNotification(`✓ ${syncedUuids.length} laporan berhasil tersinkron ke database server!`, 'success');
                }
            } else {
                console.warn('[SimoliSync] Push error response:', result);
                if (showToast && window.showToastNotification) {
                    showToastNotification('Gagal menyinkronkan: ' + (result.message || 'Server error'), 'danger');
                }
            }
        } catch (err) {
            console.error('[SimoliSync] Network push failed:', err);
            if (showToast && window.showToastNotification) {
                showToastNotification('Gagal terhubung ke server. Data tetap tersimpan di HP.', 'warning');
            }
        } finally {
            isSyncing = false;
            if (syncBtn) syncBtn.classList.remove('syncing');
            await updateNetworkStatusUI();
        }
    }

    // --- 6. Save Report Offline (Universal Form Submission Pipeline) ---
    async function saveReportFromForm(formElement) {
        const formData = new FormData(formElement);
        const uuid = crypto.randomUUID ? crypto.randomUUID() : 'report_' + Date.now() + '_' + Math.random().toString(36).substring(2, 8);
        const deviceId = await SimoliDB.getOrCreateDeviceId();
        const userPks = (await SimoliDB.getConfig('user_pks_id')) || 9;
        const username = (await SimoliDB.getConfig('username')) || 'operator';

        // Compress photos safely
        const fotoSebelumFile = formElement.querySelector('input[name="foto_sebelum"]')?.files[0];
        const fotoSesudahFile = formElement.querySelector('input[name="foto_sesudah"]')?.files[0];

        let fotoSebelumBase64 = null;
        let fotoSesudahBase64 = null;

        if (fotoSebelumFile) {
            fotoSebelumBase64 = await compressImage(fotoSebelumFile, 1024, 0.7);
        }
        if (fotoSesudahFile) {
            fotoSesudahBase64 = await compressImage(fotoSesudahFile, 1024, 0.7);
        }

        const flatBed = parseInt(formData.get('flat_bed') || 0);
        const longBed = parseInt(formData.get('long_bed') || 0);
        const jumlahBed = parseInt(formData.get('jumlah_bed') || (flatBed + longBed));

        const latAwal = formData.get('latitude_awal') || formData.get('latitude') || '';
        const longAwal = formData.get('longitude_awal') || formData.get('longitude') || '';
        const latAkhir = formData.get('latitude_akhir') || '';
        const longAkhir = formData.get('longitude_akhir') || '';

        const reportObject = {
            uuid: uuid,
            id_pks: userPks,
            alat_berat_id: formData.get('alat_berat_id'),
            tanggal: formData.get('tanggal') || new Date().toISOString().split('T')[0],
            operator: formData.get('operator') || username,
            kegiatan: formData.get('kegiatan') || 'Monitoring Alat Berat',
            lokasi_blok: formData.get('lokasi_blok') || '',
            no_bak: formData.get('no_bak') || '',
            flat_bed: flatBed,
            long_bed: longBed,
            jumlah_bed: jumlahBed,
            latitude: latAwal,
            longitude: longAwal,
            latitude_awal: latAwal,
            longitude_awal: longAwal,
            latitude_akhir: latAkhir,
            longitude_akhir: longAkhir,
            hm_awal: formData.get('hm_awal') || '',
            hm_akhir: formData.get('hm_akhir') || '',
            total_hm: parseFloat(formData.get('total_hm') || 0),
            bbm_liter: parseFloat(formData.get('bbm_liter') || 0),
            kondisi_alat: formData.get('kondisi_alat') || 'Normal',
            catatan: formData.get('catatan') || '',
            foto_sebelum_base64: fotoSebelumBase64,
            foto_sesudah_base64: fotoSesudahBase64,
            client_created_at: new Date().toISOString(),
            sync_version: 1,
            device_id: deviceId
        };

        // 1. Save directly into IndexedDB Outbox (Guaranteed local save)
        await SimoliDB.addToQueue(reportObject);
        await updateNetworkStatusUI();

        // 2. Trigger PWA Input Notification
        if (window.SimoliNotify) {
            SimoliNotify.notifyInputSuccess({
                operator: reportObject.operator,
                alat: reportObject.alat_berat_id,
                tanggal: reportObject.tanggal,
                is_offline: !navigator.onLine
            });
        }

        // 3. If online, trigger background push immediately
        if (navigator.onLine) {
            pushPendingQueue(false);
        }

        return reportObject;
    }

    // --- 7. Initialize Global Event Listeners ---
    function init() {
        if (!window.location.pathname.startsWith('/operator')) {
            return;
        }

        SimoliDB.init().then(() => {
            updateNetworkStatusUI();
            if (navigator.onLine) {
                pullMasterData();
                precacheOperatorPages();
                pushPendingQueue(false);
            }
        });

        // Online & Offline Event Listeners
        window.addEventListener('online', () => {
            console.log('[SimoliSync] Jaringan terhubung kembali (ONLINE)');
            updateNetworkStatusUI();
            if (window.showToastNotification) {
                showToastNotification('🌐 Jaringan terhubung! Memulai sinkronisasi otomatis...', 'success');
            }
            pullMasterData();
            precacheOperatorPages();
            pushPendingQueue(true);
        });

        window.addEventListener('offline', () => {
            console.log('[SimoliSync] Jaringan terputus (OFFLINE)');
            updateNetworkStatusUI();
            if (window.showToastNotification) {
                showToastNotification('⚠️ Mode Offline Lapangan aktif. Data tersimpan aman di memori HP.', 'warning');
            }
        });

        // Periodic sync poll every 30s when online
        setInterval(() => {
            if (navigator.onLine && !isSyncing) {
                pushPendingQueue(false);
            }
            updateNetworkStatusUI();
        }, 30000);
    }

    return {
        init,
        updateNetworkStatusUI,
        pullMasterData,
        precacheOperatorPages,
        pushPendingQueue,
        saveReportFromForm,
        compressImage
    };
})();

// Auto initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    SimoliSync.init();
});

window.SimoliSync = SimoliSync;
