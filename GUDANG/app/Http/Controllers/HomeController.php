<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Home;
use App\Models\Variant;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $datastok = Home::getLowStock();
        $filterNamaBarang = $request->input('filter_namaBarang');
        
        if ($filterNamaBarang && $filterNamaBarang !== 'all') {
            $homes = DB::table('home')->where('namaBarang', $filterNamaBarang)->get();
        } else {
            $homes = Home::getAll();
        }
        
        $allNamaBarang = DB::table('home')->distinct()->pluck('namaBarang');
        return view('home.index', compact('datastok', 'homes', 'allNamaBarang', 'filterNamaBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaBarang' => 'required',
            'deskripsi' => 'required',
            'stok' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'namaBarang' => $request->namaBarang,
            'deskripsi' => $request->deskripsi,
            'stok' => $request->stok
        ];

        // Handle upload gambar
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $nama_gambar = time() . '.' . $gambar->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('gambar', $gambar, $nama_gambar);
            $data['gambar'] = $nama_gambar;
        }

        Home::create($data);

        return redirect()->route('home.index')
            ->with('success', 'Barang "' . $request->namaBarang . '" berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'namaBarang' => 'required',
            'deskripsi' => 'required',
            'stok' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $home = Home::find($id);
        if (!$home) {
            abort(404);
        }
        
        $oldNamaBarang = $home->namaBarang;

        $data = [
            'namaBarang' => $request->namaBarang,
            'deskripsi' => $request->deskripsi,
            'stok' => $request->stok
        ];

        if ($request->hasFile('gambar')) {
            if ($home->gambar && Storage::disk('public')->exists('gambar/' . $home->gambar)) {
                Storage::disk('public')->delete('gambar/' . $home->gambar);
            }

            $gambar = $request->file('gambar');
            $nama_gambar = time() . '.' . $gambar->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('gambar', $gambar, $nama_gambar);
            $data['gambar'] = $nama_gambar;
        }

        Home::updateData($id, $data);

        return redirect()->route('home.index')
            ->with('success', 'Barang "' . $oldNamaBarang . '" berhasil diupdate menjadi "' . $request->namaBarang . '"!');
    }

    public function destroy($id)
    {
        $home = Home::find($id);
        if (!$home) {
            abort(404);
        }

        $namaBarang = $home->namaBarang;

        if ($home->gambar && Storage::disk('public')->exists('gambar/' . $home->gambar)) {
            Storage::disk('public')->delete('gambar/' . $home->gambar);
        }

        Home::deleteData($id);

        return redirect()->route('home.index')
            ->with('success', 'Barang "' . $namaBarang . '" berhasil dihapus!');
    }

    public function searchBarang(Request $request)
    {
        $query = $request->get('query');
        $barang = DB::table('home')
            ->where('namaBarang', 'LIKE', "%{$query}%")
            ->select('id', 'namaBarang', 'gambar', 'stok')
            ->get();
        
        return response()->json($barang);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $barang = DB::table('home')
            ->where('namaBarang', 'LIKE', "%{$query}%")
            ->select('id', 'namaBarang', 'gambar', 'stok')
            ->get();
        
        return response()->json($barang);
    }

    public function export()
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home.index')
                           ->with('error', 'Unauthorized action. This page is only accessible by admin.');
        }

        $homes = Home::getAll();
        return view('stock.index', compact('homes'));
    }

    public function detail($id)
    {
        $barang = Home::getWithVariants($id);
        if (!$barang) {
            abort(404);
        }
        
        $barangMasuk = BarangMasuk::getByNamaBarang($barang->namaBarang);
        $barangKeluar = BarangKeluar::getByNamaBarang($barang->namaBarang);
                return view('home.detail', compact('barang', 'barangMasuk', 'barangKeluar'));
    }

    public function updateStokMinimal(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home.detail', $id)
                ->with('error', 'Hanya admin yang dapat mengubah stok minimal.');
        }

        $request->validate([
            'stok_minimal' => 'required|integer|min:1'
        ]);

        $barang = Home::find($id);
        if (!$barang) {
            abort(404);
        }

        Home::updateData($id, ['stok_minimal' => $request->stok_minimal]);

        return redirect()->route('home.detail', $id)
            ->with('success', 'Stok minimal untuk "' . $barang->namaBarang . '" berhasil diupdate!');
    }

    public function enableVariants($id)
    {
        $barang = Home::find($id);
        if (!$barang) {
            abort(404);
        }

        Home::updateData($id, ['has_variant' => true]);
        
        return response()->json(['success' => true]);
    }

    public function disableVariants($id)
    {
        $barang = Home::find($id);
        if (!$barang) {
            abort(404);
        }

        Variant::deleteByBarangId($id);
        Home::updateData($id, ['has_variant' => false]);
        
        return response()->json(['success' => true]);
    }

    public function saveVariants(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengatur varian.'
            ], 403);
        }

        $barang = Home::find($id);
        if (!$barang) {
            abort(404);
        }
        
        $totalQuantity = collect($request->variants)->sum('quantity');
        if ($totalQuantity > $barang->stok) {
            return response()->json([
                'success' => false,
                'message' => 'Total kuantitas melebihi stok yang tersedia.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            Variant::deleteByBarangId($id);

            foreach ($request->variants as $variantData) {
                if ($variantData['quantity'] > 0) {
                    $variant = [
                        'barang_id' => $id,
                        'attribute_1' => $variantData['variant'][0]['type'],
                        'value_1' => $variantData['variant'][0]['value'],
                        'quantity' => $variantData['quantity']
                    ];

                    if (isset($variantData['variant'][1])) {
                        $variant['attribute_2'] = $variantData['variant'][1]['type'];
                        $variant['value_2'] = $variantData['variant'][1]['value'];
                    }

                    Variant::create($variant);
                }
            }

            Home::updateData($id, ['has_variant' => true]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteVariant($id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        try {
            Variant::deleteData($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateVariantQuantity($id, Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        try {
            Variant::updateQuantity($id, $request->quantity);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateVariant($id, Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        try {
            $data = [
                'attribute_1' => $request->attribute_1,
                'value_1' => $request->value_1
            ];

            if ($request->has('attribute_2')) {
                $data['attribute_2'] = $request->attribute_2;
                $data['value_2'] = $request->value_2;
            }

            Variant::updateData($id, $data);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
