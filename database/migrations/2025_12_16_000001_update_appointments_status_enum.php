<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Allow additional statuses used by the app (waiting, in_progress).
     */
    public function up(): void
    {
        // Postgres: the original enum is implemented via a CHECK constraint.
        // Expand it to include waiting and in_progress to match application logic.
        DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
                DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show', 'waiting', 'in_progress'))");
    }

    /**
     * Revert to the original status set.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
        DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show'))");
    }
};


