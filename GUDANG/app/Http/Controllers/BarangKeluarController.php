<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangKeluar = BarangKeluar::getAll();
        return view('barang_keluar.index', compact('barangKeluar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_barang' => 'required',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'required',
            'gambar_baru' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar_default' => 'nullable|string'
        ]);

        try {
            // Validasi stok
            $barang = DB::table('home')->where('namaBarang', $request->nama_barang)->first();
            if (!$barang) {
                return redirect()->back()
                    ->with('error', 'Barang tidak ditemukan')
                    ->withInput();
            }

            if ($request->jumlah > $barang->stok) {
                return redirect()->back()
                    ->with('error', 'Jumlah melebihi stok yang tersedia')
                    ->withInput();
            }

            // Handle gambar
            if ($request->hasFile('gambar_baru')) {
                $file = $request->file('gambar_baru');
                $extension = $file->getClientOriginalExtension();
                $fileName = str_replace(' ', '_', $request->nama_barang) . '_' . time() . '.' . $extension;
                
                // Pindahkan file ke storage
                if ($file->move(public_path('storage/gambar'), $fileName)) {
                    $gambar = $fileName;
                    \Log::info('Gambar berhasil diupload: ' . $fileName);
                } else {
                    \Log::error('Gagal mengupload gambar');
                    $gambar = $barang ? $barang->gambar : 'no-image.png';
                }
            } else {
                // Gunakan gambar dari barang yang dipilih
                $gambar = $barang ? $barang->gambar : 'no-image.png';
            }

            $data = [
                'tanggal' => $request->tanggal,
                'nama_barang' => $request->nama_barang,
                'jumlah' => $request->jumlah,
                'deskripsi' => $request->deskripsi,
                'gambar' => $gambar,
                'admin' => Auth::user()->name,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::table('barang_keluars')->insert($data);

            // Update stok
            DB::table('home')
                ->where('namaBarang', $request->nama_barang)
                ->update(['stok' => $barang->stok - $request->jumlah]);

            return redirect()->route('barangKeluar.index')
                ->with('success', 'Data barang keluar berhasil ditambahkan');

        } catch (\Exception $e) {
            \Log::error('Error in barang keluar store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barangKeluar = BarangKeluar::find($id);
        if (!$barangKeluar) {
            abort(404);
        }
        return view('barang_keluar.show', compact('barangKeluar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barangKeluar = BarangKeluar::find($id);
        if (!$barangKeluar) {
            abort(404);
        }
        return view('barang_keluar.edit', compact('barangKeluar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $barangKeluar = DB::table('barang_keluars')->where('id', $id)->first();
            if (!$barangKeluar) {
                abort(404);
            }
            $oldJumlah = $barangKeluar->jumlah;

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'nama_barang' => 'required|string',
                'deskripsi' => 'required|string',
                'jumlah' => 'required|integer|min:1',
                'gambar_default' => 'nullable|string'
            ]);

            // Validasi stok
            $barang = DB::table('home')->where('namaBarang', $request->nama_barang)->first();
            if (!$barang) {
                return redirect()->back()
                    ->with('error', 'Barang tidak ditemukan')
                    ->withInput();
            }

            // Hitung selisih jumlah
            $selisih = $request->jumlah - $oldJumlah;
            
            // Cek apakah stok mencukupi jika ada penambahan jumlah
            if ($selisih > 0 && $barang->stok < $selisih) {
                return redirect()->back()
                    ->with('error', 'Stok tidak mencukupi untuk penambahan! Stok tersedia: ' . $barang->stok)
                    ->withInput();
            }

            // Update data barang keluar
            DB::table('barang_keluars')
                ->where('id', $id)
                ->update([
                    'tanggal' => $request->tanggal,
                    'nama_barang' => $request->nama_barang,
                    'jumlah' => $request->jumlah,
                    'deskripsi' => $request->deskripsi,
                    'updated_at' => now()
                ]);

            // Update stok di home
            DB::table('home')
                ->where('namaBarang', $request->nama_barang)
                ->update(['stok' => $barang->stok - $selisih]);

            return redirect()->route('barangKeluar.index')
                ->with('success', 'Data barang keluar berhasil diperbarui');

        } catch (\Exception $e) {
            \Log::error('Error in barang keluar update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $barangKeluar = BarangKeluar::find($id);
            if (!$barangKeluar) {
                abort(404);
            }
            
            // Kembalikan stok ke home
            $home = Home::find($barangKeluar->nama_barang);
            if ($home) {
                Home::updateStok($home->id, $home->stok + $barangKeluar->jumlah);
            }

            BarangKeluar::deleteData($id);

            return redirect()->route('barangKeluar.index')
                ->with('success', 'Data barang keluar berhasil dihapus dan stok dikembalikan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function barangKeluarManual()
    {
        try {
            // Generate nomor surat
            $tahun = date('Y');
            $bulan = date('m');
            $totalSuratBulanIni = DB::table('barang_keluars')
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->count();
            
            $nomorUrut = str_pad($totalSuratBulanIni + 1, 3, '0', STR_PAD_LEFT);
            $nomorSurat = $nomorUrut . '/SJ/GDG/' . $bulan . '/' . $tahun;

            $data = [
                'nomorSurat' => $nomorSurat,
                'tanggal' => date('d/m/Y'),
                'admin' => Auth::user()->name
            ];

            $pdf = PDF::loadView('barang_keluar.surat-jalan-manual', $data);
            return $pdf->stream('surat-jalan-manual.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function barangKeluar($id)
    {
        try {
            $barangKeluar = DB::table('barang_keluars')->where('id', $id)->first();
            if (!$barangKeluar) {
                abort(404);
            }

            // Generate nomor surat
            $tahun = date('Y');
            $bulan = date('m');
            $totalSuratBulanIni = DB::table('barang_keluars')
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->count();
            
            $nomorUrut = str_pad($totalSuratBulanIni + 1, 3, '0', STR_PAD_LEFT);
            $nomorSurat = $nomorUrut . '/SJ/GDG/' . $bulan . '/' . $tahun;

            $data = [
                'barangKeluar' => $barangKeluar,
                'nomorSurat' => $nomorSurat,
                'tanggal' => date('d/m/Y'),
                'admin' => Auth::user()->name
            ];

            $pdf = PDF::loadView('barang_keluar.surat-jalan', $data);
            return $pdf->stream('surat-jalan-' . $id . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}
