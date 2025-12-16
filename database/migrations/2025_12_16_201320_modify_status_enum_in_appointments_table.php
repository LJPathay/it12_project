<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop existing constraint if it exists (Laravel default naming)
            \DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
            // Add new constraint including 'blocked'
            \DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status::text IN ('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show', 'blocked'))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
             \DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
             \DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status::text IN ('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show'))");
        });
    }
};
