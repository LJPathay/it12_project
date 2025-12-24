<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // ISO-8601: Monday=1 ... Sunday=7
        $map = [
            1 => ['General Checkup', 'Comprehensive health checkups and consultation for all ages.'],
            2 => ['Prenatal', 'Regular checkups and guidance for a healthy pregnancy.'],
            3 => ['Medical Check-up', 'Routine assessments to monitor and maintain your health.'],
            4 => ['Immunization', 'Vaccinations for preventable diseases in children and adults.'],
            5 => ['Family Planning', 'Counseling and services for reproductive health.'],
        ];

        foreach ($map as $day => [$name, $desc]) {
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");

            if ($driver === 'pgsql') {
                $exists = \Illuminate\Support\Facades\DB::table('services')
                    ->where('day_of_week', $day)
                    ->exists();

                if ($exists) {
                    \Illuminate\Support\Facades\DB::table('services')
                        ->where('day_of_week', $day)
                        ->update([
                            'name' => $name,
                            'description' => $desc,
                            'active' => \Illuminate\Support\Facades\DB::raw('true'),
                            'updated_at' => now(),
                        ]);
                } else {
                    \Illuminate\Support\Facades\DB::table('services')->insert([
                        'day_of_week' => $day,
                        'name' => $name,
                        'description' => $desc,
                        'active' => \Illuminate\Support\Facades\DB::raw('true'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                Service::updateOrCreate(
                    ['day_of_week' => $day],
                    ['name' => $name, 'description' => $desc, 'active' => true]
                );
            }
        }
    }
}


