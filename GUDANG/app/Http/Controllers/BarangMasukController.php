<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuk = BarangMasuk::getAll();
        $stokHampirHabis = Home::getLowStock();
        $barang = Home::getAll();
        $riwayatPeminjaman = DB::table('peminjaman')->get();

        return view('barang_masuk.index', compact('barangMasuk', 'stokHampirHabis', 'barang', 'riwayatPeminjaman'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_barang' => 'required',
            'gambar_baru' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar_default' => 'nullable|string',
            'deskripsi' => 'required',
            'jumlah' => 'required|integer|min:1',
            'admin' => 'required'
        ]);

        try {
            // Konversi tanggal ke format yang benar
            $tanggal = \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d H:i:s');
            
            // Cek apakah barang sudah ada di tabel homes
            $existingBarang = DB::table('home')->where('namaBarang', $request->nama_barang)->first();

            // Handle gambar
            if ($request->hasFile('gambar_baru')) {
                $file = $request->file('gambar_baru');
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                
                // Pastikan direktori storage/app/public/gambar ada
                if (!Storage::exists('public/gambar')) {
                    Storage::makeDirectory('public/gambar');
                }
                
                // Simpan file menggunakan Storage facade
                $file->move(public_path('storage/gambar'), $fileName);
                $gambar = $fileName;
            } else {
                // Gunakan gambar dari barang yang sudah ada atau gambar default
                if ($existingBarang && file_exists(public_path('storage/gambar/' . $existingBarang->gambar))) {
                    $gambar = $existingBarang->gambar;
                } else {
                    $gambar = 'default.png';
                    // Pastikan file default.png ada
                    if (!file_exists(public_path('storage/gambar/default.png'))) {
                        copy(public_path('storage/gambar/no-image.png'), public_path('storage/gambar/default.png'));
                    }
                }
            }

            // Simpan data barang masuk
            DB::table('barang_masuks')->insert([
                'tanggal' => $tanggal,
                'nama_barang' => $request->nama_barang,
                'gambar' => $gambar,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'admin' => Auth::user()->name,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update atau buat data di tabel homes
            if ($existingBarang) {
                // Update stok dan gambar jika ada perubahan
                $updateData = ['stok' => $existingBarang->stok + $request->jumlah];
                if ($request->hasFile('gambar_baru')) {
                    $updateData['gambar'] = $gambar;
                }
                DB::table('home')->where('namaBarang', $request->nama_barang)->update($updateData);
            } else {
                // Buat data baru jika barang belum ada
                DB::table('home')->insert([
                    'namaBarang' => $request->nama_barang,
                    'deskripsi' => $request->deskripsi,
                    'stok' => $request->jumlah,
                    'gambar' => $gambar,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return redirect()->route('barangMasuk.index')
                ->with('success', 'Data barang masuk berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_barang' => 'required|string',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|integer|min:1'
        ]);

        $barangMasuk = DB::table('barang_masuks')->where('id', $id)->first();
        if (!$barangMasuk) {
            abort(404);
        }

        // Simpan jumlah lama untuk update stok
        $jumlahLama = $barangMasuk->jumlah;
        
        try {
            // Update data barang masuk dengan data spesifik
            DB::table('barang_masuks')->where('id', $id)->update([
                'tanggal' => \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'nama_barang' => $request->nama_barang,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'admin' => Auth::user()->name
            ]);

            // Update stok di tabel home
            $barang = DB::table('home')->where('namaBarang', $request->nama_barang)->first();
            if ($barang) {
                $selisihStok = $request->jumlah - $jumlahLama;
                DB::table('home')->where('namaBarang', $request->nama_barang)->update([
                    'stok' => $barang->stok + $selisihStok
                ]);
            }

            return redirect()->route('barangMasuk.index')
                ->with('success', 'Barang masuk berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::find($id);
        if (!$barangMasuk) {
            abort(404);
        }

        BarangMasuk::deleteData($id);

        return redirect()->route('barangMasuk.index')
            ->with('success', 'Barang masuk berhasil dihapus!');
    }
}
