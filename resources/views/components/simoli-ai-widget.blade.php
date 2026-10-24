<!-- ============================================================
     SISIL — SIMOLI Smart Intelligence Assistant (Ollama DeepSeek)
     PTPN IV Regional III — Sistem Pemantauan Limbah & Land Application
     ============================================================ -->
<div id="simoli-sisil-root">
    <!-- Floating Action Button (FAB) with "Tanya SISIL" Pill -->
    <div class="sisil-fab-container">
        <div class="sisil-fab-pill" onclick="window.toggleSisilChat()" role="button" aria-label="Buka Tanya SISIL">
            <span class="sisil-pill-sparkle">✨</span>
            <span class="sisil-pill-text">Tanya SISIL</span>
        </div>
        <button type="button" id="sisilFabBtn" class="sisil-fab-btn" onclick="window.toggleSisilChat()" aria-label="Buka SISIL AI Assistant" title="Buka SISIL (Alt + A)">
            <img src="{{ asset('img/ai/MaskotAIBerhijabSIMOLI.png') }}" alt="SISIL Avatar" class="sisil-fab-avatar">
            <span class="sisil-online-dot"></span>
        </button>
    </div>

    <!-- Right-click Context Menu for FAB Container -->
    <div id="sisilContextMenu" class="sisil-context-menu" style="display:none;" role="menu" aria-label="Menu Opsi SISIL">
        <div class="sisil-ctx-header">
            <span class="sisil-ctx-dot"></span>
            <span class="sisil-ctx-title">SISIL Assistant</span>
        </div>
        <button type="button" id="sisilHideFabBtn" class="sisil-ctx-item" role="menuitem" title="Sembunyikan tombol hingga halaman di-refresh">
            <i class="feather-eye-off"></i>
            <span>Sembunyikan (Hide)</span>
        </button>
    </div>

    <!-- Backdrop Overlay -->
    <div id="sisilBackdrop" class="sisil-drawer-backdrop" onclick="window.closeSisilChat()"></div>

    <!-- Sliding Drawer Panel (Style Mirip Tampilan AI Lama SIMOLI) -->
    <aside id="sisilDrawer" class="sisil-drawer-panel" role="dialog" aria-modal="true" aria-label="SISIL — SIMOLI Smart Intelligence Assistant">
        <!-- Drawer Header -->
        <div class="sisil-drawer-header">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="sisil-hdr-avatar-wrap">
                    <img src="{{ asset('img/ai/MaskotAIBerhijabSIMOLI.png') }}" alt="SISIL" class="sisil-hdr-avatar">
                    <span class="sisil-hdr-pulse-dot"></span>
                </div>
                <div class="min-w-0">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-white sisil-hdr-title mb-0 text-truncate">SISIL</h5>
                        <span class="sisil-badge-tag">AI Assistant</span>
                    </div>
                    <div class="sisil-hdr-subtitle text-truncate">SIMOLI Smart Intelligence Assistant</div>
                    <div class="d-flex align-items-center gap-1 sisil-hdr-status">
                        <span class="sisil-live-indicator"></span>
                        <span>Ollama Local Engine &bull; PTPN IV Regional III</span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" id="sisilClearChatBtn" class="sisil-icon-btn" title="Bersihkan Riwayat Percakapan" aria-label="Bersihkan Riwayat">
                    <i class="feather-trash-2"></i>
                </button>
                <button type="button" id="sisilCloseBtn" class="sisil-icon-btn" onclick="window.closeSisilChat()" title="Tutup Drawer (Esc)" aria-label="Tutup">
                    <i class="feather-x"></i>
                </button>
            </div>
        </div>

        <!-- Quick Prompts Bar (Chips) -->
        <div class="sisil-quick-bar">
            <button type="button" class="sisil-prompt-chip" onclick="window.sendSimoliAiPrompt('pks mana saja yang belum input data pengaliran, pemeliharaan, dan alat berat hari ini?')">
                🚨 Belum Input Hari Ini
            </button>
            <button type="button" class="sisil-prompt-chip" onclick="window.sendSimoliAiPrompt('berapa total volume limbah dialirkan dan dihasilkan bulan ini?')">
                💧 Rekap Pengaliran
            </button>
            <button type="button" class="sisil-prompt-chip" onclick="window.sendSimoliAiPrompt('data rkp tahun ' + new Date().getFullYear())">
                🎯 Target RKP Bed
            </button>
            <button type="button" class="sisil-prompt-chip" onclick="window.sendSimoliAiPrompt('tampilkan ringkasan pemeliharaan kolam dan bed bulan ini')">
                🛠️ Pemeliharaan
            </button>
            <button type="button" class="sisil-prompt-chip" onclick="window.sendSimoliAiPrompt('bagaimana status ketersediaan dan jam kerja alat berat?')">
                🚜 Status Alat Berat
            </button>
            <button type="button" class="sisil-prompt-chip" onclick="window.sendSimoliAiPrompt('berikan ringkasan eksekutif kepatuhan seluruh unit pks')">
                📊 Briefing Eksekutif
            </button>
        </div>

        <!-- Chat Messages Body -->
        <div id="sisilChatBody" class="sisil-chat-body">
            <!-- Initial Welcome Message from SISIL -->
            <div class="sisil-msg-item sisil-msg-bot">
                <div class="sisil-msg-avatar-wrap">
                    <img src="{{ asset('img/ai/MaskotAIBerhijabSIMOLI.png') }}" alt="SISIL" class="sisil-msg-avatar">
                </div>
                <div class="sisil-bubble-wrap">
                    <div class="sisil-bubble sisil-bubble-bot">
                        <p class="mb-1 fw-bold text-success" style="font-size: 13.5px;">
                            👋 Halo! Saya <strong>SISIL</strong> (<em>SIMOLI Smart Intelligence Assistant</em>).
                        </p>
                        <p class="mb-2 text-muted" style="font-size: 12.5px; line-height: 1.55;">
                            Saya asisten virtual cerdas yang siap membantu Anda memantau kepatuhan pelaporan harian, menganalisis data pengaliran limbah LA, target RKP, pemeliharaan kolam/bed, dan operasional alat berat pada 12 unit PKS PTPN IV Regional III.
                        </p>
                        <div class="sisil-intro-guide">
                            <strong class="d-block mb-1 text-dark" style="font-size: 11.5px;">💡 Contoh pertanyaan yang bisa Anda ajukan:</strong>
                            <ul class="mb-0 ps-3" style="font-size: 11.5px; color: #475569;">
                                <li>"PKS mana saja yang belum input data hari ini?"</li>
                                <li>"Rekap data pengaliran tahun 2025 bulan oktober"</li>
                                <li>"Data RKP tahun 2026"</li>
                                <li>"Rekap pemeliharaan flat bed bulan ini"</li>
                            </ul>
                        </div>
                    </div>
                    <span class="sisil-msg-time">{{ date('H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Drawer Footer / Input Bar -->
        <div class="sisil-drawer-footer">
            <form id="sisilChatForm" class="d-flex align-items-center gap-2 m-0 w-100" onsubmit="event.preventDefault(); window.handleSisilSubmit();">
                <div class="sisil-input-wrap">
                    <input type="text" id="sisilQueryInput" class="form-control sisil-chat-input"
                           placeholder="Tanya SISIL tentang data SIMOLI (maks 500 karakter)..."
                           autocomplete="off" required maxlength="500">
                    <span id="sisilCharCounter" class="sisil-char-counter" title="Batas maksimal 500 karakter">0/500</span>
                </div>
                <button type="submit" id="sisilSendBtn" class="btn sisil-send-btn" aria-label="Kirim Pesan" title="Kirim Pesan">
                    <i class="feather-send"></i>
                </button>
            </form>
            <div class="sisil-footer-hint">
                <span>SISIL Engine (Ultra-Ringan)</span> &bull; <span>Batas 500 Karakter</span> &bull; <span><kbd>Alt + A</kbd> toggle</span>
            </div>
        </div>
    </aside>
</div>

<!-- Dependencies: Markdown parser & Highlight.js for Code Blocks & SheetJS for Excel Export -->
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>

<style>
/* ================================================================
   SISIL — SIMOLI SMART INTELLIGENCE ASSISTANT STYLING
   ================================================================ */
.sisil-fab-container {
    position: fixed;
    bottom: 90px;
    right: 24px;
    z-index: 10400;
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.sisil-fab-pill {
    background: linear-gradient(135deg, var(--theme-hero-from, #030d07) 0%, var(--theme-hero-mid, #0a2317) 35%, var(--theme-hero-to, #0e3b26) 65%, var(--theme-hero-accent, #16a34a) 100%);
    color: #ffffff;
    font-weight: 800;
    font-size: 13px;
    padding: 8px 16px;
    border-radius: 24px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.12), 0 0 0 1.5px rgba(22, 163, 74, 0.2);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    white-space: nowrap;
}

/* .sisil-fab-pill:hover {
    background: linear-gradient(135deg, #16a34a, #0d9488);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(22, 163, 74, 0.35);
} */

.sisil-pill-sparkle {
    font-size: 14px;
}

.sisil-fab-btn {
    width: 156px;
    height: 156px;
    border-radius: 50%;
    background: #ffffff;
    border: 2.5px solid #22c55e;
    box-shadow: 0 8px 24px rgba(22, 163, 74, 0.35), 0 0 15px rgba(34, 197, 94, 0.3);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 0;
    overflow: visible;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.sisil-fab-btn:hover {
    transform: translateY(-3px) scale(1.08);
    box-shadow: 0 12px 30px rgba(22, 163, 74, 0.55), 0 0 22px rgba(34, 197, 94, 0.55);
}

.sisil-fab-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 20%;
    border-radius: 50%;
    display: block;
    background: #ffffff;
}

.sisil-online-dot {
    position: absolute;
    top: 0px;
    right: 0px;
    width: 13px;
    height: 13px;
    background: #22c55e;
    border: 2px solid #ffffff;
    border-radius: 50%;
    box-shadow: 0 0 6px #22c55e;
}

/* Right-click Context Menu */
.sisil-context-menu {
    position: fixed;
    z-index: 10600;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.18), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
    min-width: 195px;
    padding: 6px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    animation: sisilCtxFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
}

@keyframes sisilCtxFadeIn {
    from {
        opacity: 0;
        transform: scale(0.92);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.sisil-ctx-header {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px 6px;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 4px;
}

.sisil-ctx-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    box-shadow: 0 0 5px #22c55e;
}

.sisil-ctx-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    text-align: left;
    transition: background 0.15s, color 0.15s;
}

.sisil-ctx-item:hover {
    background: #fee2e2;
    color: #dc2626;
}

.sisil-ctx-item i {
    font-size: 14px;
}

html.app-skin-dark .sisil-context-menu {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.55);
}

html.app-skin-dark .sisil-ctx-header {
    color: #94a3b8;
    border-bottom-color: #334155;
}

html.app-skin-dark .sisil-ctx-item {
    color: #e2e8f0;
}

html.app-skin-dark .sisil-ctx-item:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}

/* Backdrop */
.sisil-drawer-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 10500;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.sisil-drawer-backdrop.active {
    opacity: 1;
    visibility: visible;
}

/* Drawer Panel (Right Slide-over) */
.sisil-drawer-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 460px;
    max-width: 100vw;
    height: 100vh;
    background: #ffffff;
    z-index: 10550;
    box-shadow: -10px 0 35px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.sisil-drawer-panel.active {
    transform: translateX(0);
}

/* Header */
.sisil-drawer-header {
    background: linear-gradient(135deg, #15803d, #0f766e);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    color: #ffffff;
}

.sisil-hdr-avatar-wrap {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    position: relative;
    border: 2px solid rgba(255, 255, 255, 0.85);
    box-shadow: 0 0 12px rgba(74, 222, 128, 0.45);
    flex-shrink: 0;
    background: #ffffff;
}

.sisil-hdr-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    object-position: center 20%;
    display: block;
    background: #ffffff;
}

.sisil-hdr-pulse-dot {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 11px;
    height: 11px;
    background: #4ade80;
    border: 2px solid #ffffff;
    border-radius: 50%;
}

.sisil-hdr-title {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.3px;
}

.sisil-badge-tag {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.sisil-hdr-subtitle {
    font-size: 11.5px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
}

.sisil-hdr-status {
    font-size: 10.5px;
    color: rgba(255, 255, 255, 0.75);
    margin-top: 2px;
}

.sisil-live-indicator {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 5px #4ade80;
}

.sisil-icon-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.85);
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sisil-icon-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* Quick Bar Chips */
.sisil-quick-bar {
    padding: 10px 16px;
    display: flex;
    gap: 7px;
    overflow-x: auto;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
    scrollbar-width: none;
}

.sisil-quick-bar::-webkit-scrollbar {
    display: none;
}

.sisil-prompt-chip {
    white-space: nowrap;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}

.sisil-prompt-chip:hover {
    background: #16a34a;
    color: #ffffff;
    border-color: #16a34a;
    transform: translateY(-1px);
}

/* Chat Body */
.sisil-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #f8fafc;
    scroll-behavior: smooth;
}

.sisil-msg-item {
    display: flex;
    gap: 10px;
    max-width: 92%;
}

.sisil-msg-user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.sisil-msg-bot {
    align-self: flex-start;
}

.sisil-msg-avatar-wrap {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    flex-shrink: 0;
    overflow: hidden;
    border: 1.5px solid rgba(22, 163, 74, 0.35);
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.sisil-msg-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 20%;
    display: block;
    background: #ffffff;
}

.sisil-msg-user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #059669);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
}

.sisil-bubble-wrap {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.sisil-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.6;
    word-break: break-word;
}

.sisil-msg-user .sisil-bubble {
    background: #16a34a;
    color: #ffffff;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.25);
}

.sisil-msg-bot .sisil-bubble {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
}

.sisil-bubble p {
    margin-bottom: 8px;
}

.sisil-bubble p:last-child {
    margin-bottom: 0;
}

.sisil-bubble pre {
    background: #0f172a;
    color: #f8fafc;
    padding: 10px 14px;
    border-radius: 8px;
    overflow-x: auto;
    font-size: 11.5px;
    margin: 8px 0;
}

/* Responsive Table Container & Styling */
.sisil-table-wrap {
    width: 100%;
    overflow-x: auto;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.sisil-bubble table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
    margin: 0;
}

.sisil-bubble table th,
.sisil-bubble table td {
    border: 1px solid #e2e8f0;
    padding: 6px 10px;
    white-space: nowrap;
}

.sisil-bubble table th {
    background: #f8fafc;
    color: #1e293b;
    font-weight: 700;
}

.sisil-bubble table tbody tr:hover {
    background: #f1f5f9;
}

/* Excel Export Card */
.sisil-excel-card {
    margin-top: 12px;
    padding: 10px 12px;
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.08);
}

.sisil-excel-info {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.sisil-excel-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #bbf7d0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.sisil-excel-title {
    font-size: 12px;
    font-weight: 700;
    color: #15803d;
    line-height: 1.3;
}

.sisil-excel-desc {
    font-size: 10.5px;
    color: #475569;
    line-height: 1.3;
}

.sisil-excel-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    background: #16a34a;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
    flex-shrink: 0;
}

.sisil-excel-btn:hover {
    background: #15803d;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.4);
}

.sisil-excel-btn:active {
    transform: translateY(0);
}

.sisil-excel-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Dark Mode support for Table & Excel Card */
html.app-skin-dark .sisil-table-wrap {
    border-color: #334155;
    background: #0f172a;
}

html.app-skin-dark .sisil-bubble table th,
html.app-skin-dark .sisil-bubble table td {
    border-color: #334155;
}

html.app-skin-dark .sisil-bubble table th {
    background: #1e293b;
    color: #f1f5f9;
}

html.app-skin-dark .sisil-bubble table tbody tr:hover {
    background: #1e293b;
}

html.app-skin-dark .sisil-excel-card {
    background: rgba(22, 163, 74, 0.12);
    border-color: rgba(34, 197, 94, 0.35);
}

html.app-skin-dark .sisil-excel-icon {
    background: #0f172a;
    border-color: rgba(34, 197, 94, 0.3);
}

html.app-skin-dark .sisil-excel-title {
    color: #4ade80;
}

html.app-skin-dark .sisil-excel-desc {
    color: #94a3b8;
}

.sisil-intro-guide {
    background: rgba(22, 163, 74, 0.05);
    border: 1px solid rgba(22, 163, 74, 0.15);
    border-radius: 10px;
    padding: 10px 12px;
    margin-top: 8px;
}

.sisil-msg-time {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 4px;
    padding: 0 4px;
}

.sisil-msg-user .sisil-msg-time {
    text-align: right;
}

/* Typing Indicator */
.sisil-typing-box {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 12px;
    font-style: italic;
    padding: 4px 0;
}

.sisil-typing-dots {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.sisil-typing-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #16a34a;
    animation: sisilDotBounce 1.4s infinite ease-in-out both;
}

.sisil-typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.sisil-typing-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes sisilDotBounce {
    0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
    40% { transform: scale(1); opacity: 1; }
}

/* Footer & Input */
.sisil-drawer-footer {
    padding: 14px 18px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.sisil-input-wrap {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
}

.sisil-chat-input {
    border-radius: 24px;
    font-size: 13px;
    padding: 9px 58px 9px 18px;
    border: 1.5px solid rgba(22, 163, 74, 0.25);
    width: 100%;
}

.sisil-chat-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
}

.sisil-char-counter {
    position: absolute;
    right: 12px;
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    background: #f1f5f9;
    padding: 2px 7px;
    border-radius: 12px;
    pointer-events: none;
    transition: all 0.2s;
    user-select: none;
    border: 1px solid #e2e8f0;
}

.sisil-char-counter.sisil-counter-warning {
    color: #d97706;
    background: #fef3c7;
    border-color: #fde68a;
}

.sisil-char-counter.sisil-counter-limit {
    color: #dc2626;
    background: #fee2e2;
    border-color: #fca5a5;
    animation: sisilShake 0.25s ease-in-out;
}

@keyframes sisilShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px); }
    75% { transform: translateX(3px); }
}

.sisil-send-btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    padding: 0;
    background: #16a34a;
    color: #ffffff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s;
}

.sisil-send-btn:hover {
    background: #15803d;
    transform: scale(1.05);
}

.sisil-send-btn:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
}

.sisil-footer-hint {
    text-align: center;
    color: #94a3b8;
    font-size: 10.5px;
    margin-top: 8px;
}

.sisil-footer-hint kbd {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 1px 5px;
    font-size: 9.5px;
}

/* Mobile responsive */
@media (max-width: 575.98px) {
    .sisil-fab-container {
        bottom: 20px;
        right: 16px;
    }
    .sisil-drawer-panel {
        width: 100vw;
    }
}
</style>

<script>
(function() {
    const STORAGE_KEY = 'simoli_sisil_chat_history_v2';
    const MAX_PROMPT_CHARS = 500;
    const backdrop = document.getElementById('sisilBackdrop');
    const drawer = document.getElementById('sisilDrawer');
    const input = document.getElementById('sisilQueryInput');
    const sendBtn = document.getElementById('sisilSendBtn');
    const charCounter = document.getElementById('sisilCharCounter');
    const body = document.getElementById('sisilChatBody');
    const clearBtn = document.getElementById('sisilClearChatBtn');
    const fabContainer = document.querySelector('.sisil-fab-container');
    const contextMenu = document.getElementById('sisilContextMenu');
    const hideFabBtn = document.getElementById('sisilHideFabBtn');

    // Context Menu (Right Click on FAB) -> Hide temporary until refresh
    function hideContextMenu() {
        if (contextMenu) {
            contextMenu.style.display = 'none';
        }
    }

    if (fabContainer && contextMenu) {
        fabContainer.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            e.stopPropagation();

            contextMenu.style.display = 'block';

            const menuWidth = contextMenu.offsetWidth || 195;
            const menuHeight = contextMenu.offsetHeight || 75;
            const winWidth = window.innerWidth;
            const winHeight = window.innerHeight;

            let posX = e.clientX;
            let posY = e.clientY;

            if (posX + menuWidth > winWidth - 12) {
                posX = winWidth - menuWidth - 12;
            }
            if (posY + menuHeight > winHeight - 12) {
                posY = posY - menuHeight - 8;
            }

            contextMenu.style.left = `${posX}px`;
            contextMenu.style.top = `${posY}px`;
        });

        if (hideFabBtn) {
            hideFabBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                hideContextMenu();
                fabContainer.style.transition = 'opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1)';
                fabContainer.style.opacity = '0';
                fabContainer.style.transform = 'scale(0.7)';
                fabContainer.style.pointerEvents = 'none';
                setTimeout(() => {
                    fabContainer.style.display = 'none';
                }, 250);
            });
        }

        document.addEventListener('click', (e) => {
            if (contextMenu && !contextMenu.contains(e.target)) {
                hideContextMenu();
            }
        });

        document.addEventListener('contextmenu', (e) => {
            if (contextMenu && !fabContainer.contains(e.target) && !contextMenu.contains(e.target)) {
                hideContextMenu();
            }
        });

        window.addEventListener('resize', hideContextMenu);
        window.addEventListener('scroll', hideContextMenu, true);
    }

    // Real-time Character Counter & Hard Limit Blocker
    function updateCharCounter() {
        if (!input || !charCounter) return;
        if (input.value.length > MAX_PROMPT_CHARS) {
            input.value = input.value.slice(0, MAX_PROMPT_CHARS);
        }
        const len = input.value.length;
        charCounter.textContent = `${len}/${MAX_PROMPT_CHARS}`;

        if (len >= MAX_PROMPT_CHARS) {
            charCounter.classList.add('sisil-counter-limit');
            charCounter.classList.remove('sisil-counter-warning');
        } else if (len >= MAX_PROMPT_CHARS * 0.8) {
            charCounter.classList.add('sisil-counter-warning');
            charCounter.classList.remove('sisil-counter-limit');
        } else {
            charCounter.classList.remove('sisil-counter-warning', 'sisil-counter-limit');
        }
    }

    if (input) {
        input.addEventListener('input', updateCharCounter);
        input.addEventListener('paste', () => {
            setTimeout(updateCharCounter, 0);
        });
        input.addEventListener('keydown', (e) => {
            // Mencegah pengetikan jika sudah mencapai batas maksimal 500 karakter
            if (input.value.length >= MAX_PROMPT_CHARS && 
                e.key !== 'Backspace' && 
                e.key !== 'Delete' && 
                e.key !== 'ArrowLeft' && 
                e.key !== 'ArrowRight' && 
                e.key !== 'ArrowUp' && 
                e.key !== 'ArrowDown' && 
                !e.ctrlKey && 
                !e.metaKey && 
                e.key !== 'Enter') {
                e.preventDefault();
                updateCharCounter();
            }
        });
    }

    // Global Drawer Controls
    window.openSisilChat = function(initialQuery) {
        if (backdrop && drawer) {
            backdrop.classList.add('active');
            drawer.classList.add('active');
            if (input) {
                setTimeout(() => input.focus(), 250);
            }
            if (initialQuery) {
                window.sendSimoliAiPrompt(initialQuery);
            }
        }
    };

    window.closeSisilChat = function() {
        if (backdrop && drawer) {
            backdrop.classList.remove('active');
            drawer.classList.remove('active');
        }
    };

    window.toggleSisilChat = function() {
        if (drawer && drawer.classList.contains('active')) {
            window.closeSisilChat();
        } else {
            window.openSisilChat();
        }
    };

    // Backward compatibility for old dashboard links
    window.openAiAssistant = function(query) {
        window.openSisilChat(query);
    };
    window.closeAiAssistant = function() {
        window.closeSisilChat();
    };

    // Keyboard shortcuts: Alt + A (Toggle), Escape (Close)
    document.addEventListener('keydown', (e) => {
        if (e.altKey && (e.key === 'a' || e.key === 'A')) {
            e.preventDefault();
            window.toggleSisilChat();
        } else if (e.key === 'Escape') {
            hideContextMenu();
            if (drawer && drawer.classList.contains('active')) {
                window.closeSisilChat();
            }
        }
    });

    // LocalStorage Operations
    function getHistory() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveHistory(history) {
        if (history.length > 20) history = history.slice(-20);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
    }

    function renderTime() {
        const d = new Date();
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }

    function appendMessage(role, text, isMarkdown = true, timestamp = null) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `sisil-msg-item sisil-msg-${role === 'user' ? 'user' : 'bot'}`;

        if (role === 'user') {
            msgDiv.innerHTML = `
                <div class="sisil-msg-user-avatar"><i class="feather-user"></i></div>
                <div class="sisil-bubble-wrap">
                    <div class="sisil-bubble sisil-bubble-user">${escapeHtml(text)}</div>
                    <span class="sisil-msg-time">${timestamp || renderTime()}</span>
                </div>
            `;
        } else {
            const parsedHtml = isMarkdown ? marked.parse(text) : text;
            msgDiv.innerHTML = `
                <div class="sisil-msg-avatar-wrap">
                    <img src="{{ asset('img/ai/MaskotAIBerhijabSIMOLI.png') }}" alt="SISIL" class="sisil-msg-avatar">
                </div>
                <div class="sisil-bubble-wrap">
                    <div class="sisil-bubble sisil-bubble-bot">${parsedHtml}</div>
                    <span class="sisil-msg-time">${timestamp || renderTime()}</span>
                </div>
            `;
            msgDiv.querySelectorAll('pre code').forEach(block => {
                try { hljs.highlightElement(block); } catch (e) {}
            });
            attachExcelExportToBubble(msgDiv.querySelector('.sisil-bubble-bot'));
        }

        body.appendChild(msgDiv);
        body.scrollTop = body.scrollHeight;
        return msgDiv.querySelector('.sisil-bubble');
    }

    function escapeHtml(str) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(str).replace(/[&<>"']/g, m => map[m]);
    }

    // Load LocalStorage on start
    function loadSavedHistory() {
        const history = getHistory();
        if (history.length > 0) {
            body.innerHTML = '';
            history.forEach(item => {
                appendMessage(item.role, item.content, item.role !== 'user', item.time);
            });
        }
    }
    loadSavedHistory();

    // Clear history
    clearBtn?.addEventListener('click', () => {
        if (confirm('Hapus seluruh riwayat percakapan dengan SISIL di browser ini?')) {
            localStorage.removeItem(STORAGE_KEY);
            body.innerHTML = `
                <div class="sisil-msg-item sisil-msg-bot">
                    <div class="sisil-msg-avatar-wrap">
                        <img src="{{ asset('img/ai/MaskotAIBerhijabSIMOLI.png') }}" alt="SISIL" class="sisil-msg-avatar">
                    </div>
                    <div class="sisil-bubble-wrap">
                        <div class="sisil-bubble sisil-bubble-bot">
                            <p class="mb-0 fw-bold text-success">👋 Riwayat percakapan telah dibersihkan.</p>
                            <p class="mb-0 text-muted" style="font-size:12px;">Ada yang bisa SISIL bantu terkait data atau laporan SIMOLI?</p>
                        </div>
                        <span class="sisil-msg-time">${renderTime()}</span>
                    </div>
                </div>
            `;
        }
    });

    // Handle send message
    async function handleSend(promptText) {
        if (!promptText.trim()) return;

        const timeStr = renderTime();
        appendMessage('user', promptText, false, timeStr);
        if (input) {
            input.value = '';
            input.disabled = true;
        }
        if (sendBtn) sendBtn.disabled = true;

        const history = getHistory();
        history.push({ role: 'user', content: promptText, time: timeStr });
        saveHistory(history);

        // Typing indicator
        const typingHtml = `
            <div class="sisil-typing-box">
                <div class="sisil-typing-dots">
                    <span></span><span></span><span></span>
                </div>
                <span>SISIL sedang menganalisis...</span>
            </div>
        `;
        const botBubble = appendMessage('assistant', typingHtml, false);
        let fullResponse = '';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const streamUrl = "{{ route('ai.stream') }}";

            const res = await fetch(streamUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'text/event-stream'
                },
                body: JSON.stringify({
                    prompt: promptText,
                    history: history,
                    current_page: document.title || 'SIMOLI Dashboard'
                })
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}: Gagal memproses data`);

            const reader = res.body.getReader();
            const decoder = new TextDecoder('utf-8');
            let isFirstChunk = true;

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                const chunkText = decoder.decode(value, { stream: true });
                const lines = chunkText.split('\n');

                for (const line of lines) {
                    const trimmed = line.trim();
                    if (trimmed.startsWith('data: ')) {
                        const dataStr = trimmed.replace('data: ', '').trim();
                        if (dataStr === '[DONE]') break;

                        try {
                            const parsed = JSON.parse(dataStr);
                            if (parsed.text) {
                                if (isFirstChunk) {
                                    botBubble.innerHTML = '';
                                    isFirstChunk = false;
                                }
                                fullResponse += parsed.text;
                                botBubble.innerHTML = marked.parse(fullResponse);
                                body.scrollTop = body.scrollHeight;
                            } else if (parsed.error) {
                                botBubble.innerHTML = `<span class="text-danger fw-bold"><i class="feather-alert-triangle me-1"></i> ${parsed.error}</span>`;
                            }
                        } catch (err) {}
                    }
                }
            }

            botBubble.querySelectorAll('pre code').forEach(block => {
                try { hljs.highlightElement(block); } catch (e) {}
            });
            attachExcelExportToBubble(botBubble);

            if (fullResponse) {
                history.push({ role: 'assistant', content: fullResponse, time: renderTime() });
                saveHistory(history);
            }

        } catch (error) {
            botBubble.innerHTML = `<span class="text-danger"><i class="feather-alert-circle me-1"></i> Terjadi kesalahan: ${error.message}</span>`;
        } finally {
            if (input) {
                input.disabled = false;
                input.focus();
            }
            if (sendBtn) sendBtn.disabled = false;
        }
    }

    // Helper to post-process bot bubbles and attach Excel export if a table is present
    function attachExcelExportToBubble(bubble) {
        if (!bubble) return;
        const table = bubble.querySelector('table');
        if (!table) return;
        if (bubble.querySelector('.sisil-excel-card')) return;

        // Wrap table for smooth horizontal scroll
        if (!table.parentElement.classList.contains('sisil-table-wrap')) {
            const wrap = document.createElement('div');
            wrap.className = 'sisil-table-wrap';
            table.parentNode.insertBefore(wrap, table);
            wrap.appendChild(table);
        }

        // Create Excel Card
        const card = document.createElement('div');
        card.className = 'sisil-excel-card';
        card.innerHTML = `
            <div class="sisil-excel-info">
                <div class="sisil-excel-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" fill="#107c41"/>
                        <path d="M14 2V8H20" fill="#22c55e"/>
                        <path d="M9 16.5L11.5 12.5M11.5 16.5L9 12.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <div class="sisil-excel-title">Rekap Tabel Terdeteksi</div>
                    <div class="sisil-excel-desc">Ekspor tabel dan metrik ke Microsoft Excel (.xlsx)</div>
                </div>
            </div>
            <button type="button" class="sisil-excel-btn" onclick="window.exportSisilBubbleToExcel(this)" title="Unduh data ini sebagai file Excel (.xlsx)">
                <i class="feather-download"></i>
                <span>Ekspor ke Excel</span>
            </button>
        `;
        bubble.appendChild(card);
    }

    // Export Table & Report to Excel (.xlsx)
    window.exportSisilBubbleToExcel = function(triggerBtn) {
        const bubble = triggerBtn.closest('.sisil-bubble-bot');
        if (!bubble) return;

        const table = bubble.querySelector('table');
        if (!table) {
            alert('Tabel data tidak ditemukan.');
            return;
        }

        const origHtml = triggerBtn.innerHTML;
        triggerBtn.disabled = true;
        triggerBtn.innerHTML = `<i class="feather-loader"></i> <span>Mengekspor...</span>`;

        try {
            // 1. Extract Title
            const h3 = bubble.querySelector('h3, h4');
            const reportTitle = h3 ? h3.innerText.replace(/^[^\w\s\(\)]+/, '').trim() : 'Laporan Rekap Data SIMOLI';

            // 2. Extract Periode & Cakupan
            let periode = '';
            let cakupan = '12 Unit Mill PKS';
            bubble.querySelectorAll('p, li').forEach(el => {
                const txt = el.innerText.trim();
                if (txt.includes('Periode:')) {
                    const m = txt.match(/Periode:\s*([^🏢\n]+)/i);
                    if (m) periode = m[1].trim();
                }
                if (txt.includes('Cakupan:')) {
                    const m = txt.match(/Cakupan:\s*([^\n]+)/i);
                    if (m) cakupan = m[1].trim();
                }
            });

            // 3. Extract Summary / Akumulasi Metrics
            const summaryItems = [];
            bubble.querySelectorAll('ul li, ol li').forEach(li => {
                const raw = li.innerText.replace(/^[^\w\s\(\)\/]+/, '').trim();
                if (raw.includes(':')) {
                    const idx = raw.indexOf(':');
                    const label = raw.substring(0, idx).trim();
                    const rawVal = raw.substring(idx + 1).trim();

                    // Split value and unit if available
                    const unitMatch = rawVal.match(/^(.*?)\s*(m³|Bed|Ha|Hektar|Liter|Jam|HK|Transaksi|\%)\b/i);
                    if (unitMatch) {
                        const numVal = parseIndoNumber(unitMatch[1]);
                        summaryItems.push([label, numVal !== null ? numVal : unitMatch[1].trim(), unitMatch[2].trim()]);
                    } else {
                        const numVal = parseIndoNumber(rawVal);
                        summaryItems.push([label, numVal !== null ? numVal : rawVal, '']);
                    }
                }
            });

            // 4. Extract Table Data & Standardize Headers
            const headerCells = table.querySelectorAll('thead th, tr:first-child th');
            const headers = [];
            if (headerCells.length > 0) {
                headerCells.forEach(th => {
                    let hText = th.innerText.trim();
                    if (/^PKS$/i.test(hText)) hText = 'Unit PKS';
                    if (/^Vol Dihasilkan$/i.test(hText)) hText = 'Vol. Dihasilkan (m³)';
                    if (/^Vol Dialirkan$/i.test(hText)) hText = 'Vol. Dialirkan (m³)';
                    if (/^Flat Bed$/i.test(hText)) hText = 'Aplikasi Flat Bed (Bed)';
                    if (/^Long Bed$/i.test(hText)) hText = 'Aplikasi Long Bed (Bed)';
                    if (/^Luas$/i.test(hText)) hText = 'Luas Area (Ha)';
                    headers.push(hText);
                });
            }

            const bodyRows = table.querySelectorAll('tbody tr, tr:not(:first-child)');
            const parsedDataRows = [];
            const colTotals = {};
            let hasNumericData = false;

            bodyRows.forEach(tr => {
                const cells = tr.querySelectorAll('td, th');
                if (cells.length > 0 && cells[0].tagName.toLowerCase() === 'td') {
                    const row = [];
                    cells.forEach((c, cIdx) => {
                        const rawCell = c.innerText.trim();
                        // Column 0 is No
                        if (cIdx === 0 && /^\d+$/.test(rawCell)) {
                            row.push(parseInt(rawCell, 10));
                        } else if (cIdx === 1) {
                            // Column 1 is PKS Code
                            row.push(rawCell);
                        } else {
                            // Numeric columns
                            const num = parseIndoNumber(rawCell);
                            if (num !== null) {
                                row.push(num);
                                colTotals[cIdx] = (colTotals[cIdx] || 0) + num;
                                hasNumericData = true;
                            } else {
                                row.push(rawCell);
                            }
                        }
                    });
                    parsedDataRows.push(row);
                }
            });

            // Create Total Row if numeric data exists
            let totalRow = null;
            if (hasNumericData && parsedDataRows.length > 0) {
                totalRow = ['', 'TOTAL / JUMLAH'];
                for (let c = 2; c < headers.length; c++) {
                    if (colTotals[c] !== undefined) {
                        const val = colTotals[c];
                        totalRow.push(Math.round(val * 100) / 100);
                    } else {
                        totalRow.push('-');
                    }
                }
            }

            // Clean Filename
            const cleanPeriod = (periode || 'Rekap').replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 30);
            const cleanTitle = reportTitle.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 35);
            const downloadDateStr = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            const fileName = `SIMOLI_${cleanTitle}_${cleanPeriod}.xlsx`;

            // 5. Generate Excel via SheetJS (.xlsx)
            if (typeof XLSX !== 'undefined') {
                const aoa = [
                    ['PTPN IV REGIONAL III'],
                    ['SIMOLI — SISTEM PEMANTAUAN LIMBAH & LAND APPLICATION'],
                    [reportTitle.toUpperCase()],
                    ['Periode: ' + (periode || '-') + '   |   Cakupan: ' + cakupan + '   |   Tanggal Unduh: ' + downloadDateStr],
                    []
                ];

                // Add Executive Summary Section
                if (summaryItems.length > 0) {
                    aoa.push(['I. RINGKASAN AKUMULASI KINERJA OPERASIONAL']);
                    aoa.push(['No', 'Indikator / Parameter Operasional', 'Nilai Realisasi', 'Satuan']);
                    summaryItems.forEach((item, idx) => {
                        aoa.push([idx + 1, item[0], item[1], item[2]]);
                    });
                    aoa.push([]);
                }

                // Add Unit PKS Breakdown Table
                aoa.push(['II. RINCIAN DATA REKAPITULASI PER UNIT PKS (URUTAN STANDAR BAKU)']);
                aoa.push(headers);
                parsedDataRows.forEach(r => aoa.push(r));
                if (totalRow) {
                    aoa.push(totalRow);
                }

                // Add Footer note
                aoa.push([]);
                aoa.push(['Keterangan & Catatan Operasional:']);
                aoa.push(['• Laporan resmi diekspor otomatis dari SIMOLI Smart Intelligence Assistant (SISIL) PTPN IV Regional III.']);
                aoa.push(['• Urutan 12 unit PKS mengikuti standar baku operasional Bagian Teknik & Pengolahan (TEP).']);

                const ws = XLSX.utils.aoa_to_sheet(aoa);

                // Set Column Widths (Pad nicely so numbers and labels are never truncated)
                const colWidths = [
                    { wch: 8 },  // Col A: No
                    { wch: 22 }, // Col B: Unit PKS / Parameter
                    { wch: 24 }, // Col C: Vol Dihasilkan / Nilai
                    { wch: 24 }, // Col D: Vol Dialirkan / Satuan
                    { wch: 22 }, // Col E: Flat Bed
                    { wch: 20 }, // Col F: Luas
                    { wch: 18 }
                ];
                ws['!cols'] = colWidths;

                // Merge title headers across columns A:F
                ws['!merges'] = [
                    { s: { r: 0, c: 0 }, e: { r: 0, c: 5 } },
                    { s: { r: 1, c: 0 }, e: { r: 1, c: 5 } },
                    { s: { r: 2, c: 0 }, e: { r: 2, c: 5 } },
                    { s: { r: 3, c: 0 }, e: { r: 3, c: 5 } }
                ];

                // Enable Gridlines
                ws['!views'] = [{ showGridLines: true }];

                // Format Numeric Cells with Excel Thousand Separators
                const range = XLSX.utils.decode_range(ws['!ref']);
                for (let R = range.s.r; R <= range.e.r; ++R) {
                    for (let C = range.s.c; C <= range.e.c; ++C) {
                        const cellAddr = XLSX.utils.encode_cell({ r: R, c: C });
                        const cell = ws[cellAddr];
                        if (cell && cell.t === 'n') {
                            if (C === 0) {
                                cell.z = '0';
                            } else if (Number.isInteger(cell.v)) {
                                cell.z = '#,##0';
                            } else {
                                cell.z = '#,##0.00';
                            }
                        }
                    }
                }

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Rekap SIMOLI');

                XLSX.writeFile(wb, fileName);

                triggerBtn.innerHTML = `<i class="feather-check"></i> <span>Tersimpan (.xlsx)!</span>`;
                setTimeout(() => {
                    triggerBtn.disabled = false;
                    triggerBtn.innerHTML = origHtml;
                }, 2500);
            } else {
                // Fallback XML Spreadsheet format with corporate styling
                downloadXmlSpreadsheet(reportTitle, periode, cakupan, summaryItems, headers, parsedDataRows, totalRow, fileName.replace('.xlsx', '.xls'));
                triggerBtn.innerHTML = `<i class="feather-check"></i> <span>Tersimpan (.xls)!</span>`;
                setTimeout(() => {
                    triggerBtn.disabled = false;
                    triggerBtn.innerHTML = origHtml;
                }, 2500);
            }
        } catch (err) {
            console.error('[SIMOLI AI] Error exporting to Excel:', err);
            alert('Gagal mengekspor Excel: ' + err.message);
            triggerBtn.disabled = false;
            triggerBtn.innerHTML = origHtml;
        }
    };

    // Helper Number Parser for Indonesian formatted numbers
    function parseIndoNumber(str) {
        if (typeof str === 'number') return str;
        if (typeof str !== 'string') return null;
        let s = str.replace(/[^\d.,-]/g, '').trim();
        if (!s) return null;
        if (s.includes('.') && s.includes(',')) {
            s = s.replace(/\./g, '').replace(',', '.');
        } else if (s.includes(',')) {
            s = s.replace(',', '.');
        } else if (s.includes('.')) {
            const parts = s.split('.');
            if (parts.length === 2 && parts[1].length === 3 && parseInt(parts[0], 10) > 0) {
                s = parts[0] + parts[1];
            } else if (parts.length > 2) {
                s = s.replace(/\./g, '');
            }
        }
        const num = parseFloat(s);
        return isNaN(num) ? null : num;
    }

    // Professional XML Spreadsheet 2003 with corporate styles, colors, and borders
    function downloadXmlSpreadsheet(title, periode, cakupan, summaryItems, headers, dataRows, totalRow, filename) {
        const downloadDateStr = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        let xml = '<' + '?xml version="1.0"?>\n' +
            '<' + '?mso-application progid="Excel.Sheet"?>\n' +
            `<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal"><Font ss:FontName="Calibri" ss:Size="11" ss:Color="#1E293B"/></Style>
  <Style ss:ID="CompanyBrand"><Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#475569"/></Style>
  <Style ss:ID="SystemBrand"><Font ss:FontName="Calibri" ss:Size="12" ss:Bold="1" ss:Color="#15803D"/></Style>
  <Style ss:ID="ReportHeading"><Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#0F172A"/><Interior ss:Color="#F0FDF4" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#86EFAC"/></Borders></Style>
  <Style ss:ID="MetaBanner"><Font ss:FontName="Calibri" ss:Size="10" ss:Color="#64748B"/></Style>
  <Style ss:ID="SectionTitle"><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#0F172A"/></Style>
  <Style ss:ID="TblHeader"><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#15803D" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#166534"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#166534"/></Borders></Style>
  <Style ss:ID="SummaryHeader"><Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0F766E" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/></Borders></Style>
  <Style ss:ID="RowEven"><Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>
  <Style ss:ID="RowOdd"><Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>
  <Style ss:ID="CellNo"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>
  <Style ss:ID="CellPks"><Font ss:Bold="1" ss:Color="#0F172A"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>
  <Style ss:ID="CellNum"><Alignment ss:Horizontal="Right" ss:Vertical="Center"/><NumberFormat ss:Format="#,##0.00"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>
  <Style ss:ID="CellInt"><Alignment ss:Horizontal="Right" ss:Vertical="Center"/><NumberFormat ss:Format="#,##0"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>
  <Style ss:ID="TotalLabel"><Font ss:Bold="1" ss:Color="#0F172A"/><Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#15803D"/><Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#15803D"/></Borders></Style>
  <Style ss:ID="TotalNum"><Font ss:Bold="1" ss:Color="#0F172A"/><Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/><Alignment ss:Horizontal="Right" ss:Vertical="Center"/><NumberFormat ss:Format="#,##0.00"/><Borders><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#15803D"/><Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#15803D"/></Borders></Style>
  <Style ss:ID="TotalInt"><Font ss:Bold="1" ss:Color="#0F172A"/><Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/><Alignment ss:Horizontal="Right" ss:Vertical="Center"/><NumberFormat ss:Format="#,##0"/><Borders><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#15803D"/><Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#15803D"/></Borders></Style>
  <Style ss:ID="FooterNote"><Font ss:FontName="Calibri" ss:Size="9" ss:Italic="1" ss:Color="#64748B"/></Style>
 </Styles>
 <Worksheet ss:Name="Rekap SIMOLI">
  <Table>
   <Column ss:Width="45"/>
   <Column ss:Width="160"/>
   <Column ss:Width="140"/>
   <Column ss:Width="140"/>
   <Column ss:Width="130"/>
   <Column ss:Width="120"/>
   <Row><Cell ss:StyleID="CompanyBrand"><Data ss:Type="String">PTPN IV REGIONAL III</Data></Cell></Row>
   <Row><Cell ss:StyleID="SystemBrand"><Data ss:Type="String">SIMOLI — SISTEM PEMANTAUAN LIMBAH &amp; LAND APPLICATION</Data></Cell></Row>
   <Row><Cell ss:StyleID="ReportHeading"><Data ss:Type="String">${escapeXml(title.toUpperCase())}</Data></Cell></Row>
   <Row><Cell ss:StyleID="MetaBanner"><Data ss:Type="String">Periode: ${escapeXml(periode || '-')}   |   Cakupan: ${escapeXml(cakupan)}   |   Tanggal Unduh: ${downloadDateStr}</Data></Cell></Row>
   <Row></Row>`;

        if (summaryItems.length > 0) {
            xml += `<Row><Cell ss:StyleID="SectionTitle"><Data ss:Type="String">I. RINGKASAN AKUMULASI KINERJA OPERASIONAL</Data></Cell></Row>`;
            xml += `<Row>
                <Cell ss:StyleID="SummaryHeader"><Data ss:Type="String">No</Data></Cell>
                <Cell ss:StyleID="SummaryHeader"><Data ss:Type="String">Indikator / Parameter Operasional</Data></Cell>
                <Cell ss:StyleID="SummaryHeader"><Data ss:Type="String">Nilai Realisasi</Data></Cell>
                <Cell ss:StyleID="SummaryHeader"><Data ss:Type="String">Satuan</Data></Cell>
            </Row>`;
            summaryItems.forEach((item, idx) => {
                const styleRow = idx % 2 === 0 ? 'RowEven' : 'RowOdd';
                const isNum = typeof item[1] === 'number';
                xml += `<Row>
                    <Cell ss:StyleID="${styleRow}"><Data ss:Type="Number">${idx + 1}</Data></Cell>
                    <Cell ss:StyleID="${styleRow}"><Data ss:Type="String">${escapeXml(item[0])}</Data></Cell>
                    <Cell ss:StyleID="${isNum ? 'CellNum' : styleRow}"><Data ss:Type="${isNum ? 'Number' : 'String'}">${isNum ? item[1] : escapeXml(String(item[1]))}</Data></Cell>
                    <Cell ss:StyleID="${styleRow}"><Data ss:Type="String">${escapeXml(item[2])}</Data></Cell>
                </Row>`;
            });
            xml += `<Row></Row>`;
        }

        xml += `<Row><Cell ss:StyleID="SectionTitle"><Data ss:Type="String">II. RINCIAN DATA REKAPITULASI PER UNIT PKS (URUTAN STANDAR BAKU)</Data></Cell></Row>`;

        // Table Header
        xml += `<Row>`;
        headers.forEach(h => {
            xml += `<Cell ss:StyleID="TblHeader"><Data ss:Type="String">${escapeXml(h)}</Data></Cell>`;
        });
        xml += `</Row>`;

        // Table Rows
        dataRows.forEach((row, rIdx) => {
            const rowStyle = rIdx % 2 === 0 ? 'RowEven' : 'RowOdd';
            xml += `<Row>`;
            row.forEach((val, cIdx) => {
                if (cIdx === 0 && typeof val === 'number') {
                    xml += `<Cell ss:StyleID="CellNo"><Data ss:Type="Number">${val}</Data></Cell>`;
                } else if (cIdx === 1) {
                    xml += `<Cell ss:StyleID="CellPks"><Data ss:Type="String">${escapeXml(String(val))}</Data></Cell>`;
                } else if (typeof val === 'number') {
                    const styleCell = Number.isInteger(val) ? 'CellInt' : 'CellNum';
                    xml += `<Cell ss:StyleID="${styleCell}"><Data ss:Type="Number">${val}</Data></Cell>`;
                } else {
                    xml += `<Cell ss:StyleID="${rowStyle}"><Data ss:Type="String">${escapeXml(String(val))}</Data></Cell>`;
                }
            });
            xml += `</Row>`;
        });

        // Total Row
        if (totalRow) {
            xml += `<Row>`;
            totalRow.forEach((val, cIdx) => {
                if (cIdx <= 1) {
                    xml += `<Cell ss:StyleID="TotalLabel"><Data ss:Type="String">${escapeXml(String(val))}</Data></Cell>`;
                } else if (typeof val === 'number') {
                    const styleCell = Number.isInteger(val) ? 'TotalInt' : 'TotalNum';
                    xml += `<Cell ss:StyleID="${styleCell}"><Data ss:Type="Number">${val}</Data></Cell>`;
                } else {
                    xml += `<Cell ss:StyleID="TotalLabel"><Data ss:Type="String">${escapeXml(String(val))}</Data></Cell>`;
                }
            });
            xml += `</Row>`;
        }

        // Footnotes
        xml += `<Row></Row>`;
        xml += `<Row><Cell ss:StyleID="FooterNote"><Data ss:Type="String">&bull; Laporan resmi otomatis diekspor dari SIMOLI AI Assistant (SISIL) PTPN IV Regional III.</Data></Cell></Row>`;
        xml += `<Row><Cell ss:StyleID="FooterNote"><Data ss:Type="String">&bull; Urutan 12 unit PKS mengikuti standar baku operasional Bagian Teknik &amp; Pengolahan (TEP).</Data></Cell></Row>`;

        xml += `</Table></Worksheet></Workbook>`;

        const blob = new Blob([xml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }

    function escapeXml(str) {
        if (!str) return '';
        return String(str).replace(/[<>&'"]/g, c => {
            switch (c) {
                case '<': return '&lt;';
                case '>': return '&gt;';
                case '&': return '&amp;';
                case '\'': return '&apos;';
                case '"': return '&quot;';
            }
        });
    }

    window.handleSisilSubmit = function() {
        if (input && input.value) {
            handleSend(input.value);
        }
    };

    window.sendSimoliAiPrompt = function(text) {
        window.openSisilChat();
        handleSend(text);
    };
})();
</script>
