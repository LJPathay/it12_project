<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\AppointmentHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_available_slots_filters_past_time()
    {
        // Set "now" to 10:00 AM today
        $date = Carbon::today()->format('Y-m-d');
        $now = Carbon::parse($date . ' 10:00:00');
        Carbon::setTestNow($now);

        $slots = AppointmentHelper::getAvailableSlots($date);

        // Filter returned slots to just the time strings
        $times = array_column($slots, 'time');

        // Check that 08:00, 08:30, 09:00, 09:30 are NOT present
        $this->assertNotContains('08:00', $times);
        $this->assertNotContains('08:30', $times);
        $this->assertNotContains('09:00', $times);
        $this->assertNotContains('09:30', $times);

        // Check that 10:00 (current time) is also past? 
        // Logic was strict isPast(). 10:00 == 10:00 is NOT past.
        // So 10:00 SHOULD be present.
        $this->assertContains('10:00', $times);

        // Check that future slots ARE present
        $this->assertContains('10:30', $times);
        $this->assertContains('11:00', $times);
    }

    public function test_get_available_slots_shows_all_for_tomorrow()
    {
        // Set "now" to 10:00 AM today
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::parse($today . ' 10:00:00');
        Carbon::setTestNow($now);

        // Check for tomorrow
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $slots = AppointmentHelper::getAvailableSlots($tomorrow);

        $times = array_column($slots, 'time');

        // All morning slots should be present for tomorrow
        $this->assertContains('08:00', $times);
        $this->assertContains('09:00', $times);
    }
}
