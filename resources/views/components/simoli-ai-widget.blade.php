<!-- ============================================================
     SIMOLI AI ASSISTANT (DEEPSEEK LOCAL ENGINE WIDGET)
     PTPN IV Regional III — Sistem Pemantauan Limbah & Land Application
     ============================================================ -->
<div id="simoli-ai-widget" class="simoli-ai-widget-wrapper">
    <!-- Floating Trigger Button -->
    <button type="button" id="simoliAiTriggerBtn" class="simoli-ai-trigger-btn" aria-label="Buka SIMOLI AI Assistant" title="Tanya SIMOLI AI Assistant">
        <div class="ai-btn-inner">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a10 10 0 0 1 10 10c0 5.523-4.477 10-10 10a9.96 9.96 0 0 1-4.587-1.112L3 21l1.112-4.413A9.96 9.96 0 0 1 2 12C2 6.477 6.477 2 12 2z"></path>
                <path d="m9 10 3-3 3 3"></path>
                <path d="m9 14 3 3 3-3"></path>
            </svg>
        </div>
        <span class="ai-online-ping"></span>
    </button>

    <!-- Floating Chat Window -->
    <div id="simoliAiCard" class="simoli-ai-window" style="display: none;" role="dialog" aria-modal="true" aria-label="SIMOLI AI Assistant">
        <!-- Header -->
        <div class="simoli-ai-card-header">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <div class="ai-avatar-icon">
                    <i class="feather-cpu text-white" style="font-size: 16px;"></i>
                </div>
                <div class="min-w-0">
                    <h6 class="ai-card-title mb-0 text-truncate">SIMOLI AI Assistant</h6>
                    <div class="d-flex align-items-center gap-1">
                        <span class="ai-status-indicator"></span>
                        <small class="ai-card-subtitle text-white-50">DeepSeek Local Engine</small>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" id="simoliAiClearBtn" class="ai-tool-btn" title="Hapus Riwayat Chat di Browser" aria-label="Hapus Riwayat Chat">
                    <i class="feather-trash-2"></i>
                </button>
                <button type="button" id="simoliAiCloseBtn" class="ai-tool-btn" title="Tutup Chat" aria-label="Tutup Chat">
                    <i class="feather-x"></i>
                </button>
            </div>
        </div>

        <!-- Chat Body (Messages) -->
        <div id="simoliAiBody" class="simoli-ai-card-body">
            <div class="ai-msg-bubble ai-msg-assistant">
                <div class="bubble-content">
                    Halo! 👋 Saya <strong>SIMOLI AI Assistant</strong> yang berjalan lokal di server PTPN IV. Ada yang bisa saya bantu mengenai data pengaliran limbah, RKP, pemeliharaan, atau status harian PKS?
                </div>
                <span class="bubble-time">{{ date('H:i') }}</span>
            </div>
        </div>

        <!-- Suggestion Chips Bar -->
        <div class="simoli-ai-quick-chips">
            <button type="button" class="ai-chip-item" onclick="window.sendSimoliAiPrompt('Status kepatuhan input PKS hari ini')">
                <i class="feather-check-circle me-1 text-success"></i> Status Input Hari Ini
            </button>
            <button type="button" class="ai-chip-item" onclick="window.sendSimoliAiPrompt('Rekap data pengaliran limbah bulan ini')">
                <i class="feather-droplet me-1 text-info"></i> Rekap Pengaliran
            </button>
            <button type="button" class="ai-chip-item" onclick="window.sendSimoliAiPrompt('Data RKP tahun ' + new Date().getFullYear())">
                <i class="feather-clipboard me-1 text-warning"></i> Target RKP Bed
            </button>
        </div>

        <!-- Footer Input -->
        <div class="simoli-ai-card-footer">
            <form id="simoliAiChatForm" class="d-flex align-items-center gap-2 m-0 w-100">
                <input type="text" id="simoliAiInput" class="form-control ai-text-input" placeholder="Ketik pertanyaan atau perintah..." autocomplete="off" required maxlength="1000">
                <button type="submit" id="simoliAiSendBtn" class="btn ai-send-action-btn" aria-label="Kirim Pesan">
                    <i class="feather-send"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Dependencies: Markdown parser & Highlight.js for Code Blocks -->
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<style>
/* ================================================================
   SIMOLI AI ASSISTANT WIDGET STYLING
   ================================================================ */
.simoli-ai-widget-wrapper {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 10500;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.simoli-ai-trigger-btn {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #0d9488);
    color: #ffffff;
    border: none;
    box-shadow: 0 8px 24px rgba(22, 163, 74, 0.4);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.simoli-ai-trigger-btn:hover {
    transform: translateY(-2px) scale(1.06);
    box-shadow: 0 12px 30px rgba(22, 163, 74, 0.55);
}

.ai-online-ping {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #22c55e;
    border: 2px solid #ffffff;
    border-radius: 50%;
}

.simoli-ai-window {
    position: absolute;
    bottom: 68px;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 32px);
    height: 540px;
    max-height: calc(100vh - 100px);
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.2), 0 0 0 1px rgba(22, 163, 74, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: simoliAiPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes simoliAiPopIn {
    from { opacity: 0; transform: translateY(18px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.simoli-ai-card-header {
    background: linear-gradient(135deg, #16a34a, #0f766e);
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.ai-avatar-icon {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ai-card-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.2px;
}

.ai-card-subtitle {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.75);
}

.ai-status-indicator {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #4ade80;
}

.ai-tool-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.85);
    padding: 6px 8px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s;
}

.ai-tool-btn:hover {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
}

.simoli-ai-card-body {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #f8fafc;
    scroll-behavior: smooth;
}

.ai-msg-bubble {
    display: flex;
    flex-direction: column;
    max-width: 88%;
}

.ai-msg-user {
    align-self: flex-end;
}

.ai-msg-assistant {
    align-self: flex-start;
}

.bubble-content {
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 12.5px;
    line-height: 1.55;
    word-break: break-word;
}

.ai-msg-user .bubble-content {
    background: #16a34a;
    color: #ffffff;
    border-bottom-right-radius: 3px;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25);
}

.ai-msg-assistant .bubble-content {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 3px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.ai-msg-assistant .bubble-content p {
    margin-bottom: 6px;
}

.ai-msg-assistant .bubble-content p:last-child {
    margin-bottom: 0;
}

.ai-msg-assistant .bubble-content pre {
    background: #1e293b;
    color: #f8fafc;
    padding: 8px 12px;
    border-radius: 8px;
    overflow-x: auto;
    font-size: 11px;
    margin: 6px 0;
}

.ai-msg-assistant .bubble-content table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    margin: 6px 0;
}

.ai-msg-assistant .bubble-content table th,
.ai-msg-assistant .bubble-content table td {
    border: 1px solid #cbd5e1;
    padding: 4px 6px;
}

.ai-msg-assistant .bubble-content table th {
    background: #f1f5f9;
    font-weight: 700;
}

.bubble-time {
    font-size: 9.5px;
    color: #94a3b8;
    margin-top: 3px;
    padding: 0 4px;
}

.ai-msg-user .bubble-time {
    text-align: right;
}

.simoli-ai-quick-chips {
    padding: 6px 10px;
    display: flex;
    gap: 6px;
    overflow-x: auto;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    flex-shrink: 0;
    scrollbar-width: none;
}

.simoli-ai-quick-chips::-webkit-scrollbar {
    display: none;
}

.ai-chip-item {
    white-space: nowrap;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 3px 10px;
    font-size: 11px;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
}

.ai-chip-item:hover {
    background: #16a34a;
    color: #ffffff;
    border-color: #16a34a;
}

.simoli-ai-card-footer {
    padding: 10px 12px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.ai-text-input {
    border-radius: 20px;
    font-size: 12.5px;
    padding: 7px 14px;
    border: 1px solid #cbd5e1;
}

.ai-text-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
}

.ai-send-action-btn {
    border-radius: 50%;
    width: 34px;
    height: 34px;
    padding: 0;
    background: #16a34a;
    color: #ffffff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.2s;
}

.ai-send-action-btn:hover {
    background: #15803d;
    color: #ffffff;
}

.ai-send-action-btn:disabled {
    background: #94a3b8;
    cursor: not-allowed;
}
</style>

<script>
(function() {
    const STORAGE_KEY = 'simoli_ai_chat_history_v1';
    const triggerBtn = document.getElementById('simoliAiTriggerBtn');
    const card = document.getElementById('simoliAiCard');
    const closeBtn = document.getElementById('simoliAiCloseBtn');
    const clearBtn = document.getElementById('simoliAiClearBtn');
    const form = document.getElementById('simoliAiChatForm');
    const input = document.getElementById('simoliAiInput');
    const sendBtn = document.getElementById('simoliAiSendBtn');
    const body = document.getElementById('simoliAiBody');

    if (!triggerBtn || !card || !form || !input || !body) return;

    // Toggle Chat Window
    triggerBtn.addEventListener('click', () => {
        const isHidden = card.style.display === 'none';
        card.style.display = isHidden ? 'flex' : 'none';
        if (isHidden) {
            input.focus();
            body.scrollTop = body.scrollHeight;
        }
    });

    closeBtn?.addEventListener('click', () => {
        card.style.display = 'none';
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
        msgDiv.className = `ai-msg-bubble ai-msg-${role === 'user' ? 'user' : 'assistant'}`;

        const bubble = document.createElement('div');
        bubble.className = 'bubble-content';

        if (role === 'user' || !isMarkdown) {
            bubble.textContent = text;
        } else {
            bubble.innerHTML = marked.parse(text);
            bubble.querySelectorAll('pre code').forEach(block => {
                try { hljs.highlightElement(block); } catch (e) {}
            });
        }

        const timeSpan = document.createElement('span');
        timeSpan.className = 'bubble-time';
        timeSpan.textContent = timestamp || renderTime();

        msgDiv.appendChild(bubble);
        msgDiv.appendChild(timeSpan);
        body.appendChild(msgDiv);
        body.scrollTop = body.scrollHeight;
        return bubble;
    }

    // Load LocalStorage history on startup
    function loadSavedChat() {
        const history = getHistory();
        if (history.length > 0) {
            body.innerHTML = '';
            history.forEach(item => {
                appendMessage(item.role, item.content, item.role !== 'user', item.time);
            });
        }
    }
    loadSavedChat();

    // Clear History Button
    clearBtn?.addEventListener('click', () => {
        if (confirm('Hapus seluruh riwayat percakapan AI di browser ini?')) {
            localStorage.removeItem(STORAGE_KEY);
            body.innerHTML = `
                <div class="ai-msg-bubble ai-msg-assistant">
                    <div class="bubble-content">
                        Riwayat percakapan telah dibersihkan. Ada yang bisa saya bantu selanjutnya? 😊
                    </div>
                    <span class="bubble-time">${renderTime()}</span>
                </div>
            `;
        }
    });

    // Handle Streaming Prompt
    async function handleSend(promptText) {
        if (!promptText.trim()) return;

        const timeStr = renderTime();
        appendMessage('user', promptText, false, timeStr);
        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        const history = getHistory();
        history.push({ role: 'user', content: promptText, time: timeStr });
        saveHistory(history);

        const botBubble = appendMessage('assistant', '<span class="text-muted fst-italic"><i class="feather-loader me-1"></i> Sedang berpikir...</span>', false);
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

            if (!res.ok) throw new Error(`HTTP ${res.status}: Gagal memproses permintaan`);

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
                        } catch (err) {
                            // Ignored partial json chunk
                        }
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
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        handleSend(input.value);
    });

    window.sendSimoliAiPrompt = function(text) {
        if (card.style.display === 'none') {
            card.style.display = 'flex';
        }
        handleSend(text);
    };
})();
</script>
