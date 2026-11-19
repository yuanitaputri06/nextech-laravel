/**
 * NexTech AI Chatbot Assistant
 * Interactive Client Script
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'nextech_chat_history_v1';
    let isWaitingResponse = false;

    // DOM Elements
    let launcherBtn, chatWindow, chatBody, chatForm, chatInput, sendBtn, typingIndicator, clearBtn, closeBtn;
    let endpointUrl = '/chatbot/message';
    let csrfToken = '';

    // Inisialisasi saat DOM siap
    document.addEventListener('DOMContentLoaded', initNexTechChatbot);

    function initNexTechChatbot() {
        const root = document.getElementById('nextech-chatbot-root');
        if (!root) return;

        endpointUrl = root.getAttribute('data-endpoint') || '/chatbot/message';
        csrfToken = root.getAttribute('data-csrf') || '';

        launcherBtn = document.getElementById('nextech-chat-launcher');
        chatWindow = document.getElementById('nextech-chat-window');
        chatBody = document.getElementById('nextech-chat-body');
        chatForm = document.getElementById('nextech-chat-form');
        chatInput = document.getElementById('nextech-chat-input');
        sendBtn = document.getElementById('nextech-chat-send');
        typingIndicator = document.getElementById('nextech-typing-indicator');
        clearBtn = document.getElementById('nextech-chat-clear');
        closeBtn = document.getElementById('nextech-chat-close');

        if (!launcherBtn || !chatWindow) return;

        // Event Listeners
        launcherBtn.addEventListener('click', toggleChat);
        if (closeBtn) closeBtn.addEventListener('click', closeChat);
        if (clearBtn) clearBtn.addEventListener('click', clearHistory);

        if (chatForm) {
            chatForm.addEventListener('submit', function (e) {
                e.preventDefault();
                handleUserSubmit();
            });
        }

        if (chatInput) {
            chatInput.addEventListener('input', function () {
                if (sendBtn) {
                    sendBtn.disabled = !chatInput.value.trim() || isWaitingResponse;
                }
            });

            // Kirim dengan Enter (tanpa Shift)
            chatInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!isWaitingResponse && chatInput.value.trim()) {
                        handleUserSubmit();
                    }
                }
            });
        }

        // Delegasi klik untuk chips / suggestion pills
        document.addEventListener('click', function (e) {
            const chip = e.target.closest('.suggestion-chip, .chat-quick-pill');
            if (chip) {
                const query = chip.getAttribute('data-query') || chip.textContent.trim();
                if (query) {
                    openChat();
                    sendUserQuery(query);
                }
            }
        });

        // Muat riwayat chat atau tampilkan welcome default
        loadChatHistory();
    }

    function toggleChat() {
        if (chatWindow.classList.contains('open')) {
            closeChat();
        } else {
            openChat();
        }
    }

    function openChat() {
        chatWindow.classList.add('open');
        launcherBtn.classList.add('active');
        if (chatInput) {
            setTimeout(() => chatInput.focus(), 300);
        }
        scrollToBottom();
    }

    function closeChat() {
        chatWindow.classList.remove('open');
        launcherBtn.classList.remove('active');
    }

    function handleUserSubmit() {
        if (isWaitingResponse || !chatInput) return;
        const text = chatInput.value.trim();
        if (!text) return;

        chatInput.value = '';
        if (sendBtn) sendBtn.disabled = true;

        sendUserQuery(text);
    }

    function sendUserQuery(text) {
        if (isWaitingResponse) return;

        // Sembunyikan welcome card default jika ada pesan baru
        const welcomeCard = document.getElementById('nextech-welcome-card');
        if (welcomeCard) {
            welcomeCard.style.display = 'none';
        }

        // 1. Tampilkan pesan user di chat
        const timeStr = getCurrentTime();
        appendMessageRow('user', text, null, timeStr);
        saveMessageToStorage({ role: 'user', text: text, time: timeStr });

        // 2. Aktifkan indikator loading
        setLoadingState(true);

        // 3. Request ke backend Laravel
        fetch(endpointUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: text,
                history: getRecentHistory()
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' . response.status);
                }
                return response.json();
            })
            .then(data => {
                setLoadingState(false);
                if (data && data.success) {
                    const replyText = data.reply || 'Berikut rekomendasi produk yang tersedia:';
                    const products = data.products || [];
                    const botTime = getCurrentTime();

                    appendMessageRow('bot', replyText, products, botTime);
                    saveMessageToStorage({
                        role: 'bot',
                        text: replyText,
                        products: products,
                        time: botTime
                    });
                } else {
                    handleFallbackError();
                }
            })
            .catch(error => {
                console.warn('NexTech Chatbot fetch error:', error);
                setLoadingState(false);
                handleFallbackError();
            });
    }

    function handleFallbackError() {
        const errorText = "Maaf, asisten kami sedang mengalami gangguan koneksi sesaat. Silakan coba tanyakan kembali atau gunakan tombol rekomendasi di bawah.";
        const botTime = getCurrentTime();
        appendMessageRow('bot', errorText, null, botTime);
        saveMessageToStorage({ role: 'bot', text: errorText, time: botTime });
    }

    function appendMessageRow(role, text, products, time) {
        if (!chatBody) return;

        const row = document.createElement('div');
        row.className = 'chat-message-row ' + role;

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.innerHTML = formatMarkdown(text);

        // Tambahkan kartu produk jika ada
        if (products && products.length > 0) {
            const productsContainer = document.createElement('div');
            productsContainer.className = 'chat-products-container';

            products.forEach(p => {
                const card = document.createElement('a');
                card.href = p.url || ('/product/' + p.product_id);
                card.className = 'chat-product-card';
                card.setAttribute('target', '_blank');

                card.innerHTML = `
                    <div class="chat-product-thumb">
                        <img src="${escapeHtml(p.product_image)}" alt="${escapeHtml(p.product_title)}" onerror="this.src='/assets/images/product01.png'">
                    </div>
                    <div class="chat-product-info">
                        <div class="chat-product-badges">
                            <span class="chat-badge-category">${escapeHtml(p.cat_title || 'Gadget')}</span>
                        </div>
                        <div class="chat-product-title" title="${escapeHtml(p.product_title)}">${escapeHtml(p.product_title)}</div>
                        <div class="chat-product-price">${escapeHtml(p.formatted_price)}</div>
                    </div>
                    <button type="button" class="chat-product-btn">Lihat</button>
                `;
                productsContainer.appendChild(card);
            });

            bubble.appendChild(productsContainer);
        }

        const timeSpan = document.createElement('span');
        timeSpan.className = 'message-time';
        timeSpan.textContent = time || getCurrentTime();

        row.appendChild(bubble);
        row.appendChild(timeSpan);

        // Sisipkan sebelum typing indicator
        if (typingIndicator) {
            chatBody.insertBefore(row, typingIndicator);
        } else {
            chatBody.appendChild(row);
        }

        scrollToBottom();
    }

    function setLoadingState(loading) {
        isWaitingResponse = loading;
        if (typingIndicator) {
            if (loading) {
                typingIndicator.classList.add('active');
            } else {
                typingIndicator.classList.remove('active');
            }
        }
        if (sendBtn && chatInput) {
            sendBtn.disabled = loading || !chatInput.value.trim();
        }
        scrollToBottom();
    }

    function scrollToBottom() {
        if (chatBody) {
            setTimeout(() => {
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 60);
        }
    }

    function getCurrentTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    function formatMarkdown(str) {
        if (!str) return '';
        let escaped = escapeHtml(str);

        // **tebal** -> <strong>tebal</strong>
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // *miring* -> <em>miring</em>
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // • baris poin -> bullet
        escaped = escaped.replace(/^• (.*?)$/gm, '• <strong>$1</strong>');

        // Line break ganda -> paragraf
        const paragraphs = escaped.split(/\n\s*\n/);
        return paragraphs
            .map(p => '<p>' + p.replace(/\n/g, '<br>') + '</p>')
            .join('');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function saveMessageToStorage(msgObj) {
        try {
            let history = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            history.push(msgObj);
            // Simpan maksimal 35 pesan terakhir agar performa tetap cepat
            if (history.length > 35) {
                history = history.slice(-35);
            }
            localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
        } catch (e) {
            console.warn('Storage save error:', e);
        }
    }

    function loadChatHistory() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;

            const history = JSON.parse(raw);
            if (Array.isArray(history) && history.length > 0) {
                // Sembunyikan welcome card jika sudah ada percakapan
                const welcomeCard = document.getElementById('nextech-welcome-card');
                if (welcomeCard) {
                    welcomeCard.style.display = 'none';
                }

                history.forEach(item => {
                    appendMessageRow(item.role, item.text, item.products, item.time);
                });
            }
        } catch (e) {
            console.warn('Storage load error:', e);
        }
    }

    function getRecentHistory() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return [];
            const history = JSON.parse(raw);
            return history.slice(-6).map(h => ({
                role: h.role === 'user' ? 'user' : 'assistant',
                content: h.text
            }));
        } catch (e) {
            return [];
        }
    }

    function clearHistory() {
        if (confirm('Hapus seluruh riwayat percakapan NexTech Assistant?')) {
            localStorage.removeItem(STORAGE_KEY);
            if (!chatBody) return;

            // Hapus semua baris pesan
            const rows = chatBody.querySelectorAll('.chat-message-row');
            rows.forEach(r => r.remove());

            // Tampilkan kembali welcome card
            const welcomeCard = document.getElementById('nextech-welcome-card');
            if (welcomeCard) {
                welcomeCard.style.display = 'block';
            }
            scrollToBottom();
        }
    }
})();
