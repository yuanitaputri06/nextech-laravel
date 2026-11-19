<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR KATEGORI
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // Pastikan admin sudah login
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        // Ambil semua kategori
        $categories = DB::table('categories')
            ->orderBy('cat_id', 'desc')
            ->get();

        return view(
            'admin.categories',
            compact('categories')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH KATEGORI
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        // Pastikan admin sudah login
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        return view('admin.categories-create');
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN KATEGORI
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Pastikan admin sudah login
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        // Validasi
        $request->validate([
            'cat_title' => 'required|string|max:255',
        ]);


        // Simpan kategori
        DB::table('categories')->insert([
            'cat_title' => $request->cat_title,
        ]);


        return redirect()
            ->route('admin.categories')
            ->with(
                'success',
                'Kategori berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | FORM EDIT KATEGORI
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        // Pastikan admin sudah login
        if (!session('is_admin')) {
            return redirect()->route('login');
        }


        // Cari kategori
        $category = DB::table('categories')
            ->where('cat_id', $id)
            ->first();


        // Jika tidak ditemukan
        if (!$category) {
            return redirect()
                ->route('admin.categories')
                ->with(
                    'error',
                    'Kategori tidak ditemukan.'
                );
        }


        return view(
            'admin.categories-edit',
            compact('category')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE KATEGORI
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        // Pastikan admin sudah login
        if (!session('is_admin')) {
            return redirect()->route('login');
        }


        // Validasi
        $request->validate([
            'cat_title' => 'required|string|max:255',
        ]);


        // Cek kategori
        $category = DB::table('categories')
            ->where('cat_id', $id)
            ->first();


        if (!$category) {
            return redirect()
                ->route('admin.categories')
                ->with(
                    'error',
                    'Kategori tidak ditemukan.'
                );
        }


        // Update
        DB::table('categories')
            ->where('cat_id', $id)
            ->update([
                'cat_title' => $request->cat_title,
            ]);


        return redirect()
            ->route('admin.categories')
            ->with(
                'success',
                'Kategori berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS KATEGORI
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        // Pastikan admin sudah login
        if (!session('is_admin')) {
            return redirect()->route('login');
        }


        // Cari kategori
        $category = DB::table('categories')
            ->where('cat_id', $id)
            ->first();


        if (!$category) {
            return redirect()
                ->route('admin.categories')
                ->with(
                    'error',
                    'Kategori tidak ditemukan.'
                );
        }


        // Hapus kategori
        DB::table('categories')
            ->where('cat_id', $id)
            ->delete();


        return redirect()
            ->route('admin.categories')
            ->with(
                'success',
                'Kategori berhasil dihapus.'
            );
    }
}