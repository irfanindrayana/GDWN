<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');

        // Cari di tabel Home (Gudang)
        $barang = DB::table('home')
            ->where(function($q) use ($query) {
                $q->where('namaBarang', 'LIKE', "%{$query}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$query}%");
            })
            ->get();

        // Cari di tabel Barang Masuk
        $barangMasuk = DB::table('barang_masuks')
            ->where(function($q) use ($query) {
                $q->where('nama_barang', 'LIKE', "%{$query}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$query}%");
            })
            ->get();

        // Cari di tabel Barang Keluar
        $barangKeluar = DB::table('barang_keluars')
            ->where(function($q) use ($query) {
                $q->where('nama_barang', 'LIKE', "%{$query}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$query}%");
            })
            ->get();

        // Cari di tabel Member
        $member = DB::table('members')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('address', 'LIKE', "%{$query}%");
            })
            ->get();

        return view('search.results', compact('barang', 'barangMasuk', 'barangKeluar', 'member', 'query'));
    }
}