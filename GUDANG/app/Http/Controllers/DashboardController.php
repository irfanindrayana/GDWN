<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data untuk Home/Stock
        $totalHome = DB::table('home')->count();
        $lastHomeUpdate = DB::table('home')
            ->orderBy('updated_at', 'desc')
            ->value('updated_at');
        $lastHomeUpdate = $lastHomeUpdate ? Carbon::parse($lastHomeUpdate)->format('d M Y H:i') : null;

        // Mengambil data Barang Masuk
        $totalBarangMasuk = DB::table('barang_masuks')->count();
        $lastBarangMasuk = DB::table('barang_masuks')
            ->orderBy('created_at', 'desc')
            ->value('created_at');
        $lastBarangMasuk = $lastBarangMasuk ? Carbon::parse($lastBarangMasuk)->format('d M Y H:i') : null;

        // Mengambil data Barang Keluar
        $totalBarangKeluar = DB::table('barang_keluars')->count();
        $lastBarangKeluar = DB::table('barang_keluars')
            ->orderBy('created_at', 'desc')
            ->value('created_at');
        $lastBarangKeluar = $lastBarangKeluar ? Carbon::parse($lastBarangKeluar)->format('d M Y H:i') : null;

        // Mengambil aktivitas terakhir (10 terakhir)
        $recentActivities = collect();
        
        // Aktivitas dari Home/Stock
        $homeActivities = DB::table('home')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'message' => "Stock: {$item->namaBarang} diperbarui pada " . Carbon::parse($item->updated_at)->format('d M Y H:i'),
                    'timestamp' => $item->updated_at
                ];
            });

        // Aktivitas dari Barang Masuk
        $barangMasukActivities = DB::table('barang_masuks')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'message' => "Barang Masuk: {$item->nama_barang} ({$item->jumlah}) pada " . Carbon::parse($item->created_at)->format('d M Y H:i'),
                    'timestamp' => $item->created_at
                ];
            });

        // Aktivitas dari Barang Keluar
        $barangKeluarActivities = DB::table('barang_keluars')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'message' => "Barang Keluar: {$item->nama_barang} ({$item->jumlah}) pada " . Carbon::parse($item->created_at)->format('d M Y H:i'),
                    'timestamp' => $item->created_at
                ];
            });

        

        // Gabungkan semua aktivitas dan urutkan
        $recentActivities = collect()
            ->merge($homeActivities)
            ->merge($barangMasukActivities)
            ->merge($barangKeluarActivities)
            
            ->sortByDesc('timestamp')
            ->take(10)
            ->pluck('message');

        return view('dashboard.index', compact(
            'totalHome',
            'lastHomeUpdate',
            'totalBarangMasuk',
            'lastBarangMasuk',
            'totalBarangKeluar',
            'lastBarangKeluar',
            
            'recentActivities'
        ));
    }
}