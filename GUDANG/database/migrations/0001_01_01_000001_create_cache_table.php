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
        // Create cache table
        DB::statement('
            CREATE TABLE cache (
                `key` VARCHAR(255) PRIMARY KEY,
                value MEDIUMTEXT NOT NULL,
                expiration INT NOT NULL
            )
        ');

        // Create cache_locks table
        DB::statement('
            CREATE TABLE cache_locks (
                `key` VARCHAR(255) PRIMARY KEY,
                owner VARCHAR(255) NOT NULL,
                expiration INT NOT NULL
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS cache');
        DB::statement('DROP TABLE IF EXISTS cache_locks');
    }
};
