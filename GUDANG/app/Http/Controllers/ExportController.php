<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Home;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $homes = Home::all();

        // Fungsi untuk menyimpan gambar sementara
        function storeTempImage($imageData, $filename) {
            $tempDir = storage_path('app/public/temp_images');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            $filePath = $tempDir . '/' . $filename;
            Storage::disk('public')->put($filePath, base64_decode(substr($imageData, strpos($imageData, ",") + 1)));
            return asset('storage/temp_images/' . $filename);
        }

        // Modifikasi loop foreach untuk menambahkan URL gambar sementara
        foreach ($homes as &$index) { // Gunakan referensi (&) agar perubahan tersimpan
            $imagePath = storage_path('app/public/gambar/' . $index->gambar);
            if (file_exists($imagePath)) {
                $index->imageUrl = storeTempImage(base64_encode(file_get_contents($imagePath)), $index->id . '.' . pathinfo($index->gambar, PATHINFO_EXTENSION));
            } else {
                $index->imageUrl = asset('storage/gambar/default.png');
            }
        }

        $tempDir = storage_path('app/public/temp_images');
        if (is_dir($tempDir)) {
            Storage::disk('public')->deleteDirectory('temp_images');
        }

        return view('stock.index', compact('homes'));
    }
}
