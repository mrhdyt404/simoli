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
            <img src="{{ asset('images/sisil-avatar.jpg') }}" alt="SISIL Avatar" class="sisil-fab-avatar">
            <span class="sisil-online-dot"></span>
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
                    <img src="{{ asset('images/sisil-avatar.jpg') }}" alt="SISIL" class="sisil-hdr-avatar">
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
                    <img src="{{ asset('images/sisil-avatar.jpg') }}" alt="SISIL" class="sisil-msg-avatar">
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

<!-- Dependencies: Markdown parser & Highlight.js for Code Blocks -->
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

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
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: #0b1329;
    border: 2px solid #38bdf8;
    box-shadow: 0 8px 24px rgba(2, 132, 199, 0.4), 0 0 15px rgba(56, 189, 248, 0.35);
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
    box-shadow: 0 12px 30px rgba(2, 132, 199, 0.55), 0 0 22px rgba(56, 189, 248, 0.6);
}

.sisil-fab-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
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
    border: 2px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
    flex-shrink: 0;
    background: #0b1329;
}

.sisil-hdr-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: block;
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
    width: 34px;
    height: 34px;
    border-radius: 50%;
    flex-shrink: 0;
    overflow: hidden;
    border: 1.5px solid rgba(22, 163, 74, 0.3);
    background: #0b1329;
}

.sisil-msg-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
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

.sisil-bubble table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
    margin: 8px 0;
}

.sisil-bubble table th,
.sisil-bubble table td {
    border: 1px solid #cbd5e1;
    padding: 5px 8px;
}

.sisil-bubble table th {
    background: #f1f5f9;
    font-weight: 700;
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
        } else if (e.key === 'Escape' && drawer && drawer.classList.contains('active')) {
            window.closeSisilChat();
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
                    <img src="{{ asset('images/sisil-avatar.jpg') }}" alt="SISIL" class="sisil-msg-avatar">
                </div>
                <div class="sisil-bubble-wrap">
                    <div class="sisil-bubble sisil-bubble-bot">${parsedHtml}</div>
                    <span class="sisil-msg-time">${timestamp || renderTime()}</span>
                </div>
            `;
            msgDiv.querySelectorAll('pre code').forEach(block => {
                try { hljs.highlightElement(block); } catch (e) {}
            });
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
                        <img src="{{ asset('images/sisil-avatar.jpg') }}" alt="SISIL" class="sisil-msg-avatar">
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
