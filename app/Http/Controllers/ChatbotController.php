<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Kirim pesan ke chatbot dan dapatkan balasan + rekomendasi produk.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $userMessage = trim($request->input('message'));
        $history = $request->input('history', []);

        // 1. Coba gunakan AI eksternal jika API key tersedia di .env / config
        $aiApiKey = config('services.gemini.key') ?: config('services.openai.key');

        if (!empty($aiApiKey)) {
            $aiResponse = $this->handleAiProvider($userMessage, $history);
            if ($aiResponse !== null) {
                return response()->json($aiResponse);
            }
        }

        // 2. Chatbot Cerdas Berbasis Aturan & Database Produk NexTech
        $response = $this->handleRuleBasedChat($userMessage);

        return response()->json($response);
    }

    /**
     * Dapatkan daftar pertanyaan cepat yang direkomendasikan.
     */
    public function getSuggestions()
    {
        return response()->json([
            'suggestions' => [
                [
                    'label' => '📱 Rekomendasi HP Terkini',
                    'query' => 'rekomendasi HP terkini',
                ],
                [
                    'label' => '💻 Laptop Mahasiswa',
                    'query' => 'rekomendasi laptop mahasiswa',
                ],
                [
                    'label' => '📸 HP Kamera Terbaik',
                    'query' => 'rekomendasi HP kamera terbaik',
                ],
                [
                    'label' => '💰 Laptop di Bawah 7 Juta',
                    'query' => 'laptop di bawah 7 juta',
                ],
                [
                    'label' => '🔥 Produk Populer',
                    'query' => 'produk populer terlaris',
                ],
                [
                    'label' => '🎮 Laptop Gaming',
                    'query' => 'rekomendasi laptop gaming',
                ],
            ],
        ]);
    }

    /**
     * Penanganan Chatbot Menggunakan AI Eksternal (Gemini / OpenAI) dengan Grounding Data Produk.
     */
    protected function handleAiProvider(string $userMessage, array $history): ?array
    {
        try {
            $catalog = $this->getCatalogContext();

            // Jika Gemini API Key tersedia
            if ($geminiKey = config('services.gemini.key')) {
                $model = config('services.gemini.model', 'gemini-1.5-flash');
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}";

                $systemInstruction = "Anda adalah NexTech Assistant, asisten belanja virtual resmi dari toko teknologi NexTech.
Gaya bicara Anda: ramah, antusias, solutif, sopan, dan menggunakan Bahasa Indonesia yang baik.
Tugas Anda adalah membantu pengunjung menemukan laptop, smartphone, dan gadget terbaik sesuai kebutuhan dan budget mereka.
Gunakan data katalog produk NexTech berikut ini untuk memberikan rekomendasi nyata:
{$catalog}

Pedoman:
1. Rekomendasikan hanya produk yang benar-benar ada di katalog NexTech jika relevan dengan pertanyaan pengunjung.
2. Jelaskan spesifikasi utama, harga (dalam format Rp), dan alasan produk tersebut cocok.
3. Gunakan formatting markdown seperti bold dan bullet points agar rapi dan enak dibaca.
4. Jika pengunjung bertanya hal umum seperti pembayaran, garansi, atau cara beli, jelaskan bahwa NexTech bergaransi resmi, melayani pengiriman seluruh Indonesia, serta mendukung transfer bank dan e-wallet.";

                $contents = [];
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $systemInstruction . "\n\nPertanyaan pengunjung: " . $userMessage]],
                ];

                $response = Http::timeout(10)->post($url, [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ],
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($reply)) {
                        // Cari produk terkait yang disebutkan dalam jawaban
                        $matchedProducts = $this->extractReferencedProducts($reply);

                        return [
                            'success' => true,
                            'reply' => $reply,
                            'products' => $matchedProducts,
                            'provider' => 'gemini',
                        ];
                    }
                }
            }

            // Jika OpenAI API Key tersedia
            if ($openAiKey = config('services.openai.key')) {
                $model = config('services.openai.model', 'gpt-4o-mini');
                $response = Http::withToken($openAiKey)
                    ->timeout(10)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => "Anda adalah NexTech Assistant, asisten belanja resmi toko teknologi NexTech. Data produk NexTech: {$catalog}. Rekomendasikan produk dari data tersebut dengan ramah dalam Bahasa Indonesia.",
                            ],
                            ['role' => 'user', 'content' => $userMessage],
                        ],
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $reply = $json['choices'][0]['message']['content'] ?? null;
                    if (!empty($reply)) {
                        $matchedProducts = $this->extractReferencedProducts($reply);

                        return [
                            'success' => true,
                            'reply' => $reply,
                            'products' => $matchedProducts,
                            'provider' => 'openai',
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AI Chatbot provider error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Chatbot Cerdas Alternatif Berbasis Aturan & Database Real-time NexTech.
     */
    protected function handleRuleBasedChat(string $userMessage): array
    {
        $normalized = strtolower($userMessage);
        $products = [];
        $reply = '';

        // 1. Rekomendasi Laptop Gaming
        if ($this->hasWords($normalized, ['gaming', 'game', 'rtx', 'gtx', 'tuf', 'rog', 'legion', 'nitro', 'victus'])) {
            $products = $this->fetchProducts(function ($query) {
                $query->where(function ($q) {
                    $q->where('products.product_title', 'like', '%gaming%')
                      ->orWhere('products.product_title', 'like', '%tuf%')
                      ->orWhere('products.product_title', 'like', '%rog%')
                      ->orWhere('products.product_title', 'like', '%legion%')
                      ->orWhere('products.product_title', 'like', '%nitro%')
                      ->orWhere('products.product_title', 'like', '%victus%')
                      ->orWhere('products.product_desc', 'like', '%gaming%')
                      ->orWhere('products.product_desc', 'like', '%rtx%')
                      ->orWhere('products.product_desc', 'like', '%gtx%')
                      ->orWhere('products.product_keywords', 'like', '%gaming%');
                });
            }, 4);

            if (empty($products)) {
                // Fallback ke laptop berspesifikasi tertinggi
                $products = $this->fetchProducts(function ($q) {
                    $q->where('categories.cat_title', 'like', '%laptop%')
                      ->orWhere('products.product_title', 'like', '%laptop%');
                }, 3, 'products.product_price', 'desc');
            }

            $reply = "Berikut adalah rekomendasi **Laptop Gaming & Performa Tinggi** yang tersedia di NexTech. Dilengkapi grafis bertenaga dan sistem pendingin mutakhir untuk pengalaman gaming serta rendering tanpa hambatan:";
        }

        // 2. Rekomendasi Laptop Mahasiswa / Pelajar / Budget Kuliah
        elseif ($this->hasWords($normalized, ['mahasiswa', 'kuliah', 'pelajar', 'sekolah', 'skripsi', 'office', 'kantor', 'tugas'])) {
            $products = $this->fetchProducts(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('categories.cat_title', 'like', '%laptop%')
                        ->orWhere('products.product_title', 'like', '%laptop%')
                        ->orWhere('products.product_keywords', 'like', '%laptop%');
                })->where('products.product_price', '<=', 9000000);
            }, 4, 'products.product_price', 'asc');

            if (empty($products)) {
                $products = $this->fetchProducts(function ($q) {
                    $q->where('categories.cat_title', 'like', '%laptop%')
                      ->orWhere('products.product_title', 'like', '%laptop%');
                }, 3, 'products.product_price', 'asc');
            }

            $reply = "Untuk kebutuhan **kuliah, tugas kampus, dan produktivitas mahasiswa**, ini rekomendasi laptop terbaik di NexTech. Memiliki bobot yang nyaman dibawa, baterai awet, dan harga ramah di kantong:";
        }

        // 3. Laptop di Bawah 7 Juta (Budget Filter)
        elseif ($this->hasWords($normalized, ['di bawah 7 juta', 'dibawah 7 juta', '7 juta', '7jt', '7.000.000', 'budget 7'])) {
            $products = $this->fetchProducts(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('categories.cat_title', 'like', '%laptop%')
                        ->orWhere('products.product_title', 'like', '%laptop%');
                })->where('products.product_price', '<=', 7000000);
            }, 4, 'products.product_price', 'desc');

            if (!empty($products)) {
                $reply = "Berikut pilihan **Laptop di Bawah Rp 7.000.000** yang tersedia di katalog NexTech. Sangat cocok untuk kebutuhan harian, streaming, dan multitasking ringan:";
            } else {
                // Tampilkan laptop termurah yang tersedia
                $products = $this->fetchProducts(function ($q) {
                    $q->where('categories.cat_title', 'like', '%laptop%')
                      ->orWhere('products.product_title', 'like', '%laptop%');
                }, 3, 'products.product_price', 'asc');

                $reply = "Saat ini laptop di bawah 7 juta sedang banyak diminati dan stoknya dinamis. Namun, berikut adalah **laptop pilihan paling terjangkau** yang paling mendekati budget Anda saat ini di NexTech:";
            }
        }

        // 4. Kamera / Fotografi
        elseif ($this->hasWords($normalized, ['kamera terbaik', 'kamera bagus', 'fotografi', 'foto', 'video', 'kamera'])) {

            // Deteksi apakah user menyebut HP/smartphone secara eksplisit
            $wantsPhone = $this->hasWords($normalized, ['hp', 'handphone', 'smartphone', 'ponsel']);

            if ($wantsPhone) {
                // User minta "HP kamera terbaik" → langsung cari HP, skip kamera asli
                $products = $this->fetchProducts(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('categories.cat_title', 'like', '%hp%')
                            ->orWhere('categories.cat_title', 'like', '%smartphone%')
                            ->orWhere('categories.cat_title', 'like', '%phone%')
                            ->orWhere('products.product_keywords', 'like', '%kamera%')
                            ->orWhere('products.product_keywords', 'like', '%camera%');
                    })->where(function ($sub2) {
                        $sub2->where('categories.cat_title', 'not like', '%laptop%')
                             ->where('categories.cat_title', 'not like', '%smartwatch%')
                             ->where('categories.cat_title', 'not like', '%wearable%')
                             ->where('categories.cat_title', 'not like', '%watch%')
                             ->where('categories.cat_title', 'not like', '%jam%')
                             ->where('products.product_title', 'not like', '%smartwatch%')
                             ->where('products.product_title', 'not like', '%watch%');
                    });
                }, 4, 'products.product_price', 'desc');

                $reply = "Berikut **smartphone dengan kamera terbaik** di NexTech yang cocok untuk foto, video, dan konten media sosial berkualitas tinggi:";

            } else {
                // User hanya sebut "kamera" → prioritas kamera asli (DSLR, mirrorless, dll.)
                // Khusus kategori kamera — Sony TIDAK dimasukkan agar tidak tabrakan dengan Sony HP
                $products = $this->fetchProducts(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('categories.cat_title', 'like', '%kamera%')
                            ->orWhere('categories.cat_title', 'like', '%camera%')
                            ->orWhere('categories.cat_title', 'like', '%dslr%')
                            ->orWhere('categories.cat_title', 'like', '%mirrorless%')
                            ->orWhere('products.product_title', 'like', '%canon%')
                            ->orWhere('products.product_title', 'like', '%nikon%')
                            ->orWhere('products.product_title', 'like', '%fujifilm%')
                            ->orWhere('products.product_title', 'like', '%gopro%');
                    });
                }, 4, 'products.product_price', 'desc');

                if (!empty($products)) {
                    $reply = "Berikut rekomendasi **kamera terbaik** di NexTech untuk kebutuhan foto dan video Anda:";
                } else {
                    // Fallback: tidak ada kamera asli → tampilkan HP kamera terbaik
                    $products = $this->fetchProducts(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('categories.cat_title', 'like', '%hp%')
                                ->orWhere('categories.cat_title', 'like', '%smartphone%')
                                ->orWhere('categories.cat_title', 'like', '%phone%')
                                ->orWhere('products.product_keywords', 'like', '%kamera%')
                                ->orWhere('products.product_keywords', 'like', '%camera%');
                        })->where(function ($sub2) {
                            $sub2->where('categories.cat_title', 'not like', '%laptop%')
                                 ->where('categories.cat_title', 'not like', '%smartwatch%')
                                 ->where('categories.cat_title', 'not like', '%wearable%')
                                 ->where('categories.cat_title', 'not like', '%watch%')
                                 ->where('categories.cat_title', 'not like', '%jam%')
                                 ->where('products.product_title', 'not like', '%smartwatch%')
                                 ->where('products.product_title', 'not like', '%watch%');
                        });
                    }, 4, 'products.product_price', 'desc');

                    $reply = "Kami belum memiliki kategori kamera khusus, namun berikut **smartphone dengan kamera unggulan** di NexTech:";
                }
            }
        }

        // 5. Rekomendasi HP Terkini / Smartphone Terbaru
        elseif ($this->hasWords($normalized, ['hp terkini', 'hp terbaru', 'smartphone terkini', 'smartphone terbaru', 'hp', 'smartphone', 'handphone', 'ponsel'])) {
            $products = $this->fetchProducts(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('categories.cat_title', 'like', '%hp%')
                        ->orWhere('categories.cat_title', 'like', '%smartphone%')
                        ->orWhere('categories.cat_title', 'like', '%phone%')
                        ->orWhere('products.product_title', 'like', '%samsung%')
                        ->orWhere('products.product_title', 'like', '%xiaomi%')
                        ->orWhere('products.product_title', 'like', '%redmi%')
                        ->orWhere('products.product_title', 'like', '%iphone%')
                        ->orWhere('products.product_title', 'like', '%oppo%')
                        ->orWhere('products.product_title', 'like', '%vivo%');
                })->where(function ($sub2) {
                    // Eksklusi: bukan laptop, bukan smartwatch/wearable
                    $sub2->where('categories.cat_title', 'not like', '%laptop%')
                         ->where('categories.cat_title', 'not like', '%smartwatch%')
                         ->where('categories.cat_title', 'not like', '%wearable%')
                         ->where('categories.cat_title', 'not like', '%watch%')
                         ->where('categories.cat_title', 'not like', '%jam%')
                         ->where('products.product_title', 'not like', '%smartwatch%')
                         ->where('products.product_title', 'not like', '%watch%');
                });
            }, 4, 'products.product_id', 'desc');

            $reply = "Berikut adalah **rekomendasi smartphone terkini** di NexTech. Menawarkan performa mulus, layar responsif, dan baterai tahan lama:";
        }

        // 6. Produk Populer / Terlaris
        elseif ($this->hasWords($normalized, ['populer', 'terlaris', 'paling laku', 'best seller', 'favorit', 'rekomendasi produk'])) {
            $products = $this->fetchProducts(null, 4, 'products.product_id', 'desc');
            $reply = "Berikut adalah **produk terpopuler dan paling banyak diminati** pelanggan setia NexTech minggu ini:";
        }

        // 7. Pencarian Berdasarkan Merk / Brand
        elseif ($brand = $this->detectBrand($normalized)) {
            $products = $this->fetchProducts(function ($q) use ($brand) {
                $q->where('brands.brand_title', 'like', "%{$brand}%")
                  ->orWhere('products.product_title', 'like', "%{$brand}%")
                  ->orWhere('products.product_desc', 'like', "%{$brand}%");
            }, 4, 'products.product_id', 'desc');

            $brandName = ucfirst($brand);
            if (!empty($products)) {
                $reply = "Berikut produk resmi dari brand **{$brandName}** yang tersedia di etalase NexTech:";
            } else {
                $reply = "Mohon maaf, stok produk dengan merk **{$brandName}** sedang dalam proses pembaruan. Anda dapat melihat beberapa produk pilihan lainnya berikut ini:";
                $products = $this->fetchProducts(null, 3);
            }
        }

        // 8. Pencarian Kategori: Headphone / Earphone / Audio
        elseif ($this->hasWords($normalized, ['headphone', 'headset', 'earphone', 'tws', 'earbud', 'audio', 'speaker', 'sony headphone'])) {
            $products = $this->fetchProducts(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('categories.cat_title', 'like', '%headphone%')
                        ->orWhere('categories.cat_title', 'like', '%audio%')
                        ->orWhere('products.product_title', 'like', '%headphone%')
                        ->orWhere('products.product_title', 'like', '%earphone%')
                        ->orWhere('products.product_title', 'like', '%earbuds%')
                        ->orWhere('products.product_title', 'like', '%tws%')
                        ->orWhere('products.product_title', 'like', '%speaker%')
                        ->orWhere('products.product_keywords', 'like', '%headphone%')
                        ->orWhere('products.product_keywords', 'like', '%audio%');
                });
            }, 4, 'products.product_id', 'desc');

            if (!empty($products)) {
                $reply = "Berikut pilihan **Headphone, Earphone & Audio** terbaik di NexTech untuk pengalaman mendengarkan musik dan gaming yang lebih imersif:";
            } else {
                $reply = "Koleksi audio NexTech terus diperbarui. Berikut beberapa produk pilihan yang mungkin kamu suka:";
                $products = $this->fetchProducts(null, 3);
            }
        }

        // 9. Pencarian Kategori: Aksesori / Peripheral (mouse, keyboard, dll.)
        elseif ($this->hasWords($normalized, ['aksesori', 'aksesoris', 'accessories', 'peripheral', 'mouse', 'keyboard', 'casing', 'charger', 'kabel', 'hub', 'webcam', 'joystick', 'gamepad', 'cooling pad'])) {
            $products = $this->fetchProducts(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('categories.cat_title', 'like', '%aksesori%')
                        ->orWhere('categories.cat_title', 'like', '%aksesoris%')
                        ->orWhere('categories.cat_title', 'like', '%accessories%')
                        ->orWhere('categories.cat_title', 'like', '%peripheral%')
                        ->orWhere('products.product_title', 'like', '%mouse%')
                        ->orWhere('products.product_title', 'like', '%keyboard%')
                        ->orWhere('products.product_title', 'like', '%charger%')
                        ->orWhere('products.product_title', 'like', '%kabel%')
                        ->orWhere('products.product_title', 'like', '%hub%')
                        ->orWhere('products.product_title', 'like', '%webcam%')
                        ->orWhere('products.product_title', 'like', '%gamepad%')
                        ->orWhere('products.product_title', 'like', '%joystick%')
                        ->orWhere('products.product_keywords', 'like', '%aksesori%')
                        ->orWhere('products.product_keywords', 'like', '%peripheral%');
                });
            }, 4, 'products.product_id', 'desc');

            if (!empty($products)) {
                $reply = "Berikut pilihan **aksesori & peripheral** yang tersedia di NexTech untuk melengkapi setup teknologi Anda:";
            } else {
                $reply = "Kategori aksesori di NexTech terus diperbarui. Sementara itu, berikut beberapa produk pilihan yang mungkin Anda sukai:";
                $products = $this->fetchProducts(null, 3);
            }
        }

        // 9. FAQ: Cara Beli / Pemesanan / Checkout
        elseif ($this->hasWords($normalized, ['cara beli', 'cara pesan', 'order', 'pesan', 'checkout', 'pembelian'])) {
            $reply = "Cara berbelanja di **NexTech** sangat mudah! 🛍️\n\n" .
                     "1. **Pilih Produk:** Cari produk yang Anda inginkan lewat website atau rekomendasi asisten ini.\n" .
                     "2. **Klik Beli / Detail:** Buka halaman produk untuk melihat spesifikasi lengkap dan klik **Beli Sekarang**.\n" .
                     "3. **Login / Daftar:** Masuk dengan akun NexTech Anda atau registrasi akun baru dalam 1 menit.\n" .
                     "4. **Pembayaran & Pengiriman:** Isi alamat lengkap dan pilih metode pembayaran favorit Anda.\n\n" .
                     "Ada produk gadget yang sedang Anda cari saat ini? Silakan tanyakan!";
            $products = $this->fetchProducts(null, 2);
        }

        // 10. FAQ: Metode Pembayaran
        elseif ($this->hasWords($normalized, ['bayar', 'pembayaran', 'transfer', 'metode bayar', 'qris', 'rekening'])) {
            $reply = "NexTech mendukung berbagai metode pembayaran yang aman dan praktis:\n\n" .
                     "💳 **Transfer Bank Virtual Account:** BCA, Mandiri, BNI, BRI, Permata\n" .
                     "📱 **E-Wallet & QRIS:** GoPay, OVO, Dana, LinkAja, ShopeePay\n" .
                     "🔒 **Sistem Terverifikasi:** Setiap pembayaran diproses otomatis dengan verifikasi instan.";
        }

        // 11. FAQ: Garansi & Keaslian Produk
        elseif ($this->hasWords($normalized, ['garansi', 'asli', 'ori', 'original', 'resmi', 'rusak', 'retur'])) {
            $reply = "🛡️ **Jaminan 100% Produk Original & Bergaransi Resmi di NexTech**:\n\n" .
                     "• Seluruh unit produk kami dijamin **100% Original & Baru** (Brand New in Box).\n" .
                     "• Didukung garansi resmi pabrikan/distributor terpercaya (1 hingga 2 tahun).\n" .
                     "• Jaminan retur/tukar baru jika barang diterima dengan cacat produksi (S&K berlaku).";
        }

        // 12. Salam / Greeting
        elseif ($this->hasWords($normalized, ['halo', 'hai', 'hello', 'hi', 'pagi', 'siang', 'sore', 'malam', 'assalamualaikum'])) {
            $reply = "Halo! 👋 Selamat datang di **NexTech Assistant**.\n\n" .
                     "Saya siap membantu Anda menemukan laptop, smartphone, dan perlengkapan teknologi terbaik sesuai kebutuhan dan budget Anda.\n\n" .
                     "Silakan pilih tombol rekomendasi cepat di bawah atau langsung ketik gadget yang Anda inginkan!";
            $products = $this->fetchProducts(null, 3);
        }

        // 13. Pertanyaan Siapa Bot Ini
        elseif ($this->hasWords($normalized, ['siapa kamu', 'siapa anda', 'apa itu nextech', 'tentang nextech'])) {
            $reply = "Saya adalah **NexTech Assistant**, asisten AI virtual pintar yang terhubung langsung dengan katalog dan inventaris produk **NexTech**. Saya bertugas memberikan rekomendasi spesifikasi, membandingkan gadget, dan mempermudah pengalaman belanja teknologi Anda.";
        }

        // 14. Pencarian Berdasarkan Kata Kunci Umum di Database
        else {
            $keywords = $this->extractMeaningfulKeywords($normalized);

            if (!empty($keywords)) {
                $products = $this->fetchProducts(function ($q) use ($keywords) {
                    $q->where(function ($sub) use ($keywords) {
                        foreach ($keywords as $kw) {
                            $sub->orWhere('products.product_title', 'like', "%{$kw}%")
                                ->orWhere('products.product_desc', 'like', "%{$kw}%")
                                ->orWhere('products.product_keywords', 'like', "%{$kw}%")
                                ->orWhere('categories.cat_title', 'like', "%{$kw}%")
                                ->orWhere('brands.brand_title', 'like', "%{$kw}%");
                        }
                    });
                }, 4);
            }

            if (!empty($products)) {
                $reply = "Berdasarkan pencarian Anda, berikut produk di NexTech yang paling sesuai:";
            } else {
                $reply = "Terima kasih telah bertanya! Saya belum menemukan produk yang persis dengan kata kunci tersebut. Namun, jangan ragu untuk melihat rekomendasi produk unggulan kami di bawah, atau gunakan pertanyaan cepat yang tersedia:";
                $products = $this->fetchProducts(null, 3);
            }
        }

        return [
            'success' => true,
            'reply' => $reply,
            'products' => $products,
            'provider' => 'local_rule_engine',
        ];
    }

    /**
     * Ambil data produk real-time dari database dengan fallback aman.
     */
    protected function fetchProducts(?callable $filterCallback = null, int $limit = 4, string $orderBy = 'products.product_id', string $orderDir = 'desc'): array
    {
        try {
            $query = DB::table('products')
                ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
                ->leftJoin('brands', 'products.product_brand', '=', 'brands.brand_id')
                ->select(
                    'products.product_id',
                    'products.product_title',
                    'products.product_price',
                    'products.product_desc',
                    'products.product_image',
                    'products.product_keywords',
                    'categories.cat_title',
                    'brands.brand_title'
                );

            if ($filterCallback !== null) {
                $filterCallback($query);
            }

            $items = $query->orderBy($orderBy, $orderDir)->limit($limit)->get();

            $formatted = [];
            foreach ($items as $item) {
                $formatted[] = $this->formatProductItem($item);
            }

            return $formatted;
        } catch (\Throwable $e) {
            Log::warning('Database product fetch error in ChatbotController: ' . $e->getMessage());

            // Kembalikan produk fallback jika koneksi database sedang offline
            return $this->getStaticFallbackProducts($limit);
        }
    }

    /**
     * Format item produk agar siap dikonsumsi oleh widget UI.
     */
    protected function formatProductItem($item): array
    {
        $imageName = $item->product_image ?? 'product01.png';
        $imageUrl = asset('assets/images/' . $imageName);

        // Periksa apakah file ada di assets/images, assets/img, atau default
        if (!file_exists(public_path('assets/images/' . $imageName))) {
            if (file_exists(public_path('assets/img/' . $imageName))) {
                $imageUrl = asset('assets/img/' . $imageName);
            } elseif (file_exists(public_path('storage/' . $imageName))) {
                $imageUrl = asset('storage/' . $imageName);
            } else {
                $imageUrl = asset('assets/images/product01.png');
            }
        }

        return [
            'product_id' => $item->product_id,
            'product_title' => $item->product_title,
            'product_price' => (int) $item->product_price,
            'formatted_price' => 'Rp ' . number_format($item->product_price, 0, ',', '.'),
            'product_desc' => Str::limit($item->product_desc ?? 'Produk teknologi berkualitas tinggi dari NexTech.', 95),
            'product_image' => $imageUrl,
            'cat_title' => $item->cat_title ?? 'Gadget',
            'brand_title' => $item->brand_title ?? 'NexTech',
            'url' => url('/product/' . $item->product_id),
        ];
    }

    /**
     * Produk statis fallback jika database MySQL belum aktif / error koneksi.
     */
    protected function getStaticFallbackProducts(int $limit = 3): array
    {
        $fallbacks = [
            [
                'product_id' => 1,
                'product_title' => 'ASUS TUF Gaming A15',
                'product_price' => 12499000,
                'formatted_price' => 'Rp 12.499.000',
                'product_desc' => 'Laptop gaming bertenaga AMD Ryzen dan kartu grafis RTX untuk performa gaming maksimal.',
                'product_image' => asset('assets/images/product01.png'),
                'cat_title' => 'Laptop',
                'brand_title' => 'ASUS',
                'url' => url('/products'),
            ],
            [
                'product_id' => 2,
                'product_title' => 'Samsung Galaxy A55 5G',
                'product_price' => 5999000,
                'formatted_price' => 'Rp 5.999.000',
                'product_desc' => 'Smartphone dengan kamera OIS jernih, layar Super AMOLED 120Hz, dan baterai tahan lama.',
                'product_image' => asset('assets/images/product02.png'),
                'cat_title' => 'Smartphone',
                'brand_title' => 'Samsung',
                'url' => url('/products'),
            ],
            [
                'product_id' => 3,
                'product_title' => 'Lenovo IdeaPad Slim 3',
                'product_price' => 6499000,
                'formatted_price' => 'Rp 6.499.000',
                'product_desc' => 'Laptop tipis dan ringan sangat cocok untuk mahasiswa dan profesional muda.',
                'product_image' => asset('assets/images/product03.png'),
                'cat_title' => 'Laptop',
                'brand_title' => 'Lenovo',
                'url' => url('/products'),
            ],
        ];

        return array_slice($fallbacks, 0, $limit);
    }

    /**
     * Konteks katalog ringkas untuk dimasukkan ke prompt AI eksternal.
     */
    protected function getCatalogContext(): string
    {
        try {
            $products = DB::table('products')
                ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
                ->leftJoin('brands', 'products.product_brand', '=', 'brands.brand_id')
                ->select(
                    'products.product_id',
                    'products.product_title',
                    'products.product_price',
                    'categories.cat_title',
                    'brands.brand_title'
                )
                ->limit(15)
                ->get();

            $lines = [];
            foreach ($products as $p) {
                $lines[] = "- [ID {$p->product_id}] {$p->product_title} (Merk: {$p->brand_title}, Kategori: {$p->cat_title}, Harga: Rp " . number_format($p->product_price, 0, ',', '.') . ")";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            return "- ASUS TUF Gaming A15 (Laptop, Rp 12.499.000)\n- Samsung Galaxy A55 5G (Smartphone, Rp 5.999.000)\n- Lenovo IdeaPad Slim 3 (Laptop, Rp 6.499.000)";
        }
    }

    /**
     * Cocokkan produk yang mungkin disebut dalam jawaban teks AI.
     */
    protected function extractReferencedProducts(string $text): array
    {
        $textLower = strtolower($text);
        try {
            $products = DB::table('products')
                ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
                ->leftJoin('brands', 'products.product_brand', '=', 'brands.brand_id')
                ->select('products.*', 'categories.cat_title', 'brands.brand_title')
                ->limit(10)
                ->get();

            $matched = [];
            foreach ($products as $p) {
                if (stripos($textLower, strtolower($p->product_title)) !== false ||
                    (!empty($p->brand_title) && stripos($textLower, strtolower($p->brand_title)) !== false && count($matched) < 2)) {
                    $matched[] = $this->formatProductItem($p);
                }
                if (count($matched) >= 3) {
                    break;
                }
            }

            return $matched;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Deteksi apakah teks memuat salah satu kata kunci.
     */
    protected function hasWords(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, strtolower($needle))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Deteksi merk / brand dari teks pertanyaan.
     */
    protected function detectBrand(string $text): ?string
    {
        $brands = [
            'asus', 'lenovo', 'samsung', 'apple', 'iphone', 'ipad',
            'xiaomi', 'redmi', 'acer', 'hp', 'dell', 'msi', 'oppo',
            'vivo', 'realme', 'huawei', 'sony', 'infinix',
        ];

        foreach ($brands as $b) {
            if (preg_match('/\b' . preg_quote($b, '/') . '\b/i', $text)) {
                return $b;
            }
        }

        return null;
    }

    /**
     * Ambil kata kunci esensial dari pertanyaan user dengan membuang stop words bahasa Indonesia.
     */
    protected function extractMeaningfulKeywords(string $text): array
    {
        $stopwords = [
            'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'pada', 'dengan',
            'adalah', 'ini', 'itu', 'atau', 'ada', 'bisa', 'tolong', 'cari',
            'rekomendasi', 'mau', 'dong', 'apa', 'saja', 'saya', 'kamu',
            'bagus', 'terbaik', 'pilihan', 'buat', 'kak', 'min', 'produk',
        ];

        $words = preg_split('/[\s,\.\?!]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $clean = [];

        foreach ($words as $w) {
            if (strlen($w) >= 3 && !in_array($w, $stopwords)) {
                $clean[] = $w;
            }
        }

        return array_slice(array_unique($clean), 0, 4);
    }
}
