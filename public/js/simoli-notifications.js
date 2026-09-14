/**
 * SIMOLI PWA Notification & Smart Reminder Engine (v1.0)
 * Handles PWA Push Notifications, Shift Input Reminders, Offline & Online Input Alerts
 */
const SimoliNotify = (() => {
    const STORAGE_KEY_LAST_REMINDER = 'simoli_last_reminder_ts';
    const STORAGE_KEY_REMINDER_DATE = 'simoli_last_reminder_date';
    const COOLDOWN_MINUTES = 60; // Max 1 OS push reminder per 60 mins unless urgent

    let isInitialized = false;
    let cachedStatus = null;

    // --- 1. Sound Synthesizer (Web Audio API - No External Assets Needed) ---
    function playChime(type = 'success') {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'warning' || type === 'reminder') {
                // Two-tone alert chime (D5 -> A5)
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.5);
            } else {
                // Sweet 3-tone success chime (C5 -> E5 -> G5)
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(523.25, ctx.currentTime);
                osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.1);
                osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.2);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.45);
            }
        } catch (e) {
            // Audio Context not allowed before gesture or not supported
        }
    }

    // --- 2. Haptic Vibration ---
    function vibrateDevice(pattern = [100, 50, 100]) {
        if ('vibrate' in navigator) {
            try {
                navigator.vibrate(pattern);
            } catch (e) {}
        }
    }

    // --- 3. Permission Management ---
    function getPermission() {
        if (!('Notification' in window)) return 'unsupported';
        return Notification.permission; // 'default', 'granted', 'denied'
    }

    async function requestPermission() {
        if (!('Notification' in window)) {
            if (window.showToastNotification) {
                showToastNotification('Perangkat/Browser ini tidak mendukung Web Notifications.', 'warning');
            }
            return 'unsupported';
        }

        try {
            const permission = await Notification.requestPermission();
            updateNotificationUI();

            if (permission === 'granted') {
                playChime('success');
                vibrateDevice([150]);
                send({
                    title: '🔔 Notifikasi PWA SIMOLI Aktif',
                    body: 'Notifikasi sistem dan pengingat laporan kerja berhasil diaktifkan.',
                    type: 'success',
                    tag: 'simoli-perm-activated',
                    url: '/operator'
                });
                if (window.showToastNotification) {
                    showToastNotification('✓ Izin notifikasi berhasil diaktifkan!', 'success');
                }
            } else if (permission === 'denied') {
                if (window.showToastNotification) {
                    showToastNotification('Izin notifikasi ditolak. Aktifkan pada pengaturan browser jika ingin menerima pengingat.', 'warning');
                }
            }

            return permission;
        } catch (err) {
            console.warn('[SimoliNotify] Permission request error:', err);
            return getPermission();
        }
    }

    // --- 4. Send Browser / Service Worker Notification ---
    async function send(options) {
        const {
            title = 'SIMOLI Monitoring',
            body = '',
            icon = '/logo/Icon%20SIMOLI.png',
            badge = '/logo/Icon%20SIMOLI.png',
            url = '/operator',
            type = 'info',
            tag = 'simoli-notice-' + Date.now(),
            requireInteraction = false
        } = options;

        playChime(type);
        vibrateDevice(type === 'warning' || type === 'reminder' ? [200, 100, 200] : [100, 50, 100]);

        const notifOptions = {
            body: body,
            icon: icon,
            badge: badge,
            tag: tag,
            renotify: true,
            requireInteraction: requireInteraction,
            vibrate: [200, 100, 200],
            data: {
                url: url,
                timestamp: Date.now()
            }
        };

        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                if ('serviceWorker' in navigator) {
                    const registration = await navigator.serviceWorker.ready;
                    if (registration && registration.showNotification) {
                        await registration.showNotification(title, notifOptions);
                        return true;
                    }
                }
                new Notification(title, notifOptions);
                return true;
            } catch (err) {
                console.warn('[SimoliNotify] showNotification fallback:', err);
            }
        }

        // In-App Toast Fallback if Notification permission is not granted
        if (window.showToastNotification) {
            const toastType = type === 'warning' || type === 'reminder' ? 'warning' : 'success';
            showToastNotification(`<strong>${title}</strong><br>${body}`, toastType);
        }

        return false;
    }

    // --- 5. EVENT 1: Saat Operator Menginput Data ---
    function notifyInputSuccess(data = {}) {
        const operatorName = data.operator || 'Operator Lapangan';
        const alatName = data.alat || data.alat_berat_kode || 'Unit Alat Berat';
        const tgl = data.tanggal || new Date().toLocaleDateString('id-ID');
        const isOffline = data.is_offline || !navigator.onLine;

        const title = isOffline 
            ? '💾 Laporan Disimpan di HP (Offline)' 
            : '📝 Laporan Kerja Berhasil Diinput';

        const body = isOffline
            ? `Laporan kerja ${operatorName} (${alatName}) tersimpan aman di memori HP. Otomatis disinkronkan ke server saat ada sinyal.`
            : `Laporan kerja ${operatorName} untuk unit ${alatName} tanggal ${tgl} berhasil dicatat ke sistem SIMOLI.`;

        send({
            title: title,
            body: body,
            type: 'success',
            tag: 'simoli-report-saved',
            url: '/operator'
        });

        // Re-check input status after 2 seconds to refresh banner
        setTimeout(() => checkAndNotifyMissingInput(true), 2000);
    }

    // --- 6. EVENT 1b: Saat Data Offline Berhasil Sinkron ---
    function notifySyncSuccess(syncedCount = 1) {
        send({
            title: '🚀 Sinkronisasi Server Berhasil',
            body: `Sebanyak ${syncedCount} laporan kerja offline dari HP Anda telah berhasil disinkronkan ke server database SIMOLI!`,
            type: 'success',
            tag: 'simoli-sync-success',
            url: '/operator'
        });

        setTimeout(() => checkAndNotifyMissingInput(true), 1500);
    }

    // --- 7. EVENT 2: Saat Operator Tidak Menginputkan Data (Reminder / Pengingat) ---
    async function checkAndNotifyMissingInput(force = false) {
        try {
            const res = await fetch('/operator/check-today-input', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return null;

            const data = await res.json();
            if (!data || !data.success) return null;

            cachedStatus = data;
            renderReminderBanner(data);
            dispatchStatusEvent(data);

            const now = Date.now();
            const todayStr = data.today_date;
            const lastReminderTs = parseInt(localStorage.getItem(STORAGE_KEY_LAST_REMINDER) || '0');
            const lastReminderDate = localStorage.getItem(STORAGE_KEY_REMINDER_DATE) || '';
            const minutesSinceLast = (now - lastReminderTs) / (1000 * 60);

            // Kondisi A: Operator BELUM menginput data hari ini
            if (!data.has_input_today) {
                const isNewDay = lastReminderDate !== todayStr;
                const isCooldownPassed = minutesSinceLast >= COOLDOWN_MINUTES;

                if (force || isNewDay || isCooldownPassed || data.urgency === 'critical') {
                    let reminderTitle = '⚠️ Pengingat: Belum Input Laporan Kerja Hari Ini';
                    let reminderBody = `Halo ${data.operator_name || 'Operator'}, Anda belum menginput data operasional alat berat hari ini (${data.today_formatted}). Segera input data pekerjaan!`;

                    if (data.current_hour >= 16) {
                        reminderTitle = '🚨 Peringatan Akhir Shift: Belum Ada Input Hari Ini!';
                        reminderBody = `Shift kerja hampir selesai namun belum ada laporan kerja tercatat hari ini (${data.today_formatted}). Klik untuk input sekarang.`;
                    } else if (data.current_hour >= 12) {
                        reminderTitle = '⏰ Pengingat Siang: Laporan Kerja Hari Ini Belum Diisi';
                        reminderBody = `Jangan lupa mencatat jam kerja (HM) dan aplikasi bed alat berat untuk hari ini (${data.today_formatted}).`;
                    }

                    send({
                        title: reminderTitle,
                        body: reminderBody,
                        type: 'reminder',
                        tag: 'simoli-missing-input-' + todayStr,
                        url: '/operator/create',
                        requireInteraction: data.urgency === 'critical'
                    });

                    localStorage.setItem(STORAGE_KEY_LAST_REMINDER, now.toString());
                    localStorage.setItem(STORAGE_KEY_REMINDER_DATE, todayStr);
                }
            } 
            // Kondisi B: Operator SUDAH input tetapi ada shift belum diselesaikan (belum ada foto sesudah/HM akhir)
            else if (data.uncompleted_shifts > 0 && data.current_hour >= 14) {
                const isCooldownPassed = minutesSinceLast >= COOLDOWN_MINUTES;
                if (force || isCooldownPassed) {
                    send({
                        title: '⏱️ Pengingat: Selesaikan Shift Kerja Anda',
                        body: `Terdapat ${data.uncompleted_shifts} laporan kerja yang belum ditutup (Foto sesudah & HM akhir). Lengkapi sebelum meninggalkan lokasi kerja.`,
                        type: 'warning',
                        tag: 'simoli-uncompleted-shift-' + todayStr,
                        url: '/operator'
                    });
                    localStorage.setItem(STORAGE_KEY_LAST_REMINDER, now.toString());
                }
            }

            return data;
        } catch (err) {
            console.warn('[SimoliNotify] Check today input error:', err);
            return null;
        }
    }

    // --- 8. In-App Reminder Banner Renderer ---
    function renderReminderBanner(data) {
        const bannerContainer = document.getElementById('simoli-reminder-banner-slot');
        if (!bannerContainer || !data) return;

        if (!data.has_input_today) {
            const urgencyBg = data.current_hour >= 16 ? '#FEF2F2' : (data.current_hour >= 12 ? '#FFFBEB' : '#EFF6FF');
            const borderCol = data.current_hour >= 16 ? '#EF4444' : (data.current_hour >= 12 ? '#F59E0B' : '#0F52BA');
            const iconCol = data.current_hour >= 16 ? 'text-danger' : (data.current_hour >= 12 ? 'text-warning' : 'text-primary');

            bannerContainer.innerHTML = `
                <div class="op-card mb-3 p-3 shadow-sm border-0" style="background: ${urgencyBg}; border-left: 4px solid ${borderCol} !important;">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="feather-alert-triangle fs-3 ${iconCol} flex-shrink-0 mt-0.5"></i>
                            <div>
                                <div class="fw-bold fs-14 text-dark mb-0.5">
                                    ${data.current_hour >= 16 ? 'Peringatan: Laporan Kerja Hari Ini Belum Diisi!' : 'Pengingat Shift: Belum Ada Input Hari Ini'}
                                </div>
                                <div class="text-muted fs-12 mb-2">
                                    Halo <strong>${data.operator_name}</strong>, Anda belum mencatat data operasional alat berat untuk hari ini (<strong>${data.today_formatted}</strong>). Silakan input laporan kerja Anda.
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="/operator/create" class="btn btn-sm btn-op-primary px-3 py-1.5 fs-12 fw-bold rounded-pill" style="width: auto;">
                                        <i class="feather-plus-circle me-1"></i>Input Laporan Sekarang
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1.5 fs-11 rounded-pill" onclick="SimoliNotify.testNotification()" title="Uji Notifikasi PWA">
                                        <i class="feather-bell me-1"></i>Uji Notifikasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else if (data.uncompleted_shifts > 0) {
            bannerContainer.innerHTML = `
                <div class="op-card mb-3 p-3 shadow-sm border-0" style="background: #FFFBEB; border-left: 4px solid #F59E0B !important;">
                    <div class="d-flex align-items-start gap-2.5">
                        <i class="feather-clock fs-3 text-warning flex-shrink-0 mt-0.5"></i>
                        <div>
                            <div class="fw-bold fs-14 text-dark mb-0.5">Shift Sedang Berjalan (${data.uncompleted_shifts} Belum Selesai)</div>
                            <div class="text-muted fs-12 mb-2">
                                Anda memiliki laporan kerja yang belum ditutup (Foto sesudah & HM akhir belum lengkap). Harap selesaikan sebelum shift berakhir.
                            </div>
                            <a href="#recent-logs" class="btn btn-sm btn-warning text-dark px-3 py-1 fs-12 fw-bold rounded-pill border-0 shadow-sm">
                                <i class="feather-edit-2 me-1"></i>Lihat & Selesaikan Shift
                            </a>
                        </div>
                    </div>
                </div>
            `;
        } else {
            bannerContainer.innerHTML = `
                <div class="op-card mb-3 p-3 shadow-sm border-0" style="background: #F0FDF4; border-left: 4px solid #10B981 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="feather-check-circle fs-3 text-success flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold fs-13 text-dark">Laporan Hari Ini Telah Terisi (${data.total_today} Laporan)</div>
                                <div class="text-muted fs-11">Data operasional kerja hari ini (${data.today_formatted}) sudah tercatat dengan lengkap.</div>
                            </div>
                        </div>
                        <span class="badge bg-success rounded-pill px-2.5 py-1 fs-11 fw-bold">✓ Lengkap</span>
                    </div>
                </div>
            `;
        }

        if (window.feather) feather.replace();
    }

    // --- 9. UI Status Updater ---
    function updateNotificationUI() {
        const btn = document.getElementById('simoli-notif-toggle-btn');
        const badge = document.getElementById('simoli-notif-status-badge');
        const perm = getPermission();

        if (btn) {
            if (perm === 'granted') {
                btn.classList.remove('btn-outline-warning', 'btn-outline-danger');
                btn.classList.add('btn-outline-success');
                btn.innerHTML = '<i class="feather-bell"></i><span class="d-none d-sm-inline ms-1 fs-11">Notif Aktif</span>';
            } else if (perm === 'denied') {
                btn.classList.remove('btn-outline-success', 'btn-outline-warning');
                btn.classList.add('btn-outline-danger');
                btn.innerHTML = '<i class="feather-bell-off"></i><span class="d-none d-sm-inline ms-1 fs-11">Notif Ditolak</span>';
            } else {
                btn.classList.remove('btn-outline-success', 'btn-outline-danger');
                btn.classList.add('btn-outline-warning');
                btn.innerHTML = '<i class="feather-bell"></i><span class="d-none d-sm-inline ms-1 fs-11">Aktifkan Notif</span>';
            }
        }

        if (badge) {
            if (perm === 'granted') {
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fs-11';
                badge.innerHTML = '<i class="feather-check me-1"></i>Notifikasi PWA Aktif';
            } else if (perm === 'denied') {
                badge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 fs-11';
                badge.innerHTML = '<i class="feather-x me-1"></i>Izin Notifikasi Ditolak';
            } else {
                badge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5 fs-11';
                badge.innerHTML = '<i class="feather-alert-circle me-1"></i>Izin Belum Diaktifkan';
            }
        }

        if (window.feather) feather.replace();
    }

    function dispatchStatusEvent(data) {
        try {
            window.dispatchEvent(new CustomEvent('simoli:input-status-updated', { detail: data }));
        } catch (e) {}
    }

    // --- 10. Interactive Test Notification ---
    function testNotification() {
        if (getPermission() !== 'granted') {
            requestPermission().then((p) => {
                if (p === 'granted') {
                    sendSampleNotification();
                }
            });
        } else {
            sendSampleNotification();
        }
    }

    function sendSampleNotification() {
        send({
            title: '🔔 Uji Coba Notifikasi PWA SIMOLI',
            body: 'Notifikasi berhasil terhubung! Anda akan menerima konfirmasi saat input data dan pengingat saat belum input laporan.',
            type: 'reminder',
            tag: 'simoli-test-' + Date.now(),
            url: '/operator'
        });
        if (window.showToastNotification) {
            showToastNotification('🔔 Notifikasi percobaan berhasil dikirim ke perangkat Anda!', 'info');
        }
    }

    // --- 11. Initializer ---
    function init() {
        if (isInitialized) return;
        isInitialized = true;

        updateNotificationUI();

        // Check today's input status
        checkAndNotifyMissingInput();

        // Periodic check every 15 minutes
        setInterval(() => {
            if (navigator.onLine) {
                checkAndNotifyMissingInput();
            }
        }, 15 * 60 * 1000);

        // Re-check when window regains focus or comes back online
        window.addEventListener('focus', () => {
            checkAndNotifyMissingInput();
        });

        window.addEventListener('online', () => {
            checkAndNotifyMissingInput();
        });
    }

    return {
        init,
        getPermission,
        requestPermission,
        send,
        playChime,
        vibrateDevice,
        notifyInputSuccess,
        notifySyncSuccess,
        checkAndNotifyMissingInput,
        testNotification,
        updateNotificationUI,
        getCachedStatus: () => cachedStatus
    };
})();

// Auto-initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    SimoliNotify.init();
});

window.SimoliNotify = SimoliNotify;
