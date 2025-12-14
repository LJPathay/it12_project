<?php

require __DIR__ . '/vendor/autoload.php';

use App\Helpers\AppointmentHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test Case 1: Past times should be filtered
echo "Testing Past Times filtering...\n";

// Mock today as 2025-12-12 10:00:00
$today = '2025-12-12';
$now = Carbon::parse("$today 10:00:00");
Carbon::setTestNow($now);

$slots = AppointmentHelper::getAvailableSlots($today);
$times = array_column($slots, 'time');

echo "Current Time: " . $now->format('H:i') . "\n";
echo "Available Slots:\n";
print_r($times);

if (in_array('09:00', $times)) {
    echo "FAIL: 09:00 should be filtered out.\n";
} else {
    echo "PASS: 09:00 is filtered out.\n";
}

if (in_array('11:00', $times)) {
    echo "PASS: 11:00 is available.\n";
} else {
    echo "FAIL: 11:00 should be available.\n";
}

// Test Case 2: Future date should show all
echo "\nTesting Future Date...\n";
$tomorrow = Carbon::tomorrow()->format('Y-m-d');
$slotsTomorrow = AppointmentHelper::getAvailableSlots($tomorrow);
$timesTomorrow = array_column($slotsTomorrow, 'time');

if (in_array('09:00', $timesTomorrow)) {
    echo "PASS: 09:00 is available tomorrow.\n";
} else {
    echo "FAIL: 09:00 should be available tomorrow.\n";
}
