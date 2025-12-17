<?php

namespace App\Helpers;

use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentHelper
{
    /**
     * Get all available time slots for a given date
     * 
     * @param string $date
     * @return array
     */
    public static function getAvailableSlots($date, $serviceType = null)
    {
        $timeSlots = self::getAllTimeSlots();
        $selectedDate = Carbon::parse($date);
        $isToday = $selectedDate->isToday();

        // Define limits per service
        $limits = [
            'Immunization' => ['daily' => 25, 'slot' => 4],
            'Medical Checkup' => ['daily' => 15, 'slot' => 2],
            'def' => ['daily' => 10, 'slot' => 1] // Default
        ];
        
        $limitConfig = $limits[$serviceType] ?? $limits['def'];
        $dailyLimit = $limitConfig['daily'];
        $slotLimit = $limitConfig['slot'];

        // Check if date is blocked (Doctor Unavailable)
        $isBlocked = Appointment::whereDate('appointment_date', $date)
            ->where('status', 'blocked')
            ->exists();
            
        if ($isBlocked) {
            // Return all slots as unavailable
            $availableSlots = [];
            foreach ($timeSlots as $slot) {
                $availableSlots[] = [
                    'time' => $slot['time'],
                    'display' => $slot['display'],
                    'available' => false,
                    'occupied_count' => 0,
                    'is_past' => false,
                    'capacity' => 0,
                    'is_blocked' => true
                ];
            }
            return $availableSlots;
        }

        // Get daily total for this service
        $dailyTotalQuery = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['approved', 'completed', 'waiting']);
            
        if ($serviceType) {
            $dailyTotalQuery->where('service_type', $serviceType);
        }
        $currentDailyTotal = $dailyTotalQuery->count();
        $isDailyFull = $currentDailyTotal >= $dailyLimit;

        // Get slot counts for this service
        $countsQuery = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['approved', 'completed', 'waiting']);
            
        if ($serviceType) {
            $countsQuery->where('service_type', $serviceType);
        }

        $counts = $countsQuery->selectRaw('appointment_time, count(*) as total')
            ->groupBy('appointment_time')
            ->pluck('total', 'appointment_time');

        // Map counts to normalized time strings (H:i)
        $normalizedCounts = [];
        foreach ($counts as $time => $count) {
            $normalizedTime = substr($time, 0, 5); // Convert 08:00:00 to 08:00
            $normalizedCounts[$normalizedTime] = $count;
        }

        $availableSlots = [];
        foreach ($timeSlots as $slot) {
            $count = $normalizedCounts[$slot['time']] ?? 0;
            
            // Check if time passed
            $isPast = false;
            if ($isToday) {
                $slotTime = Carbon::parse($date . ' ' . $slot['time']);
                $isPast = $slotTime->isPast();
            }

            // Available if: Not Past AND Daily Not Full AND Slot Not Full
            $available = !$isPast && !$isDailyFull && ($count < $slotLimit);

            $availableSlots[] = [
                'time' => $slot['time'],
                'display' => $slot['display'],
                'available' => $available,
                'occupied_count' => $count,
                'is_past' => $isPast,
                'capacity' => $slotLimit
            ];
        }

        return $availableSlots;
    }

    /**
     * Get all predefined time slots (30-minute intervals)
     * 
     * @return array
     */
    public static function getAllTimeSlots()
    {
        return [
            ['time' => '08:00', 'display' => '8:00 AM'],
            ['time' => '08:30', 'display' => '8:30 AM'],
            ['time' => '09:00', 'display' => '9:00 AM'],
            ['time' => '09:30', 'display' => '9:30 AM'],
            ['time' => '10:00', 'display' => '10:00 AM'],
            ['time' => '10:30', 'display' => '10:30 AM'],
            ['time' => '11:00', 'display' => '11:00 AM'],
            ['time' => '11:30', 'display' => '11:30 AM'],
            // Lunch Break 12-1
            ['time' => '13:00', 'display' => '1:00 PM'],
            ['time' => '13:30', 'display' => '1:30 PM'],
            ['time' => '14:00', 'display' => '2:00 PM'],
            ['time' => '14:30', 'display' => '2:30 PM'],
            ['time' => '15:00', 'display' => '3:00 PM'],
            ['time' => '15:30', 'display' => '3:30 PM'],
            ['time' => '16:00', 'display' => '4:00 PM'],
        ];
    }

    /**
     * Get calendar data for a month
     * 
     * @param int $year
     * @param int $month
     * @return array
     */
    public static function getCalendarData($year, $month)
    {
        $calendar = [];
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        // Optimize: Use DB aggregation to count slots per day
        $dailyCounts = Appointment::whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['approved', 'completed'])
            ->selectRaw('appointment_date, count(*) as total')
            ->groupBy('appointment_date')
            ->pluck('total', 'appointment_date');

        $daysInMonth = $startDate->daysInMonth;
        $totalSlotsPerDay = count(self::getAllTimeSlots());

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($year, $month, $day);
            $dateString = $currentDate->format('Y-m-d');

            $occupiedCount = $dailyCounts[$dateString] ?? 0;
            
            // Check if day is blocked
            $isBlocked = Appointment::whereDate('appointment_date', $dateString)
                ->where('status', 'blocked')
                ->exists();

            if ($isBlocked) {
                 $availableSlots = 0;
            } else {
                 $availableSlots = $totalSlotsPerDay - $occupiedCount;
            }
            
            // For today's date, check if all time slots are in the past
            $allSlotsPast = false;
            if ($currentDate->isToday() && !$isBlocked) {
                $now = Carbon::now();
                $allSlots = self::getAllTimeSlots();
                $futureSlots = 0;
                
                foreach ($allSlots as $slot) {
                    $slotTime = Carbon::parse($dateString . ' ' . $slot['time']);
                    if ($slotTime->isFuture()) {
                        $futureSlots++;
                    }
                }
                
                // If no future slots, mark as all slots past
                if ($futureSlots === 0) {
                    $availableSlots = 0;
                    $allSlotsPast = true;
                }
            }

            $calendar[] = [
                'date' => $dateString,
                'day' => $day,
                'total_slots' => $totalSlotsPerDay,
                'occupied_slots' => $occupiedCount,
                'available_slots' => $availableSlots,
                'is_fully_occupied' => $availableSlots <= 0 && !$allSlotsPast,
                'is_blocked' => $isBlocked,
                'is_weekend' => $currentDate->isWeekend(),
                'is_past' => ($currentDate->isPast() && !$currentDate->isToday()) || $allSlotsPast,
            ];
        }

        return $calendar;
    }
}
