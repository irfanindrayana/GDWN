<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            CREATE TABLE home (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                namaBarang VARCHAR(255) NOT NULL,
                deskripsi VARCHAR(255) NOT NULL,
                stok INT NOT NULL,
                gambar VARCHAR(255) NULL,
                stok_minimal INT NOT NULL DEFAULT 5,
                has_variant BOOLEAN NULL DEFAULT false,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS home');
    }
};
