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
            ALTER TABLE barang_masuks 
            MODIFY COLUMN tanggal DATETIME NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('
            ALTER TABLE barang_masuks 
            MODIFY COLUMN tanggal DATE NOT NULL
        ');
    }
};
