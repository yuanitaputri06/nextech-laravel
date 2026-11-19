{{-- =========================================================
     NEXTECH AI CHATBOT WIDGET COMPONENT
     Floating Assistant with Navy, Blue, & White Design
     ========================================================= --}}

<link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}?v={{ time() }}">

<div id="nextech-chatbot-root"
     data-endpoint="{{ route('chatbot.message') }}"
     data-csrf="{{ csrf_token() }}">

    <!-- TOMBOL CHATBOT MENGAMBANG (POJOK KANAN BAWAH) -->
    <button type="button"
            id="nextech-chat-launcher"
            class="nextech-chat-launcher"
            aria-label="Buka Chat NexTech Assistant"
            title="Chat dengan NexTech Assistant">
        
        <span class="launcher-tooltip">Tanya NexTech Assistant 👋</span>
        <span class="launcher-badge"></span>

        <!-- Icon Chat -->
        <span class="launcher-icon-chat">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                <circle cx="9" cy="10" r="1" fill="currentColor"></circle>
                <circle cx="12" cy="10" r="1" fill="currentColor"></circle>
                <circle cx="15" cy="10" r="1" fill="currentColor"></circle>
            </svg>
        </span>

        <!-- Icon Close -->
        <span class="launcher-icon-close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </span>
    </button>

    <!-- JENDELA PERCAKAPAN NEXTECH ASSISTANT -->
    <div id="nextech-chat-window" class="nextech-chat-window" role="dialog" aria-modal="true" aria-label="Percakapan NexTech Assistant">
        
        <!-- HEADER -->
        <div class="chat-header">
            <div class="chat-header-brand">
                <div class="chat-avatar-wrapper">
                    🤖
                    <span class="chat-status-dot" title="Status: Online"></span>
                </div>
                <div class="chat-title-group">
                    <h3>Nex<span>Tech</span> Assistant</h3>
                    <p>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#10b981">
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>
                        Asisten Belanja AI • Online
                    </p>
                </div>
            </div>

            <div class="chat-header-actions">
                <button type="button"
                        id="nextech-chat-clear"
                        class="chat-action-btn"
                        title="Hapus Riwayat Percakapan"
                        aria-label="Hapus Riwayat">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>

                <button type="button"
                        id="nextech-chat-close"
                        class="chat-action-btn"
                        title="Tutup Chat"
                        aria-label="Tutup Percakapan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- BODY CHAT / PESAN -->
        <div id="nextech-chat-body" class="chat-body">
            
            <!-- KARTU SAMBUTAN DEFAULT -->
            <div id="nextech-welcome-card" class="chat-welcome-card">
                <span class="welcome-badge">💡 Asisten Belanja Pintar</span>
                <h4>Halo! Ada yang bisa saya bantu?</h4>
                <p>
                    Saya asisten virtual resmi <strong>NexTech</strong>. Tanyakan rekomendasi laptop, smartphone, spesifikasi gadget, atau sesuaikan dengan budget Anda.
                </p>

                <div class="chat-suggestions-title">
                    <span>✨ Rekomendasi Pertanyaan Cepat:</span>
                </div>

                <!-- TOMBOL REKOMENDASI PERTANYAAN (KLIK LANGSUNG) -->
                <div class="chat-suggestions-grid">
                    <button type="button" class="suggestion-chip" data-query="rekomendasi HP terkini">
                        📱 Rekomendasi HP Terkini
                    </button>
                    <button type="button" class="suggestion-chip" data-query="rekomendasi laptop mahasiswa">
                        💻 Laptop Mahasiswa
                    </button>
                    <button type="button" class="suggestion-chip" data-query="rekomendasi HP kamera terbaik">
                        📸 HP Kamera Terbaik
                    </button>
                    <button type="button" class="suggestion-chip" data-query="laptop di bawah 7 juta">
                        💰 Laptop di Bawah 7 Juta
                    </button>
                    <button type="button" class="suggestion-chip" data-query="produk populer terlaris">
                        🔥 Produk Populer
                    </button>
                    <button type="button" class="suggestion-chip" data-query="rekomendasi laptop gaming">
                        🎮 Laptop Gaming
                    </button>
                </div>
            </div>

            <!-- INDIKATOR TYPING / LOADING -->
            <div id="nextech-typing-indicator" class="chat-typing-indicator">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span>NexTech Assistant sedang mencari produk...</span>
            </div>

        </div>

        <!-- STRIP REKOMENDASI CEPAT (DI ATAS INPUT) -->
        <div class="chat-quick-strip">
            <button type="button" class="chat-quick-pill" data-query="rekomendasi HP terkini">📱 HP Terkini</button>
            <button type="button" class="chat-quick-pill" data-query="rekomendasi laptop mahasiswa">💻 Laptop Kuliah</button>
            <button type="button" class="chat-quick-pill" data-query="rekomendasi HP kamera terbaik">📸 Kamera Bagus</button>
            <button type="button" class="chat-quick-pill" data-query="laptop di bawah 7 juta">💰 Di Bawah 7 Jt</button>
            <button type="button" class="chat-quick-pill" data-query="rekomendasi laptop gaming">🎮 Laptop Gaming</button>
            <button type="button" class="chat-quick-pill" data-query="cara pemesanan produk">🛍️ Cara Beli</button>
        </div>

        <!-- FOOTER & FORM INPUT -->
        <div class="chat-footer">
            <form id="nextech-chat-form" class="chat-input-form">
                <input type="text"
                       id="nextech-chat-input"
                       class="chat-input-field"
                       placeholder="Tanyakan rekomendasi, budget, spek..."
                       autocomplete="off"
                       maxlength="1000">

                <button type="submit"
                        id="nextech-chat-send"
                        class="chat-send-btn"
                        title="Kirim Pesan"
                        disabled>
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>

            <div class="chat-footer-hint">
                Tekan <span>Enter</span> untuk kirim • Terhubung dengan database <span>NexTech</span>
            </div>
        </div>

    </div>

</div>

<script src="{{ asset('assets/js/chatbot.js') }}?v={{ time() }}" defer></script>
