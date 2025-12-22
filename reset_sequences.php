<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$connectionName = 'pgsql_online'; // The connection reported in error

try {
    echo "Resetting sequences for connection: $connectionName\n";
    $db = DB::connection($connectionName);
    
    // Get all tables
    $tables = [
        'appointments',
        'patients',
        'services',
        'inventory',
        'inventory_transactions',
        'inventory_batches',
        'announcements',
        'admins',
        'super_admins',
        'patient_immunizations'
    ];

    foreach ($tables as $table) {
        if (Schema::connection($connectionName)->hasTable($table)) {
            echo "Processing table: $table... ";
            
            // PostgreSQL specific sequence reset
            // This selects the max ID and sets the next value of the sequence to max+1
            $result = $db->statement("
                SELECT setval(
                    pg_get_serial_sequence('$table', 'id'), 
                    COALESCE((SELECT MAX(id) FROM \"$table\"), 1)
                )
            ");
            
            if ($result) {
                echo "Success!\n";
            } else {
                echo "Failed to reset sequence.\n";
            }
        } else {
            echo "Table $table does not exist. Skipping.\n";
        }
    }

    echo "\nAll sequences checked and reset where possible.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
