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
        // Create users table
        DB::statement('
            CREATE TABLE users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                email_verified_at TIMESTAMP NULL DEFAULT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM("admin", "staff") NOT NULL DEFAULT "staff",
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL
            )
        ');

        // Create password reset tokens table
        DB::statement('
            CREATE TABLE password_reset_tokens (
                email VARCHAR(255) PRIMARY KEY,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL DEFAULT NULL
            )
        ');

        // Create sessions table
        DB::statement('
            CREATE TABLE sessions (
                id VARCHAR(255) PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload LONGTEXT NOT NULL,
                last_activity INT NOT NULL,
                INDEX sessions_user_id_index (user_id),
                INDEX sessions_last_activity_index (last_activity),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');
    }

    /**
     * Reverse the migrations.
     */

};
