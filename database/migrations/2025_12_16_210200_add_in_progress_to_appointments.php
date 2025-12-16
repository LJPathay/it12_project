<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Postgres supports check constraints. We drop the old one and add the new one.
            DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status::text IN ('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show', 'blocked', 'in_progress'))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
            // Revert to previous list (excluding in_progress logic would ideally depend on data, but for schema revert:)
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status::text IN ('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show', 'blocked'))");
        });
    }
};
