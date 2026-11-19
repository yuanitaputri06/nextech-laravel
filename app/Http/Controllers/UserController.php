<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Review;
use App\Services\MidtransService;

class UserController extends Controller
{
    /**
     * Dashboard / Beranda User setelah login
     */
    public function dashboard()
    {
        if (!session('is_user')) {
            return redirect()->route('user.login');
        }

        $products = Product::orderBy('product_id', 'desc')->limit(8)->get();
        $categories = DB::table('categories')->orderBy('cat_title')->get();
        $user = DB::table('user_info')->where('user_id', session('user_id'))->first();

        if (!$user) {
            session()->forget(['user_id', 'user_name', 'user_email', 'is_user']);
            return redirect()->route('login')->with('error', 'Data pengguna tidak ditemukan.');
        }

        return view('user.dashboard', compact('products', 'categories', 'user'));
    }

    /**
     * Menu Pesanan Saya (Menggabungkan Tab Keranjang & Tab Riwayat Pesanan)
     */
    public function orders(Request $request)
    {
        if (!session('is_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');
        $activeTab = $request->query('tab', 'cart'); // default 'cart' atau 'orders'

        // 1. Data Keranjang Belanja User
        $cartItems = DB::table('cart')
            ->join('products', 'cart.p_id', '=', 'products.product_id')
            ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
            ->select(
                'cart.id as cart_id',
                'cart.qty',
                'products.product_id',
                'products.product_title',
                'products.product_price',
                'products.product_image',
                'products.stock',
                'categories.cat_title'
            )
            ->where('cart.user_id', $userId)
            ->orderBy('cart.id', 'desc')
            ->get();

        // 2. Data Riwayat Pesanan User
        $orders = DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.product_id')
            ->select(
                'orders.order_id',
                'orders.trx_id',
                'orders.qty',
                'orders.p_status',
                'orders.order_status',
                'orders.payment_method',
                'orders.total_amt',
                'orders.snap_token',
                'orders.midtrans_status',
                'orders.midtrans_payment_type',
                'orders.midtrans_transaction_id',
                'orders.paid_at',
                'orders.courier_name',
                'orders.tracking_number',
                'orders.shipping_notes',
                'orders.processed_at',
                'orders.shipped_at',
                'orders.completed_at',
                'orders.created_at',
                'products.product_id',
                'products.product_title',
                'products.product_price',
                'products.product_image'
            )
            ->where('orders.user_id', $userId)
            ->orderBy('orders.order_id', 'desc')
            ->get();

        $user = DB::table('user_info')->where('user_id', $userId)->first();

        return view('user.orders', compact('cartItems', 'orders', 'activeTab', 'user'));
    }

    /**
     * Menu Wishlist User
     */
    public function wishlist()
    {
        if (!session('is_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $wishlists = DB::table('wishlists')
            ->join('products', 'wishlists.product_id', '=', 'products.product_id')
            ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
            ->select(
                'wishlists.id as wishlist_id',
                'wishlists.created_at as added_at',
                'products.product_id',
                'products.product_title',
                'products.product_price',
                'products.product_desc',
                'products.product_image',
                'products.stock',
                'categories.cat_title'
            )
            ->where('wishlists.user_id', $userId)
            ->orderBy('wishlists.id', 'desc')
            ->get();

        return view('user.wishlist', compact('wishlists'));
    }

    /**
     * Toggle Wishlist (AJAX atau Form)
     */
    public function toggleWishlist(Request $request)
    {
        if (!session('is_user')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'redirect' => route('login'),
                    'message' => 'Silakan login terlebih dahulu untuk menyimpan ke Wishlist.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $userId = session('user_id');
        $productId = (int) $request->input('product_id');

        $existing = DB::table('wishlists')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            DB::table('wishlists')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();

            $inWishlist = false;
            $message = 'Produk berhasil dihapus dari Wishlist.';
        } else {
            DB::table('wishlists')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inWishlist = true;
            $message = 'Produk berhasil ditambahkan ke Wishlist!';
        }

        $totalWishlist = DB::table('wishlists')->where('user_id', $userId)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'count' => $totalWishlist,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Tambah Produk ke Keranjang (AJAX atau Form)
     */
    public function addToCart(Request $request)
    {
        if (!session('is_user')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'redirect' => route('login'),
                    'message' => 'Silakan login terlebih dahulu untuk memasukkan barang ke keranjang.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'product_id' => 'required|integer',
            'qty' => 'nullable|integer|min:1',
        ]);

        $userId = session('user_id');
        $productId = (int) $request->input('product_id');
        $qtyToAdd = (int) $request->input('qty', 1);

        $product = DB::table('products')->where('product_id', $productId)->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        $stock = (int) ($product->stock ?? 0);
        if ($stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, stok produk ini sedang habis.'
            ], 422);
        }

        // Cek item di cart
        $cartItem = DB::table('cart')
            ->where('user_id', $userId)
            ->where('p_id', $productId)
            ->first();

        $currentQty = $cartItem ? (int) $cartItem->qty : 0;
        $newQty = $currentQty + $qtyToAdd;

        if ($newQty > $stock) {
            return response()->json([
                'success' => false,
                'message' => "Jumlah pembelian total ({$newQty} unit) melebihi stok yang tersedia ({$stock} unit)."
            ], 422);
        }

        if ($cartItem) {
            DB::table('cart')
                ->where('id', $cartItem->id)
                ->update(['qty' => $newQty]);
        } else {
            DB::table('cart')->insert([
                'p_id' => $productId,
                'ip_add' => $request->ip() ?? '127.0.0.1',
                'user_id' => $userId,
                'qty' => $qtyToAdd,
            ]);
        }

        $totalCartCount = (int) DB::table('cart')->where('user_id', $userId)->sum('qty');

        return response()->json([
            'success' => true,
            'count' => $totalCartCount,
            'message' => "Berhasil menambahkan {$qtyToAdd} unit ke keranjang belanja!",
        ]);
    }

    /**
     * Update Jumlah Item di Keranjang
     */
    public function updateCart(Request $request)
    {
        if (!session('is_user')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'cart_id' => 'required|integer',
            'qty' => 'required|integer|min:1',
        ]);

        $userId = session('user_id');
        $cartId = (int) $request->input('cart_id');
        $newQty = (int) $request->input('qty');

        $cartItem = DB::table('cart')
            ->join('products', 'cart.p_id', '=', 'products.product_id')
            ->where('cart.id', $cartId)
            ->where('cart.user_id', $userId)
            ->select('cart.*', 'products.stock', 'products.product_price')
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item keranjang tidak ditemukan.'], 404);
        }

        $stock = (int) ($cartItem->stock ?? 0);
        if ($newQty > $stock) {
            return response()->json([
                'success' => false,
                'message' => "Jumlah ({$newQty}) melebihi stok tersedia ({$stock} unit).",
                'max_stock' => $stock
            ], 422);
        }

        DB::table('cart')->where('id', $cartId)->update(['qty' => $newQty]);

        $subtotal = $newQty * (int) $cartItem->product_price;
        $totalCartCount = (int) DB::table('cart')->where('user_id', $userId)->sum('qty');

        return response()->json([
            'success' => true,
            'qty' => $newQty,
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'count' => $totalCartCount,
            'message' => 'Jumlah berhasil diperbarui.'
        ]);
    }

    /**
     * Hapus Item dari Keranjang
     */
    public function removeFromCart($id)
    {
        if (!session('is_user')) {
            return redirect()->route('login');
        }

        $userId = session('user_id');
        DB::table('cart')->where('id', $id)->where('user_id', $userId)->delete();

        return redirect()->route('user.orders', ['tab' => 'cart'])
            ->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * Halaman Checkout
     */
    public function checkout(Request $request)
    {
        if (!session('is_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk checkout.');
        }

        $userId = session('user_id');
        $user = DB::table('user_info')->where('user_id', $userId)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        $checkoutItems = collect();
        $isDirect = false;

        // Cek jika direct buy (Beli Sekarang dari halaman detail)
        $directProductId = $request->query('product_id');
        $directQty = (int) $request->query('qty', 1);

        if ($directProductId) {
            $product = DB::table('products')->where('product_id', $directProductId)->first();
            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }

            $stock = (int) ($product->stock ?? 0);
            if ($stock <= 0) {
                return redirect()->route('products.show', $directProductId)->with('error', 'Maaf, stok produk habis.');
            }
            if ($directQty > $stock) {
                $directQty = $stock;
            }

            $checkoutItems->push((object) [
                'cart_id' => null,
                'product_id' => $product->product_id,
                'product_title' => $product->product_title,
                'product_price' => (int) $product->product_price,
                'product_image' => $product->product_image,
                'stock' => $stock,
                'qty' => $directQty,
                'subtotal' => $directQty * (int) $product->product_price,
            ]);
            $isDirect = true;
        } else {
            // Checkout dari keranjang
            $selectedCartIds = $request->input('selected_items', []);

            // Jika dipanggil via GET tanpa selected_items, ambil semua item keranjang
            $query = DB::table('cart')
                ->join('products', 'cart.p_id', '=', 'products.product_id')
                ->where('cart.user_id', $userId)
                ->select(
                    'cart.id as cart_id',
                    'cart.qty',
                    'products.product_id',
                    'products.product_title',
                    'products.product_price',
                    'products.product_image',
                    'products.stock'
                );

            if (!empty($selectedCartIds) && is_array($selectedCartIds)) {
                $query->whereIn('cart.id', $selectedCartIds);
            }

            $items = $query->get();

            if ($items->isEmpty()) {
                return redirect()->route('user.orders', ['tab' => 'cart'])
                    ->with('error', 'Pilih minimal satu produk untuk di-checkout.');
            }

            foreach ($items as $item) {
                $stock = (int) ($item->stock ?? 0);
                $qty = (int) $item->qty;
                if ($qty > $stock) {
                    $qty = $stock;
                }

                $checkoutItems->push((object) [
                    'cart_id' => $item->cart_id,
                    'product_id' => $item->product_id,
                    'product_title' => $item->product_title,
                    'product_price' => (int) $item->product_price,
                    'product_image' => $item->product_image,
                    'stock' => $stock,
                    'qty' => $qty,
                    'subtotal' => $qty * (int) $item->product_price,
                ]);
            }
        }

        $totalAmount = $checkoutItems->sum('subtotal');

        return view('user.checkout', compact('user', 'checkoutItems', 'totalAmount', 'isDirect'));
    }

    /**
     * Proses Checkout / Pembayaran
     */
    public function processCheckout(Request $request)
    {
        if (!session('is_user')) {
            return redirect()->route('login');
        }

        $userId = session('user_id');

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'address' => 'required|string',
            'payment_method' => 'required|string|in:Midtrans',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.cart_id' => 'nullable|integer',
        ]);

        $user = DB::table('user_info')->where('user_id', $userId)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Verifikasi item dan hitung total murni dari database (tidak mempercayai nominal frontend)
        $itemsData = [];
        $totalAmount = 0;

        foreach ($validated['items'] as $item) {
            $product = DB::table('products')->where('product_id', $item['product_id'])->first();
            if (!$product) {
                return back()->withInput()->with('error', 'Salah satu produk tidak ditemukan.');
            }

            $stock = (int) ($product->stock ?? 0);
            $qty = (int) $item['qty'];

            if ($qty > $stock) {
                return back()->withInput()->with('error', "Stok {$product->product_title} tidak mencukupi (tersisa {$stock} unit).");
            }

            $price = (int) $product->product_price;
            $subtotal = $qty * $price;
            $totalAmount += $subtotal;

            $itemsData[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $subtotal,
                'cart_id' => $item['cart_id'] ?? null,
            ];
        }

        $trxId = 'NX-' . strtoupper(uniqid());
        $snapToken = null;
        $isMidtrans = ($validated['payment_method'] === 'Midtrans');

        // 2. Jika metode pembayaran Midtrans, buat Snap Token terlebih dahulu
        if ($isMidtrans) {
            $midtransService = app(MidtransService::class);

            if (!$midtransService->isConfigured()) {
                return back()->withInput()->with('error', 'Kredensial Midtrans Sandbox belum dikonfigurasi di file .env (MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY). Harap isi konfigurasi atau pilih metode Bayar di Tempat (COD) untuk sementara.');
            }

            $midtransItems = [];
            foreach ($itemsData as $item) {
                $midtransItems[] = [
                    'id' => (string) $item['product']->product_id,
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'name' => (string) $item['product']->product_title,
                ];
            }

            $customerDetails = [
                'first_name' => $user->first_name ?? 'Pelanggan',
                'last_name' => $user->last_name ?? '',
                'recipient_name' => $validated['recipient_name'],
                'email' => $user->email ?? 'customer@example.com',
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ];

            try {
                $snapToken = $midtransService->createSnapToken($trxId, $totalAmount, $midtransItems, $customerDetails);
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Gagal membuat sesi pembayaran Midtrans: ' . $e->getMessage());
            }
        }

        // 3. Database Transaction untuk simpan pesanan dan kurangi stok
        DB::beginTransaction();
        try {
            foreach ($itemsData as $item) {
                $productLocked = DB::table('products')->where('product_id', $item['product']->product_id)->lockForUpdate()->first();
                $stockNow = (int) ($productLocked->stock ?? 0);

                if ($item['qty'] > $stockNow) {
                    throw new \Exception("Stok {$item['product']->product_title} baru saja habis (tersisa {$stockNow} unit).");
                }

                // Insert ke orders
                DB::table('orders')->insertGetId([
                    'user_id' => $userId,
                    'product_id' => $item['product']->product_id,
                    'qty' => $item['qty'],
                    'trx_id' => $trxId,
                    'p_status' => 'Pending',
                    'order_status' => $isMidtrans ? 'menunggu_pembayaran' : 'pembayaran_berhasil',
                    'payment_method' => $validated['payment_method'],
                    'snap_token' => $snapToken,
                    'midtrans_status' => $isMidtrans ? 'pending' : null,
                    'total_amt' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Kurangi stok
                DB::table('products')->where('product_id', $item['product']->product_id)->decrement('stock', $item['qty']);

                // Hapus dari keranjang jika ada
                if (!empty($item['cart_id'])) {
                    DB::table('cart')->where('id', $item['cart_id'])->where('user_id', $userId)->delete();
                }
            }

            DB::commit();

            // Catat riwayat status pesanan pertama kali
            try {
                $orderService = app(\App\Services\OrderService::class);
                $customerName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: (session('user_name') ?? 'Pelanggan');

                if ($isMidtrans) {
                    $orderService->recordHistory(
                        trxId: $trxId,
                        status: 'menunggu_pembayaran',
                        title: 'Pesanan Dibuat',
                        description: "Pesanan #{$trxId} berhasil dibuat. Menunggu konfirmasi pembayaran via Midtrans Snap.",
                        actorType: 'user',
                        actorId: $userId,
                        actorName: $customerName
                    );
                }
            } catch (\Throwable $th) {
                // Jangan menggagalkan pesanan jika logging history bermasalah
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }

        // Jika metode Midtrans, arahkan ke halaman pembayaran Snap
        if ($isMidtrans) {
            return redirect()->route('user.payment', ['trx_id' => $trxId]);
        }

        return redirect()->route('user.orders', ['tab' => 'orders'])
            ->with('success', "Pesanan #{$trxId} berhasil dibuat! Terima kasih telah berbelanja di NexTech.");
    }

    /**
     * Berikan / Update Rating & Ulasan Produk
     */
    public function addReview(Request $request, $id)
    {
        if (!session('is_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk memberikan ulasan.');
        }

        $userId = session('user_id');

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|min:3|max:2000',
        ], [
            'rating.required' => 'Silakan pilih bintang rating (1-5).',
            'rating.between' => 'Rating harus antara 1 sampai 5 bintang.',
            'review.required' => 'Ulasan tidak boleh kosong.',
            'review.min' => 'Ulasan minimal 3 karakter.',
        ]);

        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        // Simpan atau update ulasan (user hanya punya 1 ulasan per produk)
        Review::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $id,
            ],
            [
                'rating' => $validated['rating'],
                'review' => $validated['review'],
            ]
        );

        return back()->with('success', 'Terima kasih! Ulasan dan rating Anda berhasil disimpan.');
    }

    /**
     * Detail Pesanan dan Timeline Pelacakan Pesanan untuk User
     */
    public function showOrderDetail(Request $request, $trxId)
    {
        if (!session('is_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $orders = DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.product_id')
            ->select(
                'orders.*',
                'products.product_title',
                'products.product_price',
                'products.product_image'
            )
            ->where('orders.trx_id', $trxId)
            ->where('orders.user_id', $userId)
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('user.orders', ['tab' => 'orders'])
                ->with('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.');
        }

        $firstOrder = $orders->first();

        // Rekonsiliasi otomatis langsung ke Midtrans API jika status masih Pending
        if (strtolower((string) $firstOrder->p_status) === 'pending' && strtolower((string) ($firstOrder->payment_method ?? '')) === 'midtrans') {
            try {
                $midtransService = app(\App\Services\MidtransService::class);
                $orderService = app(\App\Services\OrderService::class);
                $reconcile = $midtransService->reconcileOrder($trxId, $orderService);

                if (!empty($reconcile['synced'])) {
                    // Muat ulang data pesanan yang baru diperbarui
                    $orders = DB::table('orders')
                        ->join('products', 'orders.product_id', '=', 'products.product_id')
                        ->select(
                            'orders.*',
                            'products.product_title',
                            'products.product_price',
                            'products.product_image'
                        )
                        ->where('orders.trx_id', $trxId)
                        ->where('orders.user_id', $userId)
                        ->get();
                    $firstOrder = $orders->first();
                }
            } catch (\Throwable $e) {
                // Abaikan jika tidak ada koneksi keluar
            }
        }

        $user = DB::table('user_info')->where('user_id', $userId)->first();

        // Riwayat perubahan status pesanan
        $histories = DB::table('order_status_histories')
            ->where('trx_id', $trxId)
            ->orderBy('id', 'asc')
            ->get();

        $totalAmount = $orders->sum(function ($item) {
            return (int) ($item->total_amt ?: ($item->qty * $item->product_price));
        });

        return view('user.order-detail', compact('orders', 'firstOrder', 'user', 'histories', 'totalAmount', 'trxId'));
    }

    /**
     * User Mengonfirmasi Penerimaan Barang ('Pesanan Diterima')
     */
    public function confirmReceived(Request $request, $trxId)
    {
        if (!session('is_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');
        $user = DB::table('user_info')->where('user_id', $userId)->first();
        $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: (session('user_name') ?? 'Pelanggan');

        $orderService = app(\App\Services\OrderService::class);
        $result = $orderService->confirmReceived($trxId, $userId, $userName);

        if ($result['success']) {
            return redirect()->route('user.orders', ['tab' => 'orders'])
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
