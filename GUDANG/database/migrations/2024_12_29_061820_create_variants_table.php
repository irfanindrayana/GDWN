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
        // Create variants table
        DB::statement('
            CREATE TABLE variants (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                barang_id BIGINT UNSIGNED NOT NULL,
                attribute_1 VARCHAR(255) NOT NULL,
                value_1 VARCHAR(255) NOT NULL,
                attribute_2 VARCHAR(255) NULL,
                value_2 VARCHAR(255) NULL,
                quantity INT NOT NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                FOREIGN KEY (barang_id) REFERENCES home(id) ON DELETE CASCADE
            )
        ');

        // Add has_variant column to home table
        // DB::statement('
        //     ALTER TABLE home 
        //     ADD COLUMN has_variant BOOLEAN NOT NULL DEFAULT false AFTER stok_minimal
        // ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop variants table
        DB::statement('DROP TABLE IF EXISTS variants');
        
        // Drop has_variant column from home table
        DB::statement('
            ALTER TABLE home 
            DROP COLUMN has_variant
        ');
    }
};
