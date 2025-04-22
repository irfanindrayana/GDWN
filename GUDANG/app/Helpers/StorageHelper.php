<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageHelper
{
    public static function createStorageLink()
    {
        try {
            // Hapus symbolic link yang ada jika sudah ada
            $publicStoragePath = public_path('storage');
            $targetPath = storage_path('app/public');

            // Pastikan target directory exists
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            // Hapus symbolic link lama jika ada
            if (is_link($publicStoragePath)) {
                unlink($publicStoragePath);
            }

            // Hapus directory jika ada
            if (is_dir($publicStoragePath)) {
                File::deleteDirectory($publicStoragePath);
            }

            // Buat symbolic link baru
            if (@symlink($targetPath, $publicStoragePath)) {
                return true;
            }

            Log::error("Failed to create symlink from {$targetPath} to {$publicStoragePath}");
            return false;
        } catch (\Exception $e) {
            Log::error('Error in createStorageLink: ' . $e->getMessage());
            return false;
        }
    }

    public static function ensureGambarDirectoryExists()
    {
        try {
            $storagePath = storage_path('app/public/gambar');
            
            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0755, true);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error in ensureGambarDirectoryExists: ' . $e->getMessage());
            return false;
        }
    }

    public static function moveImageToCorrectLocation($fileName)
    {
        try {
            self::ensureGambarDirectoryExists();
            
            $storagePath = storage_path('app/public/gambar/' . $fileName);
            
            if (!File::exists($storagePath)) {
                Log::error("Source file does not exist: {$storagePath}");
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error in moveImageToCorrectLocation: ' . $e->getMessage());
            return false;
        }
    }
} 