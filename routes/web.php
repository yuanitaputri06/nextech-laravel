<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\ActivityLog;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PaymentController;



/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $products = Product::orderBy('product_id', 'desc')
        ->limit(8)
        ->get();

    $categories = DB::table('categories')
        ->orderBy('cat_title')
        ->get();

    return view('home', compact('products', 'categories'));
})->name('home');


/*
|--------------------------------------------------------------------------
| HALAMAN SEMUA PRODUK DAN FILTER KATEGORI
|--------------------------------------------------------------------------
*/

Route::get('/products', function (Request $request) {
    $categories = DB::table('categories')
        ->orderBy('cat_title')
        ->get();

    $query = DB::table('products')
        ->leftJoin(
            'categories',
            'products.product_cat',
            '=',
            'categories.cat_id'
        )
        ->select(
            'products.*',
            'categories.cat_title'
        );

    $selectedCategory = $request->query('category');

    if ($selectedCategory !== null && $selectedCategory !== '') {
        $request->validate([
            'category' => 'integer',
        ]);

        $query->where(
            'products.product_cat',
            $selectedCategory
        );
    }

    $products = $query
        ->orderBy('products.product_id', 'desc')
        ->get();

    $activeCategory = null;

    if ($selectedCategory !== null && $selectedCategory !== '') {
        $activeCategory = $categories->firstWhere(
            'cat_id',
            (int) $selectedCategory
        );
    }

    return view('products', compact(
        'products',
        'categories',
        'selectedCategory',
        'activeCategory'
    ));
})->name('products.index');

/*
|--------------------------------------------------------------------------
| PENCARIAN PRODUK (HALAMAN & API AUTOCOMPLETE)
|--------------------------------------------------------------------------
*/

Route::get('/search', function (Request $request) {
    $q = trim((string) $request->query('q', ''));

    $categories = DB::table('categories')
        ->orderBy('cat_title')
        ->get();

    $query = DB::table('products')
        ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
        ->leftJoin('brands', 'products.product_brand', '=', 'brands.brand_id')
        ->select(
            'products.*',
            'categories.cat_title',
            'brands.brand_title'
        );

    if ($q !== '') {
        $query->where(function ($sub) use ($q) {
            $sub->where('products.product_title', 'LIKE', "%{$q}%")
                ->orWhere('products.product_desc', 'LIKE', "%{$q}%")
                ->orWhere('products.product_keywords', 'LIKE', "%{$q}%")
                ->orWhere('categories.cat_title', 'LIKE', "%{$q}%")
                ->orWhere('brands.brand_title', 'LIKE', "%{$q}%");
        });
    }

    $products = $query
        ->orderBy('products.product_id', 'desc')
        ->paginate(12)
        ->appends(['q' => $q]);

    $totalResults = $products->total();

    return view('search', compact(
        'products',
        'q',
        'categories',
        'totalResults'
    ));
})->name('search');

Route::get('/api/search', function (Request $request) {
    $q = trim((string) $request->query('q', ''));

    if (mb_strlen($q) < 2) {
        return response()->json([]);
    }

    $results = DB::table('products')
        ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
        ->leftJoin('brands', 'products.product_brand', '=', 'brands.brand_id')
        ->select(
            'products.product_id',
            'products.product_title',
            'products.product_price',
            'products.product_image',
            'categories.cat_title',
            'brands.brand_title'
        )
        ->where(function ($sub) use ($q) {
            $sub->where('products.product_title', 'LIKE', "%{$q}%")
                ->orWhere('products.product_desc', 'LIKE', "%{$q}%")
                ->orWhere('products.product_keywords', 'LIKE', "%{$q}%")
                ->orWhere('categories.cat_title', 'LIKE', "%{$q}%")
                ->orWhere('brands.brand_title', 'LIKE', "%{$q}%");
        })
        ->orderBy('products.product_id', 'desc')
        ->limit(6)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->product_id,
                'title' => $item->product_title,
                'price' => 'Rp ' . number_format($item->product_price, 0, ',', '.'),
                'category' => $item->cat_title ?? 'Gadget',
                'brand' => $item->brand_title ?? '',
                'image' => asset('assets/images/' . $item->product_image),
                'url' => route('products.show', $item->product_id),
            ];
        });

    return response()->json($results);
})->name('api.search');

/*
|--------------------------------------------------------------------------
| DETAIL PRODUK DAN REKOMENDASI
|--------------------------------------------------------------------------
*/

Route::get('/product/{id}', function ($id) {
    // Ambil detail produk beserta nama kategorinya.
    $product = DB::table('products')
        ->leftJoin(
            'categories',
            'products.product_cat',
            '=',
            'categories.cat_id'
        )
        ->select(
            'products.*',
            'categories.cat_title'
        )
        ->where('products.product_id', $id)
        ->first();

    // Tampilkan halaman 404 jika produk tidak ditemukan.
    if (!$product) {
        abort(404);
    }

    // Ambil ulasan produk dari database
    $reviews = DB::table('reviews')
        ->join('user_info', 'reviews.user_id', '=', 'user_info.user_id')
        ->select(
            'reviews.*',
            'user_info.first_name',
            'user_info.last_name',
            'user_info.profile_photo'
        )
        ->where('reviews.product_id', $id)
        ->orderBy('reviews.id', 'desc')
        ->get();

    $reviewCount = $reviews->count();
    $averageRating = $reviewCount > 0 ? round((float) $reviews->avg('rating'), 1) : 0;

    $userReview = null;
    $isInWishlist = false;
    if (session('is_user') && session('user_id')) {
        $userReview = DB::table('reviews')
            ->where('product_id', $id)
            ->where('user_id', session('user_id'))
            ->first();

        $isInWishlist = DB::table('wishlists')
            ->where('product_id', $id)
            ->where('user_id', session('user_id'))
            ->exists();
    }

    // Ambil maksimal 3 produk lain dari kategori yang sama.
    $recommendations = DB::table('products')
        ->leftJoin(
            'categories',
            'products.product_cat',
            '=',
            'categories.cat_id'
        )
        ->select(
            'products.*',
            'categories.cat_title'
        )
        ->where('products.product_cat', $product->product_cat)
        ->where('products.product_id', '!=', $product->product_id)
        ->orderByDesc('products.product_id')
        ->limit(3)
        ->get();

    return view('product-detail', compact(
        'product',
        'recommendations',
        'reviews',
        'reviewCount',
        'averageRating',
        'userReview',
        'isInWishlist'
    ));
})->name('products.show');

/*
|--------------------------------------------------------------------------
| VALIDASI STOK PRODUK (BACKEND API UNTUK KERANJANG & CHECKOUT)
|--------------------------------------------------------------------------
*/

Route::post('/product/{id}/check-stock', function (Request $request, $id) {
    $request->validate([
        'qty' => 'required|integer|min:1',
    ]);

    $product = DB::table('products')->where('product_id', $id)->first();
    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan.',
        ], 404);
    }

    $stock = (int) ($product->stock ?? 0);
    $qty = (int) $request->input('qty');

    if ($stock <= 0) {
        return response()->json([
            'success' => false,
            'available' => false,
            'stock' => 0,
            'message' => 'Maaf, stok produk ini sedang habis.',
        ]);
    }

    if ($qty > $stock) {
        return response()->json([
            'success' => false,
            'available' => true,
            'stock' => $stock,
            'message' => "Jumlah pembelian ({$qty} unit) melebihi stok yang tersedia (tersisa {$stock} unit).",
        ]);
    }

    return response()->json([
        'success' => true,
        'available' => true,
        'stock' => $stock,
        'message' => "Stok mencukupi. Tersedia {$stock} unit.",
    ]);
})->name('products.check-stock');


/*
|--------------------------------------------------------------------------
| LOGIN GABUNGAN ADMIN DAN USER
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| LOGIN GABUNGAN ADMIN DAN USER
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Cek akun admin terlebih dahulu.
    $admin = DB::table('admin_info')
        ->where('admin_email', $request->email)
        ->where('admin_password', $request->password)
        ->first();

    if ($admin) {
        session()->forget([
            'user_id',
            'user_name',
            'user_email',
            'is_user',
        ]);

        session()->regenerate();

        session()->put([
            'admin_id' => $admin->admin_id,
            'admin_name' => $admin->admin_name,
            'is_admin' => true,
        ]);

        return redirect()->route('admin.dashboard');
    }

    // Jika bukan admin, cek akun user.
    $user = DB::table('user_info')
        ->where('email', $request->email)
        ->where('password', $request->password)
        ->first();

    if ($user) {
        session()->forget([
            'admin_id',
            'admin_name',
            'is_admin',
        ]);

        session()->regenerate();

        session()->put([
            'user_id' => $user->user_id,
            'user_name' => trim(
                $user->first_name . ' ' . $user->last_name
            ),
            'user_email' => $user->email,
            'is_user' => true,
        ]);

        return redirect()->route('user.dashboard');
    }

    return back()
        ->withInput($request->only('email'))
        ->with('error', 'Email atau password salah.');
})->name('login.process');


/*
|--------------------------------------------------------------------------
| LOGOUT ADMIN
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {
    session()->forget([
        'admin_id',
        'admin_name',
        'is_admin',
    ]);

    session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');


/*
|--------------------------------------------------------------------------
| REGISTER USER
|--------------------------------------------------------------------------
*/

// Halaman register.
Route::get('/register', function () {
    return view('auth.register');
})->name('register');


// Proses register user.
Route::post('/register', function (Request $request) {
    $request->validate([
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|email|max:255',
        'mobile' => 'required|string|max:30',
        'address1' => 'required|string|max:255',
        'password' => 'required|min:6',
        'password_confirmation' => 'required|same:password',
    ], [
        'first_name.required' => 'Nama depan wajib diisi.',
        'last_name.required' => 'Nama belakang wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'mobile.required' => 'Nomor HP wajib diisi.',
        'address1.required' => 'Alamat wajib diisi.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password_confirmation.required' =>
            'Konfirmasi password wajib diisi.',
        'password_confirmation.same' =>
            'Konfirmasi password tidak sama.',
    ]);

    // Periksa apakah email sudah digunakan.
    $emailExists = DB::table('user_info')
        ->where('email', $request->email)
        ->exists();

    if ($emailExists) {
        return back()
            ->withInput($request->except([
                'password',
                'password_confirmation',
            ]))
            ->with(
                'error',
                'Email sudah terdaftar. Silakan gunakan email lain.'
            );
    }

    // Simpan data user.
    $userId = DB::table('user_info')->insertGetId([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'password' => $request->password,
        'mobile' => $request->mobile,
        'address1' => $request->address1,
        'address2' => '',
    ]);

    // Ambil data user yang baru dibuat.
    $user = DB::table('user_info')
        ->where('user_id', $userId)
        ->first();

    // Bersihkan session admin sebelum login sebagai user.
    session()->forget([
        'admin_id',
        'admin_name',
        'is_admin',
    ]);

    session()->regenerate();

    // Login otomatis setelah registrasi.
    session()->put([
        'user_id' => $user->user_id,
        'user_name' => trim(
            $user->first_name . ' ' . $user->last_name
        ),
        'user_email' => $user->email,
        'is_user' => true,
    ]);

    return redirect()->route('user.dashboard');
})->name('register.process');


/*
|--------------------------------------------------------------------------
| LOGIN USER
|--------------------------------------------------------------------------
*/

// Halaman login user menggunakan halaman login gabungan.
Route::get('/user/login', function () {
    return redirect()->route('login');
})->name('user.login');


// Proses login user.
Route::post('/user/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = DB::table('user_info')
        ->where('email', $request->email)
        ->where('password', $request->password)
        ->first();

    if (!$user) {
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Email atau password salah.');
    }

    // Bersihkan session admin.
    session()->forget([
        'admin_id',
        'admin_name',
        'is_admin',
    ]);

    session()->regenerate();

    // Simpan session user.
    session()->put([
        'user_id' => $user->user_id,
        'user_name' => trim(
            $user->first_name . ' ' . $user->last_name
        ),
        'user_email' => $user->email,
        'is_user' => true,
    ]);

    return redirect()->route('user.dashboard');
})->name('user.login.process');


/*
|--------------------------------------------------------------------------
| LOGOUT USER
|--------------------------------------------------------------------------
*/

Route::post('/user/logout', function () {
    session()->forget([
        'user_id',
        'user_name',
        'user_email',
        'is_user',
    ]);

    session()->regenerateToken();

    return redirect()->route('home');
})->name('user.logout');


/*
|--------------------------------------------------------------------------
| DASHBOARD USER (BERANDA SETELAH LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');


/*
|--------------------------------------------------------------------------
| PESANAN SAYA & KERANJANG BELANJA USER
|--------------------------------------------------------------------------
*/

Route::get('/user/orders', [UserController::class, 'orders'])->name('user.orders');
Route::get('/user/orders/{trx_id}', [UserController::class, 'showOrderDetail'])->name('user.orders.detail');
Route::post('/user/orders/{trx_id}/confirm-received', [UserController::class, 'confirmReceived'])->name('user.orders.confirm-received');
Route::get('/user/cart', function () {
    return redirect()->route('user.orders', ['tab' => 'cart']);
})->name('user.cart');

Route::post('/user/cart/add', [UserController::class, 'addToCart'])->name('user.cart.add');
Route::post('/user/cart/update', [UserController::class, 'updateCart'])->name('user.cart.update');
Route::delete('/user/cart/{id}', [UserController::class, 'removeFromCart'])->name('user.cart.remove');


/*
|--------------------------------------------------------------------------
| WISHLIST USER
|--------------------------------------------------------------------------
*/

Route::get('/user/wishlist', [UserController::class, 'wishlist'])->name('user.wishlist');
Route::post('/user/wishlist/toggle', [UserController::class, 'toggleWishlist'])->name('user.wishlist.toggle');


/*
|--------------------------------------------------------------------------
| RATING & ULASAN PRODUK
|--------------------------------------------------------------------------
*/

Route::post('/product/{id}/review', [UserController::class, 'addReview'])->name('products.review');


/*
|--------------------------------------------------------------------------
| DATA PRIBADI / PROFIL USER
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| DATA PRIBADI / PROFIL USER
|--------------------------------------------------------------------------
*/

Route::get('/user/profile', function () {
    if (!session('is_user')) {
        return redirect()->route('user.login')
            ->with('error', 'Silakan login terlebih dahulu.');
    }

    $user = DB::table('user_info')
        ->where('user_id', session('user_id'))
        ->first();

    if (!$user) {
        session()->forget([
            'user_id',
            'user_name',
            'user_email',
            'is_user',
        ]);

        return redirect()->route('login')
            ->with('error', 'Data pengguna tidak ditemukan.');
    }

    $totalOrders = DB::table('orders')
        ->where('user_id', $user->user_id)
        ->count();

    $completedOrders = DB::table('orders')
        ->where('user_id', $user->user_id)
        ->where('p_status', 'Completed')
        ->count();

    $pendingOrders = DB::table('orders')
        ->where('user_id', $user->user_id)
        ->where('p_status', 'Pending')
        ->count();

    return view('user.profile', compact(
        'user',
        'totalOrders',
        'completedOrders',
        'pendingOrders'
    ));
})->name('user.profile');


Route::put('/user/profile', function (Request $request) {
    if (!session('is_user')) {
        return redirect()->route('user.login');
    }

    $user = DB::table('user_info')
        ->where('user_id', session('user_id'))
        ->first();

    if (!$user) {
        return redirect()->route('login')
            ->with('error', 'Data pengguna tidak ditemukan.');
    }

    $validated = $request->validate([
        'first_name' => 'required|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'email' => 'required|email|max:255',
        'mobile' => 'nullable|string|max:30',
        'address1' => 'nullable|string|max:255',
        'address2' => 'nullable|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $emailExists = DB::table('user_info')
        ->where('email', $validated['email'])
        ->where('user_id', '!=', $user->user_id)
        ->exists();

    if ($emailExists) {
        return back()->withInput()->withErrors([
            'email' => 'Email sudah digunakan akun lain.',
        ]);
    }

    $data = [
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'] ?? '',
        'email' => $validated['email'],
        'mobile' => $validated['mobile'] ?? '',
        'address1' => $validated['address1'] ?? '',
        'address2' => $validated['address2'] ?? '',
    ];

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('profiles', 'public');
        $data['profile_photo'] = $path;
    }

    DB::table('user_info')
        ->where('user_id', $user->user_id)
        ->update($data);

    session()->put([
        'user_name' => trim(
            $data['first_name'] . ' ' . $data['last_name']
        ),
        'user_email' => $data['email'],
    ]);

    return redirect()->route('user.profile')
        ->with('success', 'Data pribadi berhasil diperbarui.');
})->name('user.profile.update');

/*
|--------------------------------------------------------------------------
| CHECKOUT & PEMBAYARAN USER
|--------------------------------------------------------------------------
*/

Route::get('/checkout', [UserController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [UserController::class, 'checkout'])->name('checkout.from_cart');
Route::post('/checkout/process', [UserController::class, 'processCheckout'])->name('checkout.process');

// MIDTRANS PAYMENT & WEBHOOK NOTIFICATION
Route::get('/payment/{trx_id}', [PaymentController::class, 'showPayment'])->name('user.payment');
Route::get('/payment-finish', [PaymentController::class, 'paymentFinish'])->name('user.payment.finish');
Route::match(['get', 'post'], '/payment/{trx_id}/check-status', [PaymentController::class, 'checkStatus'])->name('payment.check-status');
Route::match(['get', 'post'], '/midtrans/notification', [PaymentController::class, 'notification'])->name('midtrans.notification');
Route::match(['get', 'post'], '/api/midtrans/notification', [PaymentController::class, 'notification'])->name('api.midtrans.notification');


/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

/*
|--------------------------------------------------------------------------
| ADMIN - KEMBALI KE TOKO (STOREFRONT ADMIN)
|--------------------------------------------------------------------------
*/
Route::get('/admin/store', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }
    return redirect()->route('home');
})->name('admin.store');


/*
|--------------------------------------------------------------------------
| ADMIN - PRODUCTS
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/products',
    [ProductController::class, 'adminIndex']
)->name('admin.products');

Route::get(
    '/admin/products/create',
    [ProductController::class, 'create']
)->name('admin.products.create');

Route::post(
    '/admin/products',
    [ProductController::class, 'store']
)->name('admin.products.store');

Route::get(
    '/admin/products/{id}/edit',
    [ProductController::class, 'edit']
)->name('admin.products.edit');

Route::put(
    '/admin/products/{id}',
    [ProductController::class, 'update']
)->name('admin.products.update');

Route::delete(
    '/admin/products/{id}',
    [ProductController::class, 'destroy']
)->name('admin.products.destroy');


/*
|--------------------------------------------------------------------------
| ADMIN - CATEGORIES
|--------------------------------------------------------------------------
*/

Route::get('/admin/categories', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $categories = DB::table('categories')
        ->orderBy('cat_id', 'desc')
        ->get();

    return view('admin.categories', compact('categories'));
})->name('admin.categories');


Route::get('/admin/categories/create', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.categories-create');
})->name('admin.categories.create');


Route::post('/admin/categories', function (Request $request) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $request->validate([
        'cat_title' => 'required|string|max:255',
    ]);

    DB::table('categories')->insert([
        'cat_title' => $request->cat_title,
    ]);

    return redirect()
        ->route('admin.categories')
        ->with('success', 'Kategori berhasil ditambahkan.');
})->name('admin.categories.store');


Route::get('/admin/categories/{id}/edit', function ($id) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $category = DB::table('categories')
        ->where('cat_id', $id)
        ->first();

    if (!$category) {
        return redirect()
            ->route('admin.categories')
            ->with('error', 'Kategori tidak ditemukan.');
    }

    return view('admin.categories-edit', compact('category'));
})->name('admin.categories.edit');


Route::put('/admin/categories/{id}', function (
    Request $request,
    $id
) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $request->validate([
        'cat_title' => 'required|string|max:255',
    ]);

    $category = DB::table('categories')
        ->where('cat_id', $id)
        ->first();

    if (!$category) {
        return redirect()
            ->route('admin.categories')
            ->with('error', 'Kategori tidak ditemukan.');
    }

    DB::table('categories')
        ->where('cat_id', $id)
        ->update([
            'cat_title' => $request->cat_title,
        ]);

    return redirect()
        ->route('admin.categories')
        ->with('success', 'Kategori berhasil diperbarui.');
})->name('admin.categories.update');


Route::delete('/admin/categories/{id}', function ($id) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $category = DB::table('categories')
        ->where('cat_id', $id)
        ->first();

    if (!$category) {
        return redirect()
            ->route('admin.categories')
            ->with('error', 'Kategori tidak ditemukan.');
    }

    DB::table('categories')
        ->where('cat_id', $id)
        ->delete();

    return redirect()
        ->route('admin.categories')
        ->with('success', 'Kategori berhasil dihapus.');
})->name('admin.categories.destroy');


/*
|--------------------------------------------------------------------------
| ADMIN - BRANDS
|--------------------------------------------------------------------------
*/

Route::get('/admin/brands', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $brands = DB::table('brands')
        ->orderBy('brand_id', 'desc')
        ->get();

    return view('admin.brands', compact('brands'));
})->name('admin.brands');


Route::get('/admin/brands/create', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.brands-create');
})->name('admin.brands.create');


Route::post('/admin/brands', function (Request $request) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $request->validate([
        'brand_title' => 'required|string|max:255',
    ]);

    DB::table('brands')->insert([
        'brand_title' => $request->brand_title,
    ]);

    return redirect()
        ->route('admin.brands')
        ->with('success', 'Brand berhasil ditambahkan.');
})->name('admin.brands.store');


Route::get('/admin/brands/{id}/edit', function ($id) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $brand = DB::table('brands')
        ->where('brand_id', $id)
        ->first();

    if (!$brand) {
        return redirect()
            ->route('admin.brands')
            ->with('error', 'Brand tidak ditemukan.');
    }

    return view('admin.brands-edit', compact('brand'));
})->name('admin.brands.edit');


Route::put('/admin/brands/{id}', function (
    Request $request,
    $id
) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $request->validate([
        'brand_title' => 'required|string|max:255',
    ]);

    $brand = DB::table('brands')
        ->where('brand_id', $id)
        ->first();

    if (!$brand) {
        return redirect()
            ->route('admin.brands')
            ->with('error', 'Brand tidak ditemukan.');
    }

    DB::table('brands')
        ->where('brand_id', $id)
        ->update([
            'brand_title' => $request->brand_title,
        ]);

    return redirect()
        ->route('admin.brands')
        ->with('success', 'Brand berhasil diperbarui.');
})->name('admin.brands.update');


Route::delete('/admin/brands/{id}', function ($id) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $brand = DB::table('brands')
        ->where('brand_id', $id)
        ->first();

    if (!$brand) {
        return redirect()
            ->route('admin.brands')
            ->with('error', 'Brand tidak ditemukan.');
    }

    DB::table('brands')
        ->where('brand_id', $id)
        ->delete();

    return redirect()
        ->route('admin.brands')
        ->with('success', 'Brand berhasil dihapus.');
})->name('admin.brands.destroy');


/*
|--------------------------------------------------------------------------
| ADMIN - USERS
|--------------------------------------------------------------------------
*/

Route::get('/admin/users', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $users = DB::table('user_info')
        ->orderBy('user_id', 'desc')
        ->get();

    return view('admin.users', compact('users'));
})->name('admin.users');

Route::get('/admin/users/create', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.users-create');
})->name('admin.users.create');

Route::post('/admin/users', function (Request $request) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $request->validate([
        'first_name' => 'required|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:6',
        'mobile' => 'nullable|string|max:30',
        'address1' => 'nullable|string|max:255',
        'address2' => 'nullable|string|max:255',
    ]);

    $exists = DB::table('user_info')->where('email', $request->email)->exists();
    if ($exists) {
        return back()->withInput()->with('error', 'Email sudah terdaftar. Silakan gunakan email lain.');
    }

    DB::table('user_info')->insert([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name ?? '',
        'email' => $request->email,
        'password' => $request->password,
        'mobile' => $request->mobile ?? '',
        'address1' => $request->address1 ?? '',
        'address2' => $request->address2 ?? '',
    ]);

    return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan.');
})->name('admin.users.store');

Route::get('/admin/users/{id}/edit', function ($id) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $user = DB::table('user_info')->where('user_id', $id)->first();
    if (!$user) {
        return redirect()->route('admin.users')->with('error', 'User tidak ditemukan.');
    }

    return view('admin.users-edit', compact('user'));
})->name('admin.users.edit');

Route::put('/admin/users/{id}', function (Request $request, $id) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $request->validate([
        'first_name' => 'required|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'email' => 'required|email|max:255',
        'mobile' => 'nullable|string|max:30',
        'address1' => 'nullable|string|max:255',
        'address2' => 'nullable|string|max:255',
    ]);

    $user = DB::table('user_info')->where('user_id', $id)->first();
    if (!$user) {
        return redirect()->route('admin.users')->with('error', 'User tidak ditemukan.');
    }

    $data = [
        'first_name' => $request->first_name,
        'last_name' => $request->last_name ?? '',
        'email' => $request->email,
        'mobile' => $request->mobile ?? '',
        'address1' => $request->address1 ?? '',
        'address2' => $request->address2 ?? '',
    ];

    if ($request->filled('password')) {
        $data['password'] = $request->password;
    }

    DB::table('user_info')->where('user_id', $id)->update($data);

    return redirect()->route('admin.users')->with('success', 'Data user berhasil diperbarui.');
})->name('admin.users.update');

Route::delete('/admin/users/{id}', function ($id) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $user = DB::table('user_info')->where('user_id', $id)->first();
    if (!$user) {
        return redirect()->route('admin.users')->with('error', 'User tidak ditemukan.');
    }

    DB::table('user_info')->where('user_id', $id)->delete();

    return redirect()->route('admin.users')->with('success', 'User berhasil dihapus.');
})->name('admin.users.destroy');


/*
|--------------------------------------------------------------------------
| ADMIN - ORDERS
|--------------------------------------------------------------------------
*/

Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
Route::get('/admin/orders/{trx_id}/detail', [AdminController::class, 'orderDetail'])->name('admin.orders.detail');
Route::post('/admin/orders/{trx_id}/process', [AdminController::class, 'processOrder'])->name('admin.orders.process');
Route::post('/admin/orders/{trx_id}/ship', [AdminController::class, 'shipOrder'])->name('admin.orders.ship');
Route::put('/admin/orders/{id}', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update');
Route::delete('/admin/orders/{id}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');
Route::get('/admin/orders/completed', [AdminController::class, 'completedOrders'])->name('admin.orders.completed');


/*
|--------------------------------------------------------------------------
| ADMIN - REPORTS
|--------------------------------------------------------------------------
*/

Route::get('/admin/reports', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $reports = DB::table('orders')
        ->leftJoin(
            'user_info',
            'orders.user_id',
            '=',
            'user_info.user_id'
        )
        ->leftJoin(
            'products',
            'orders.product_id',
            '=',
            'products.product_id'
        )
        ->select(
            'orders.order_id',
            'orders.user_id',
            'orders.product_id',
            'orders.qty',
            'orders.trx_id',
            'orders.p_status',
            'orders.payment_method',
            'orders.total_amt',
            'orders.midtrans_transaction_id',
            'orders.midtrans_payment_type',
            'orders.midtrans_status',
            'orders.paid_at',
            'orders.created_at',
            'user_info.first_name',
            'user_info.last_name',
            'products.product_title',
            'products.product_price'
        )
        ->orderBy('orders.order_id', 'desc')
        ->get();

    $totalOrders = DB::table('orders')->count();

    $completedOrders = DB::table('orders')
        ->where('p_status', 'Completed')
        ->count();

    $pendingOrders = DB::table('orders')
        ->where('p_status', 'Pending')
        ->count();

    $totalQty = DB::table('orders')->sum('qty');

    $totalRevenue = DB::table('orders')
        ->leftJoin(
            'products',
            'orders.product_id',
            '=',
            'products.product_id'
        )
        ->selectRaw(
            'SUM(orders.qty * products.product_price) as total'
        )
        ->value('total');

    $totalRevenue = $totalRevenue ?? 0;

    $totalTransactions = $totalOrders;
    $totalSold = $totalQty;
    $totalSales = $totalRevenue;

    return view(
        'admin.reports',
        compact(
            'reports',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalQty',
            'totalRevenue',
            'totalTransactions',
            'totalSold',
            'totalSales'
        )
    );
})->name('admin.reports');

/*
|--------------------------------------------------------------------------
| ADMIN - SETTINGS (PENGATURAN)
|--------------------------------------------------------------------------
*/
Route::get('/admin/settings', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $admin = DB::table('admin_info')->where('admin_id', session('admin_id'))->first();
    if (!$admin) {
        $admin = DB::table('admin_info')->first();
    }

    $settingsFile = storage_path('app/store_settings.json');
    $storeSettings = file_exists($settingsFile)
        ? json_decode(file_get_contents($settingsFile), true)
        : [
            'store_name' => 'NexTech Store',
            'store_email' => 'admin@nextech.com',
            'store_phone' => '+62 812-3456-7890',
            'store_address' => 'Jl. Teknologi Modern No. 88, Jakarta Selatan',
            'store_description' => 'Pusat e-commerce produk teknologi dan aksesoris terpercaya.',
            'shipping_couriers' => 'JNE, TIKI, SiCepat, GoSend',
        ];

    return view('admin.settings', compact('admin', 'storeSettings'));
})->name('admin.settings');

Route::post('/admin/settings', function (Request $request) {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    $request->validate([
        'admin_name' => 'required|string|max:100',
        'admin_email' => 'required|email|max:255',
        'admin_password' => 'nullable|string|min:4',
        'store_name' => 'required|string|max:100',
        'store_email' => 'required|email|max:255',
        'store_phone' => 'nullable|string|max:50',
        'store_address' => 'nullable|string|max:255',
        'store_description' => 'nullable|string|max:500',
        'shipping_couriers' => 'nullable|string|max:255',
    ]);

    $adminId = session('admin_id');
    $adminUpdate = [
        'admin_name' => $request->admin_name,
        'admin_email' => $request->admin_email,
    ];

    if ($request->filled('admin_password')) {
        $adminUpdate['admin_password'] = $request->admin_password;
    }

    if ($adminId) {
        DB::table('admin_info')->where('admin_id', $adminId)->update($adminUpdate);
    } else {
        DB::table('admin_info')->limit(1)->update($adminUpdate);
    }
    session(['admin_name' => $request->admin_name]);

    $storeData = [
        'store_name' => $request->store_name,
        'store_email' => $request->store_email,
        'store_phone' => $request->store_phone ?? '',
        'store_address' => $request->store_address ?? '',
        'store_description' => $request->store_description ?? '',
        'shipping_couriers' => $request->shipping_couriers ?? '',
        'updated_at' => now()->toDateTimeString(),
    ];

    $settingsPath = storage_path('app/store_settings.json');
    if (!is_dir(dirname($settingsPath))) {
        mkdir(dirname($settingsPath), 0777, true);
    }
    file_put_contents($settingsPath, json_encode($storeData, JSON_PRETTY_PRINT));

    return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil disimpan dan diperbarui.');
})->name('admin.settings.update');

/*
|--------------------------------------------------------------------------
| AI CHATBOT NEXTECH ASSISTANT
|--------------------------------------------------------------------------
*/

Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.message');
Route::get('/chatbot/suggestions', [ChatbotController::class, 'getSuggestions'])->name('chatbot.suggestions');


