<?php

use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\Appointment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Default Connection: " . config('database.default') . "\n";
    echo "Driver: " . DB::connection()->getDriverName() . "\n";
    
    echo "Testing Service::whereRaw('active = TRUE')...\n";
    $service = Service::whereRaw('active = TRUE')->first();
    echo "Success!\n";

    echo "Testing Appointment::whereRaw('is_walk_in = FALSE')...\n";
    $appointment = Appointment::whereRaw('is_walk_in = FALSE')->first();
    echo "Success!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
