<?php

namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PatientReportExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Summary Sheet
        $sheets[] = new class($this->startDate, $this->endDate) implements FromCollection, WithHeadings, WithTitle {
            protected $start;
            protected $end;

            public function __construct($start, $end)
            {
                $this->start = $start;
                $this->end = $end;
            }

            public function collection()
            {
                $totalPatients = Patient::count();
                $maleCount = Patient::where('gender', 'male')->count();
                $femaleCount = Patient::where('gender', 'female')->count();
                
                // "New" count depends on the date range
                $newPatients = Patient::whereBetween('created_at', [$this->start, $this->end])->count();

                $data = new Collection([
                    ['Metric', 'Count', 'Percentage', 'Notes'],
                    ['Total Patients', $totalPatients, '100%', 'Snapshot'],
                    ['Male Patients', $maleCount, $totalPatients > 0 ? round(($maleCount / $totalPatients) * 100, 1) . '%' : '0%', 'Snapshot'],
                    ['Female Patients', $femaleCount, $totalPatients > 0 ? round(($femaleCount / $totalPatients) * 100, 1) . '%' : '0%', 'Snapshot'],
                    ['New Patients (In Range)', $newPatients, '-', 'Registered ' . $this->start->format('Y-m-d') . ' to ' . $this->end->format('Y-m-d')],
                    ['', '', '', ''],
                    ['--- Age Distribution ---', '', '', ''],
                ]);

                // Age Distribution
                $ageGroups = [
                    '0-17' => Patient::whereBetween('age', [0, 17])->count(),
                    '18-30' => Patient::whereBetween('age', [18, 30])->count(),
                    '31-50' => Patient::whereBetween('age', [31, 50])->count(),
                    '51-70' => Patient::whereBetween('age', [51, 70])->count(),
                    '71+' => Patient::where('age', '>', 70)->count(),
                ];

                foreach ($ageGroups as $group => $count) {
                    $pct = $totalPatients > 0 ? round(($count / $totalPatients) * 100, 1) . '%' : '0%';
                    $data->push([$group . ' years', $count, $pct, '']);
                }

                $data->push(['', '', '', '']);
                $data->push(['--- Barangay Distribution ---', '', '', '']);

                // Barangay Distribution
                $barangays = Patient::selectRaw('barangay, count(*) as count')->groupBy('barangay')->get();
                foreach ($barangays as $bg) {
                    $pct = $totalPatients > 0 ? round(($bg->count / $totalPatients) * 100, 1) . '%' : '0%';
                    $data->push([$bg->barangay, $bg->count, $pct, '']);
                }

                return $data;
            }

            public function headings(): array
            {
                return ['Report Summary', 'Value', '', ''];
            }

            public function title(): string
            {
                return 'Summary';
            }
        };

        // 2. Top Patients Sheet (Filtered by Date Range)
        $sheets[] = new class($this->startDate, $this->endDate) implements FromCollection, WithHeadings, WithTitle {
            protected $start;
            protected $end;

            public function __construct($start, $end)
            {
                $this->start = $start;
                $this->end = $end;
            }

            public function collection()
            {
                return Patient::withCount(['appointments' => function ($query) {
                        $query->whereBetween('appointment_date', [$this->start, $this->end]);
                    }])
                    ->whereHas('appointments', function ($query) {
                        $query->whereBetween('appointment_date', [$this->start, $this->end]);
                    })
                    ->orderByDesc('appointments_count')
                    ->limit(50)
                    ->get()
                    ->map(function ($p) {
                        return [
                            'ID' => $p->id,
                            'Name' => $p->name,
                            'Appointments in Period' => $p->appointments_count,
                            'Phone' => $p->phone,
                        ];
                    });
            }

            public function headings(): array
            {
                return ['Patient ID', 'Name', 'Appointments Count', 'Phone'];
            }

            public function title(): string
            {
                return 'Top Patients';
            }
        };

        // 3. Recent Registrations (Filtered by Date Range)
        $sheets[] = new class($this->startDate, $this->endDate) implements FromCollection, WithHeadings, WithTitle {
            protected $start;
            protected $end;

            public function __construct($start, $end)
            {
                $this->start = $start;
                $this->end = $end;
            }

            public function collection()
            {
                return Patient::whereBetween('created_at', [$this->start, $this->end])
                    ->latest()
                    ->get()
                    ->map(function ($p) {
                        return [
                            'ID' => $p->id,
                            'Name' => $p->name,
                            'Registered Date' => optional($p->created_at)->format('Y-m-d H:i:s') ?? 'N/A',
                            'Registered Time Ago' => optional($p->created_at)->diffForHumans() ?? 'N/A',
                        ];
                    });
            }

            public function headings(): array
            {
                return ['Patient ID', 'Name', 'Registration Timestamp', 'Time Ago'];
            }

            public function title(): string
            {
                return 'New Registrations';
            }
        };

        // 4. All Patients (Raw Data Snapshot)
        $sheets[] = new class implements FromCollection, WithHeadings, WithTitle {
            public function collection()
            {
                return Patient::select('id', 'name', 'gender', 'age', 'barangay', 'phone', 'created_at')
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(function ($p) {
                        return [
                            'ID' => $p->id,
                            'Name' => $p->name,
                            'Gender' => ucfirst($p->gender),
                            'Age' => $p->age,
                            'Barangay' => $p->barangay,
                            'Phone' => $p->phone,
                            'Registered' => optional($p->created_at)->format('Y-m-d') ?? 'N/A',
                        ];
                    });
            }

            public function headings(): array
            {
                return ['ID', 'Name', 'Gender', 'Age', 'Barangay', 'Phone', 'Registered'];
            }

            public function title(): string
            {
                return 'All Patients';
            }
        };

        return $sheets;
    }
}
