<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // =====================================================
    // HALAMAN PRODUK DI WEBSITE
    // =====================================================

    public function index()
    {
        $products = DB::table('products')
            ->leftJoin(
                'categories',
                'products.product_cat',
                '=',
                'categories.cat_id'
            )
            ->leftJoin(
                'brands',
                'products.product_brand',
                '=',
                'brands.brand_id'
            )
            ->select(
                'products.*',
                'categories.cat_title',
                'brands.brand_title'
            )
            ->orderBy('products.product_id', 'desc')
            ->get();

        return view('home', compact('products'));
    }


    // =====================================================
    // HALAMAN PRODUK ADMIN
    // =====================================================

    public function adminIndex(Request $request)
    {
        // Cek login admin
        if (!session('is_admin')) {
            return redirect('/login');
        }

        $search = trim((string) $request->query('q', ''));
        $categoryFilter = $request->query('category');
        $brandFilter = $request->query('brand');

        $query = DB::table('products')
            ->leftJoin('categories', 'products.product_cat', '=', 'categories.cat_id')
            ->leftJoin('brands', 'products.product_brand', '=', 'brands.brand_id')
            ->select(
                'products.*',
                'categories.cat_title',
                'brands.brand_title'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_title', 'like', "%{$search}%")
                  ->orWhere('products.product_keywords', 'like', "%{$search}%")
                  ->orWhere('products.product_desc', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryFilter)) {
            $query->where('products.product_cat', $categoryFilter);
        }

        if (!empty($brandFilter)) {
            $query->where('products.product_brand', $brandFilter);
        }

        $products = $query->orderBy('products.product_id', 'desc')->get();

        $categories = DB::table('categories')->orderBy('cat_title', 'asc')->get();
        $brands = DB::table('brands')->orderBy('brand_title', 'asc')->get();

        return view(
            'admin.products.index',
            compact('products', 'categories', 'brands', 'search', 'categoryFilter', 'brandFilter')
        );
    }


    // =====================================================
    // FORM TAMBAH PRODUK
    // =====================================================

    public function create()
    {
        // Cek login admin
        if (!session('is_admin')) {
            return redirect('/login');
        }


        // Ambil kategori
        $categories = DB::table('categories')
            ->orderBy('cat_title', 'asc')
            ->get();


        // Ambil brand
        $brands = DB::table('brands')
            ->orderBy('brand_title', 'asc')
            ->get();


        return view(
            'admin.products.create',
            compact(
                'categories',
                'brands'
            )
        );
    }


    // =====================================================
    // SIMPAN PRODUK BARU
    // =====================================================

    public function store(Request $request)
    {
        // Cek login admin
        if (!session('is_admin')) {
            return redirect('/login');
        }


        // Validasi
        $request->validate([

            'product_title' =>
                'required|string|max:255',

            'product_brand' =>
                'nullable|integer',

            'product_price' =>
                'required|numeric|min:0',

            'stock' =>
                'nullable|integer|min:0',

            'product_cat' =>
                'required',

            'product_desc' =>
                'nullable|string',

            'product_keywords' =>
                'nullable|string',

            'product_image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);


        // =================================================
        // UPLOAD GAMBAR
        // =================================================

        $imageName = null;


        if ($request->hasFile('product_image')) {

            $image = $request->file('product_image');

            $imageName =
                time() . '_' .
                $image->getClientOriginalName();


            $image->move(
                public_path('assets/images'),
                $imageName
            );
        }


        // =================================================
        // SIMPAN PRODUK DENGAN TRANSAKSI DATABASE
        // =================================================

        $productId = null;
        DB::connection('mysql')->beginTransaction();

        try {
            $stock = $request->filled('stock') ? (int) $request->stock : 10;

            $productId = DB::connection('mysql')->table('products')->insertGetId([
                'product_title'    => $request->product_title,
                'product_brand'    => $request->product_brand ?? 0,
                'product_price'    => $request->product_price,
                'stock'            => $stock,
                'product_desc'     => $request->product_desc,
                'product_cat'      => $request->product_cat,
                'product_image'    => $imageName,
                'product_keywords' => $request->product_keywords,
            ]);

            DB::connection('mysql')->commit();
        } catch (\Throwable $e) {
            DB::connection('mysql')->rollBack();

            // Hapus gambar jika transaksi utama gagal
            if ($imageName && file_exists(public_path('assets/images/' . $imageName))) {
                unlink(public_path('assets/images/' . $imageName));
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }

        // =================================================
        // PENCATATAN AKTIVITAS KE DATABASE KEDUA
        // =================================================
        ActivityLog::record(
            adminId: session('admin_id'),
            adminName: session('admin_name'),
            activityType: 'Menambahkan Produk',
            description: "Menambahkan produk baru: {$request->product_title} (ID: {$productId})",
            subjectType: 'Product',
            subjectId: (int) $productId,
            oldValues: null,
            newValues: [
                'product_title'    => $request->product_title,
                'product_brand'    => $request->product_brand ?? 0,
                'product_price'    => $request->product_price,
                'stock'            => $stock,
                'product_cat'      => $request->product_cat,
                'product_image'    => $imageName,
                'product_desc'     => $request->product_desc,
                'product_keywords' => $request->product_keywords,
            ],
            objectInfo: "Produk: {$request->product_title} (ID: {$productId})"
        );

        return redirect()
            ->route('admin.products')
            ->with(
                'success',
                'Produk berhasil ditambahkan.'
            );
    }


    // =====================================================
    // FORM EDIT PRODUK
    // =====================================================

    public function edit($id)
    {
        // Cek login admin
        if (!session('is_admin')) {
            return redirect('/login');
        }


        // Ambil produk
        $product = DB::table('products')
            ->where('product_id', $id)
            ->first();


        // Jika produk tidak ditemukan
        if (!$product) {

            return redirect()
                ->route('admin.products')
                ->with(
                    'error',
                    'Produk tidak ditemukan.'
                );
        }


        // Ambil kategori
        $categories = DB::table('categories')
            ->orderBy('cat_title', 'asc')
            ->get();


        // Ambil brand
        $brands = DB::table('brands')
            ->orderBy('brand_title', 'asc')
            ->get();


        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories',
                'brands'
            )
        );
    }


    // =====================================================
    // UPDATE PRODUK
    // =====================================================

    public function update(
        Request $request,
        $id
    ) {

        // Cek login admin
        if (!session('is_admin')) {
            return redirect('/login');
        }


        // Validasi
        $request->validate([

            'product_title' =>
                'required|string|max:255',

            'product_brand' =>
                'nullable|integer',

            'product_price' =>
                'required|numeric|min:0',

            'stock' =>
                'nullable|integer|min:0',

            'product_cat' =>
                'required',

            'product_desc' =>
                'nullable|string',

            'product_keywords' =>
                'nullable|string',

            'product_image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);


        // Ambil produk lama
        $product = DB::table('products')
            ->where('product_id', $id)
            ->first();


        if (!$product) {

            return redirect()
                ->route('admin.products')
                ->with(
                    'error',
                    'Produk tidak ditemukan.'
                );
        }


        // Simpan data lama sebelum diubah
        $oldProductData = [
            'product_title'    => $product->product_title,
            'product_brand'    => $product->product_brand,
            'product_price'    => $product->product_price,
            'stock'            => $product->stock ?? 10,
            'product_desc'     => $product->product_desc,
            'product_cat'      => $product->product_cat,
            'product_image'    => $product->product_image,
            'product_keywords' => $product->product_keywords,
        ];

        // Gunakan gambar lama
        $imageName =
            $product->product_image;


        // =================================================
        // JIKA ADA GAMBAR BARU
        // =================================================

        if ($request->hasFile('product_image')) {

            $image =
                $request->file('product_image');


            $imageName =
                time() . '_' .
                $image->getClientOriginalName();


            // Simpan gambar baru
            $image->move(
                public_path('assets/images'),
                $imageName
            );


            // Hapus gambar lama
            if (
                $product->product_image &&
                file_exists(
                    public_path(
                        'assets/images/' .
                        $product->product_image
                    )
                )
            ) {

                unlink(
                    public_path(
                        'assets/images/' .
                        $product->product_image
                    )
                );
            }
        }


        // =================================================
        // UPDATE DATABASE DENGAN TRANSAKSI
        // =================================================

        $stock = $request->filled('stock') ? (int) $request->stock : ($product->stock ?? 10);

        $newProductData = [
            'product_title'    => $request->product_title,
            'product_brand'    => $request->product_brand ?? 0,
            'product_price'    => $request->product_price,
            'stock'            => $stock,
            'product_desc'     => $request->product_desc,
            'product_cat'      => $request->product_cat,
            'product_image'    => $imageName,
            'product_keywords' => $request->product_keywords,
        ];

        DB::connection('mysql')->beginTransaction();

        try {
            DB::connection('mysql')->table('products')
                ->where('product_id', $id)
                ->update($newProductData);

            DB::connection('mysql')->commit();
        } catch (\Throwable $e) {
            DB::connection('mysql')->rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }

        // =================================================
        // PENCATATAN AKTIVITAS KE DATABASE KEDUA
        // =================================================
        ActivityLog::record(
            adminId: session('admin_id'),
            adminName: session('admin_name'),
            activityType: 'Mengubah Produk',
            description: "Mengubah produk: {$request->product_title} (ID: {$id})",
            subjectType: 'Product',
            subjectId: (int) $id,
            oldValues: $oldProductData,
            newValues: $newProductData,
            objectInfo: "Produk: {$request->product_title} (ID: {$id})"
        );

        return redirect()
            ->route('admin.products')
            ->with(
                'success',
                'Produk berhasil diperbarui.'
            );
    }


    // =====================================================
    // HAPUS PRODUK
    // =====================================================

    public function destroy($id)
    {
        // Cek login admin
        if (!session('is_admin')) {
            return redirect('/login');
        }


        // Ambil produk
        $product = DB::table('products')
            ->where('product_id', $id)
            ->first();


        // Jika tidak ditemukan
        if (!$product) {

            return redirect()
                ->route('admin.products')
                ->with(
                    'error',
                    'Produk tidak ditemukan.'
                );
        }


        // Simpan data produk yang akan dihapus agar identitas tetap tercatat di log
        $deletedProductData = [
            'product_id'       => $product->product_id,
            'product_title'    => $product->product_title,
            'product_brand'    => $product->product_brand,
            'product_price'    => $product->product_price,
            'stock'            => $product->stock ?? 10,
            'product_desc'     => $product->product_desc,
            'product_cat'      => $product->product_cat,
            'product_image'    => $product->product_image,
            'product_keywords' => $product->product_keywords,
        ];

        // =================================================
        // HAPUS PRODUK DENGAN TRANSAKSI DATABASE
        // =================================================

        DB::connection('mysql')->beginTransaction();

        try {
            DB::connection('mysql')->table('products')
                ->where('product_id', $id)
                ->delete();

            DB::connection('mysql')->commit();
        } catch (\Throwable $e) {
            DB::connection('mysql')->rollBack();

            return redirect()
                ->route('admin.products')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }

        // =================================================
        // HAPUS GAMBAR SETELAH TRANSAKSI BERHASIL
        // =================================================

        if (
            $product->product_image &&
            file_exists(
                public_path(
                    'assets/images/' .
                    $product->product_image
                )
            )
        ) {
            unlink(
                public_path(
                    'assets/images/' .
                    $product->product_image
                )
            );
        }

        // =================================================
        // PENCATATAN AKTIVITAS KE DATABASE KEDUA
        // =================================================
        ActivityLog::record(
            adminId: session('admin_id'),
            adminName: session('admin_name'),
            activityType: 'Menghapus Produk',
            description: "Menghapus produk: {$product->product_title} (ID: {$id})",
            subjectType: 'Product',
            subjectId: (int) $id,
            oldValues: $deletedProductData,
            newValues: null,
            objectInfo: "Produk: {$product->product_title} (ID: {$id})"
        );

        return redirect()
            ->route('admin.products')
            ->with(
                'success',
                'Produk berhasil dihapus.'
            );
    }
}