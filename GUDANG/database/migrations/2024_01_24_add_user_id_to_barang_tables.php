<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Check if barang_keluars table exists
        $tableExists = DB::select("SHOW TABLES LIKE 'barang_keluars'");
        
        if (empty($tableExists)) {
            // Create barang_keluars table if it doesn't exist
            DB::statement('
                CREATE TABLE barang_keluars (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT UNSIGNED NULL,
                    tanggal DATE NOT NULL,
                    gambar VARCHAR(255) NULL,
                    nama_barang VARCHAR(255) NOT NULL,
                    jumlah INT NOT NULL,
                    deskripsi TEXT NULL,
                    admin VARCHAR(255) NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                )
            ');
        } else {
            // Check if user_id column exists
            $columnExists = DB::select("SHOW COLUMNS FROM barang_keluars LIKE 'user_id'");
            
            if (empty($columnExists)) {
                // Add user_id column if it doesn't exist
                DB::statement('
                    ALTER TABLE barang_keluars
                    ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER id,
                    ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                ');
            }
        }

        // Check if barang_masuks table exists
        $tableExists = DB::select("SHOW TABLES LIKE 'barang_masuks'");
        
        if (empty($tableExists)) {
            // Create barang_masuks table if it doesn't exist
            DB::statement('
                CREATE TABLE barang_masuks (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT UNSIGNED NULL,
                    tanggal DATE NOT NULL,
                    gambar VARCHAR(255) NULL,
                    nama_barang VARCHAR(255) NOT NULL,
                    jumlah INT NOT NULL,
                    deskripsi TEXT NULL,
                    admin VARCHAR(255) NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                )
            ');
        } else {
            // Check if user_id column exists
            $columnExists = DB::select("SHOW COLUMNS FROM barang_masuks LIKE 'user_id'");
            
            if (empty($columnExists)) {
                // Add user_id column if it doesn't exist
                DB::statement('
                    ALTER TABLE barang_masuks
                    ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER id,
                    ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                ');
            }
        }
    }


}; 