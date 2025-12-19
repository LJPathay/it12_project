<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\InventoryBatch;
use App\Models\Patient;
use App\Helpers\AppointmentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AppointmentApproved;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentRangeExport;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends Controller
{
    public function dashboard()
    {
        $adminId = Auth::guard('admin')->id();
        $totalPatients = Patient::query()->count();
        $newPatientsThisMonth = Patient::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Today metrics (Personalized)
        $todayAppointments = Appointment::whereDate('appointment_date', today())
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->count();
        $todayCompleted = Appointment::whereDate('appointment_date', today())
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->where('status', 'completed')
            ->count();
        $todayPending = Appointment::whereDate('appointment_date', today())
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->whereIn('status', ['pending', 'approved', 'waiting', 'in_progress', 'rescheduled'])
            ->count();

        // Get today's appointment list (Personalized)
        $todaysAppointments = Appointment::whereDate('appointment_date', today())
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->whereIn('status', ['approved', 'waiting', 'completed']) // Exclude pending
            ->orderBy('appointment_time')
            ->get();

        // Inventory metrics (Keep global as stock is shared)
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = Inventory::where('current_stock', 0)->count();
        $expiringSoonCount = Inventory::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->count();

        // Monthly services metrics (Personalized)
        $now = now();
        $lastMonth = $now->copy()->subMonth();

        $monthlyServices = Appointment::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->count();
        $lastMonthServices = Appointment::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->count();
        $servicesChange = $lastMonthServices > 0
            ? round((($monthlyServices - $lastMonthServices) / $lastMonthServices) * 100)
            : null;

        // Monthly patient growth metrics (Stay global)
        $patientsThisMonth = Patient::query()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();
        $patientsLastMonth = Patient::query()
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
        $patientsChange = $patientsLastMonth > 0
            ? round((($patientsThisMonth - $patientsLastMonth) / $patientsLastMonth) * 100)
            : null;

        $recentAppointments = Appointment::with('user')
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->latest()
            ->limit(5)
            ->get();
        $lowStockInventory = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->limit(5)->get();

        // Patients by Barangay data for the doughnut chart (Stay global)
        $patientsByBarangay = Patient::query()
            ->whereNotNull('barangay')
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // --- Chart Data Preparation (Personalized) ---

        $driver = DB::getDriverName();

        // Weekly (Current Week: Sun-Sat)
        $dayOfWeekSql = $driver === 'pgsql' ? 'EXTRACT(DOW FROM appointment_date) + 1' : 'DAYOFWEEK(appointment_date)';
        $weeklyOverview = Appointment::selectRaw("$dayOfWeekSql as label_key, count(*) as count")
            ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->groupBy('label_key')
            ->pluck('count', 'label_key')
            ->toArray();

        // Monthly (Current Month: 1-31)
        $daySql = $driver === 'pgsql' ? 'EXTRACT(DAY FROM appointment_date)' : 'DAY(appointment_date)';
        $monthlyOverview = Appointment::selectRaw("$daySql as label_key, count(*) as count")
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->groupBy('label_key')
            ->pluck('count', 'label_key')
            ->toArray();

        // Yearly (Current Year: 1-12)
        $monthSql = $driver === 'pgsql' ? 'EXTRACT(MONTH FROM appointment_date)' : 'MONTH(appointment_date)';
        $yearlyOverview = Appointment::selectRaw("$monthSql as label_key, count(*) as count")
            ->whereYear('appointment_date', now()->year)
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->groupBy('label_key')
            ->pluck('count', 'label_key')
            ->toArray();

        // Weekly (Service breakdown per day)
        $weeklyServices = Appointment::selectRaw("$dayOfWeekSql as day, service_type, count(*) as count")
            ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->groupBy('day', 'service_type')
            ->get();

        // Monthly
        $monthlyServicesChart = Appointment::selectRaw('service_type, count(*) as count')
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->groupBy('service_type')
            ->get();

        // Yearly (Service breakdown per month)
        $yearlyServices = Appointment::selectRaw("$monthSql as month, service_type, count(*) as count")
            ->whereYear('appointment_date', now()->year)
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->groupBy('month', 'service_type')
            ->get();

        $chartData = [
            'overview' => [
                'weekly' => $weeklyOverview,
                'monthly' => $monthlyOverview,
                'yearly' => $yearlyOverview,
            ],
            'services' => [
                'weekly' => $weeklyServices,
                'monthly' => $monthlyServicesChart,
                'yearly' => $yearlyServices,
            ]
        ];

        // Count for notifications
        $adminId = Auth::guard('admin')->id();
        $totalPendingAction = Appointment::where('status', 'pending')
            ->where('approved_by_admin_id', $adminId)
            ->count();
        
        $totalSystemPending = Appointment::where('status', 'pending')->count();

        $appointmentsNeedingAction = Appointment::whereIn('status', ['pending', 'approved'])
            ->whereDate('appointment_date', '<=', today())
            ->where('approved_by_admin_id', $adminId)
            ->count();

        return view('admin.dashboard', compact(
            'totalPatients',
            'newPatientsThisMonth',
            'todayAppointments',
            'todayCompleted',
            'todayPending',
            'lowStockItems',
            'monthlyServices',
            'servicesChange',
            'patientsChange',
            'totalPendingAction',
            'totalSystemPending',
            'appointmentsNeedingAction',
            'recentAppointments',
            'lowStockInventory',
            'patientsByBarangay',
            'chartData',
            'outOfStockCount',
            'expiringSoonCount',
            'todaysAppointments',
            'totalPendingAction',
            'appointmentsNeedingAction'
        ));
    }

    public function patients()
    {
        $patients = Patient::query()
            ->with('appointments')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.patients', compact('patients'));
    }

    public function archivePatient(Patient $patient)
    {
        // Patients don't have a role field, they are all patients
        // No need to check role

        if ($patient->id === Auth::guard('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot archive your own account.');
        }

        $patient->delete();

        return redirect()->back()->with('success', 'Patient archived successfully.');
    }

    public function archivedPatients()
    {
        $patients = Patient::onlyTrashed()

            ->orderByDesc('deleted_at')
            ->paginate(10);

        return view('admin.patients-archive', compact('patients'));
    }

    public function restorePatient($id)
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->route('admin.patients.archive')->with('success', 'Patient restored successfully.');
    }

    public function createPatient(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|string|email|max:255|unique:patient',
            'gender' => 'required|in:male,female,other',
            'barangay' => [
                'required',
                Rule::in(['Barangay 11', 'Barangay 12', 'Other']),
            ],
            'barangay_other' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->barangay === 'Other'),
            ],
            'purok' => [
                'nullable',
                Rule::requiredIf(fn() => in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
                Rule::when(
                    $request->barangay === 'Barangay 11',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'])
                ),
                Rule::when(
                    $request->barangay === 'Barangay 12',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3'])
                ),
            ],
            'phone' => 'nullable|string|max:20',
            'birth_date' => [
                'required',
                'date',
                'before:today',
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ], [
            'name.regex' => 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
            'gender.required' => 'Please select a gender.',
            'barangay.in' => 'Please select Barangay 11, Barangay 12, or choose Other.',
            'barangay_other.required' => 'Please specify the barangay.',
            'purok.required' => 'Please select a purok for the chosen barangay.',
            'purok.in' => 'Please choose a valid purok option.',
            'birth_date.before' => 'Birth date must be in the past.',
        ]);

        $age = Carbon::parse($validated['birth_date'])->age;

        Patient::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'barangay' => $validated['barangay'],
            'barangay_other' => $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null,
            'purok' => $validated['barangay'] === 'Other' ? null : ($validated['purok'] ?? null),
            'phone' => $validated['phone'],
            'birth_date' => $validated['birth_date'],
            'age' => $age,
            'password' => Hash::make($validated['password']),
            'role' => 'user'
        ]);

        return redirect()->back()->with('success', 'Patient created successfully.');
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|string|email|max:255|unique:patient,email,' . $patient->id,
            'gender' => 'required|in:male,female,other',
            'barangay' => [
                'required',
                Rule::in(['Barangay 11', 'Barangay 12', 'Other']),
            ],
            'barangay_other' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->barangay === 'Other'),
            ],
            'purok' => [
                'nullable',
                Rule::requiredIf(fn() => in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
                Rule::when(
                    $request->barangay === 'Barangay 11',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'])
                ),
                Rule::when(
                    $request->barangay === 'Barangay 12',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3'])
                ),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => [
                'required',
                'date',
                'before:today',
            ],
            'password' => [
                'nullable',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ], [
            'name.regex' => 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
            'gender.required' => 'Please select a gender.',
            'barangay.in' => 'Please select Barangay 11, Barangay 12, or choose Other.',
            'barangay_other.required' => 'Please specify the barangay.',
            'purok.required' => 'Please select a purok for the chosen barangay.',
            'purok.in' => 'Please choose a valid purok option.',
            'birth_date.before' => 'Birth date must be in the past.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'barangay' => $validated['barangay'],
            'barangay_other' => $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null,
            'purok' => $validated['barangay'] === 'Other' ? null : ($validated['purok'] ?? null),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'birth_date' => $validated['birth_date'],
            'age' => Carbon::parse($validated['birth_date'])->age,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $patient->update($updateData);

        return redirect()->back()->with('success', 'Patient updated successfully.');
    }

    public function appointments(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $query = Appointment::with(['patient', 'approvedByAdmin'])
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false);

        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sort === 'date') {
            $query->orderBy('appointment_date', $direction)
                ->orderBy('appointment_time', $direction);
        } else {
            $query->orderByDesc('appointment_date')
                ->orderByDesc('appointment_time');
        }

        $searchInput = $request->input('search', $request->input('q'));
        if (filled($searchInput)) {
            $search = trim($searchInput);
            $query->where(function ($sub) use ($search) {
                $sub->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('patient_phone', 'like', "%{$search}%")
                    ->orWhere('service_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service')) {
            $query->where('service_type', $request->service);
        }

        if ($request->filled('from')) {
            $query->whereDate('appointment_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('appointment_date', '<=', $request->to);
        }

        $appointments = $query->get();

        // Populate services for filters and booking drawer
        $services = [
            'General Checkup',
            'Prenatal',
            'Medical Check-up',
            'Immunization',
            'Family Planning',
        ];
        if (class_exists(Service::class) && Schema::hasTable('services')) {
            $dbServices = Service::where('active', true)->pluck('name')->toArray();
            if (!empty($dbServices)) {
                $services = array_values(array_unique(array_merge($services, $dbServices)));
            }
        }

        // Simple availability metrics for today (all services)
        $todaySlots = 9; // per service per day
        $todayBooked = Appointment::whereDate('appointment_date', today())
            ->where('approved_by_admin_id', $adminId)
            ->where('status', '!=', 'cancelled')
            ->count();
        $todayCapacity = $todaySlots > 0 ? (int) min(100, round(($todayBooked / $todaySlots) * 100)) : 0;

        // Additional Stats for KPI Cards (Personalized)
        $pendingCount = Appointment::where('status', 'pending')
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->count();
        $todayCount = Appointment::whereDate('appointment_date', today())
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->count();
        $completedToday = Appointment::whereDate('appointment_date', today())
            ->where('status', 'completed')
            ->where('approved_by_admin_id', $adminId)
            ->where('is_walk_in', false)
            ->count();

        return view('admin.appointments', compact(
            'appointments', 
            'services', 
            'todaySlots', 
            'todayBooked', 
            'todayCapacity',
            'pendingCount',
            'todayCount',
            'completedToday'
        ));
    }

    public function createAppointment(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'service_type' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'patient_phone' => 'nullable|string|max:20',
            'patient_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Find the service by name
        $service = Service::where('name', $request->service_type)->first();
        if (!$service) {
            return redirect()->back()->with('error', 'Invalid service selected.');
        }

        // Enforce 9 slots per day per service (excluding cancelled)
        $existingCount = Appointment::whereDate('appointment_date', $request->appointment_date)
            ->where('service_type', $request->service_type)
            ->where('status', '!=', 'cancelled')
            ->count();
        if ($existingCount >= 9) {
            return redirect()->back()->with('error', 'No slots available for this service on the selected date.');
        }

        // If user_id is provided, link to registered patient; otherwise link to admin
        $userId = $request->filled('user_id') ? $request->user_id : Auth::guard('admin')->id();
        $isWalkIn = !$request->filled('user_id'); // Only walk-in if no user_id provided

        $appointment = Appointment::create([
            'patient_id' => $userId,
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone ?: '',
            'patient_address' => $request->patient_address ?: 'N/A',
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_type' => $request->service_type,
            'notes' => $request->notes,
            'is_walk_in' => $isWalkIn,
            'approved_by_admin_id' => ($assignedAdmin = AppointmentHelper::getLeastBusyAdmin()) ? $assignedAdmin->id : null,
            'status' => 'pending'
        ]);

        // Attach service to appointment_service pivot table
        \Log::info('Admin creating appointment - attaching service', [
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'service_name' => $service->name
        ]);
        
        $appointment->services()->attach($service->id);

        return redirect()->back()->with('success', 'Appointment created successfully.');
    }

    public function blockDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255'
        ]);

        // Check if already blocked
        $existing = Appointment::whereDate('appointment_date', $request->date)
            ->where('status', 'blocked')
            ->exists();
            
        if ($existing) {
            return redirect()->back()->with('error', 'Date is already blocked.');
        }

        // 1. Create blocked record
        Appointment::create([
            'patient_id' => null,
            'patient_name' => 'Doctor Unavailable',
            'patient_phone' => 'N/A',
            'patient_address' => 'N/A',
            'appointment_date' => $request->date,
            'appointment_time' => '08:00',
            'service_type' => 'Blocked',
            'notes' => $request->reason ?? 'Doctor Unavailable',
            'status' => 'blocked',
            'is_walk_in' => false
        ]);

        // 2. Handle existing appointments (Emergency Reschedule)
        $affectedAppointments = Appointment::whereDate('appointment_date', $request->date)
            ->whereIn('status', ['pending', 'approved'])
            ->with('user')
            ->get();

        $count = 0;
        foreach ($affectedAppointments as $appointment) {
            $appointment->update([
                'status' => 'rescheduled',
                'notes' => 'Reschedule required: ' . ($request->reason ?? 'Doctor Unavailable'),
            ]);

            // 3. Send Notification
            $targetEmail = $appointment->user->email ?? null;
            if ($targetEmail) {
                try {
                    Mail::to($targetEmail)->send(new \App\Mail\AppointmentRescheduled($appointment));
                    Log::info('Sent emergency reschedule email', ['id' => $appointment->id, 'email' => $targetEmail]);
                } catch (\Exception $e) {
                    Log::error('Failed to send emergency mail', ['id' => $appointment->id, 'error' => $e->getMessage()]);
                }
            }
            $count++;
        }

        // 4. Create Global Announcement
        \App\Models\Announcement::create([
            'title' => 'Doctor Unavailable: ' . \Carbon\Carbon::parse($request->date)->format('F d, Y'),
            'message' => 'Please note that the doctor is unavailable on ' . \Carbon\Carbon::parse($request->date)->format('F d, Y') . '. ' . ($request->reason ? "Reason: $request->reason." : ''),
            'type' => 'danger',
            'start_date' => now(), // Start showing immediately
            'end_date' => \Carbon\Carbon::parse($request->date)->endOfDay(), // Show until the end of the blocked day
            'is_active' => true
        ]);

        return redirect()->back()->with('success', "Date blocked successfully. $count appointments marked for rescheduling and notified.");
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rescheduled,cancelled,completed,no_show,blocked,in_progress',
            'notes' => 'nullable|string|max:1000',
            'new_date' => 'nullable|date|after_or_equal:today',
            'new_time' => 'nullable'
        ]);

        $oldStatus = $appointment->status;

        $update = [
            'status' => $request->status,
            'notes' => $request->notes,
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now()
        ];
        if ($request->status === 'rescheduled') {
            if ($request->filled('new_date')) {
                // Check for availability on the new date (Limit: 9 per service per day)
                $existingCount = Appointment::whereDate('appointment_date', $request->new_date)
                    ->where('service_type', $appointment->service_type)
                    ->where('status', '!=', 'cancelled')
                    ->count();

                if ($existingCount >= 9) {
                    return redirect()->back()->with('error', 'Cannot reschedule: The selected date is fully booked for ' . $appointment->service_type . '.');
                }

                $update['appointment_date'] = $request->new_date;
            }
            if ($request->filled('new_time')) {
                $update['appointment_time'] = $request->new_time;
            }
        }

        $appointment->update($update);

        // Send approval email only when transitioning to approved
    if ($oldStatus !== 'approved' && $request->status === 'approved') {
        $appointment->loadMissing('user');
        $targetEmail = $appointment->user->email ?? null;
        Log::info('[AppointmentApprovedEmail] Preparing to send', [
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user->id ?? null,
            'target_email' => $targetEmail,
        ]);
        if (!empty($targetEmail)) {
            try {
                Mail::to($targetEmail)->send(new AppointmentApproved($appointment));
                Log::info('[AppointmentApprovedEmail] Sent successfully', [
                    'appointment_id' => $appointment->id,
                    'target_email' => $targetEmail,
                ]);
            } catch (\Throwable $e) {
                Log::error('[AppointmentApprovedEmail] Failed to send', [
                    'appointment_id' => $appointment->id,
                    'target_email' => $targetEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning('[AppointmentApprovedEmail] No recipient email found for appointment approval', [
                'appointment_id' => $appointment->id,
            ]);
        }

        // Auto-cancel conflicting pending appointments (First-In-First-Out)
        $conflictingAppointments = Appointment::where('id', '!=', $appointment->id)
            ->where('appointment_date', $appointment->appointment_date)
            ->where('appointment_time', $appointment->appointment_time)
            ->where('service_type', $appointment->service_type)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        if ($conflictingAppointments->count() > 0) {
            Log::info('[AppointmentConflictResolution] Found conflicting appointments', [
                'approved_appointment_id' => $appointment->id,
                'conflicts_count' => $conflictingAppointments->count(),
            ]);

            foreach ($conflictingAppointments as $conflict) {
                $conflict->update([
                    'status' => 'cancelled',
                    'notes' => 'Automatically cancelled - Time slot was filled by another patient. Please book a new appointment.',
                ]);

                // Send cancellation email
                if ($conflict->user && $conflict->user->email) {
                    try {
                        Mail::to($conflict->user->email)->send(new \App\Mail\AppointmentCancelled($conflict));
                        Log::info('[AppointmentConflictResolution] Cancellation email sent', [
                            'cancelled_appointment_id' => $conflict->id,
                            'patient_email' => $conflict->user->email,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('[AppointmentConflictResolution] Failed to send cancellation email', [
                            'cancelled_appointment_id' => $conflict->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }
    }

    return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    public function inventory(Request $request)
    {
        $query = Inventory::query()
            ->with([
                'transactions',
                'batches' => function($q) {
                    $q->orderBy('expiry_date', 'asc')
                      ->orderBy('received_date', 'asc');
                }
            ])
            ->orderBy('item_name');
        if ($request->filled('category')) {
            $query->whereRaw('LOWER(category) = ?', [strtolower($request->category)]);
        }

        if ($request->filled('search')) {
            $q = trim($request->search);
            $query->where(function ($sub) use ($q) {
                $term = "%" . strtolower($q) . "%";
                $sub->whereRaw('LOWER(item_name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(location) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(unit) LIKE ?', [$term]);

                if (is_numeric($q)) {
                    $sub->orWhere('id', (int) $q);
                }
            });
        }
        // Load all items for global client-side search
        $inventory = $query->get();

        // Stats for header cards and alerts
        $totalItems = Inventory::count();
        $lowStockCount = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = Inventory::where('current_stock', 0)->count();
        $expiringSoonCount = Inventory::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->count();

        // Build category options from existing items
        $defaultCategories = [
            'Medicines',
            'Medical Supplies',
            'Equipment',
            'Vaccines',
            'PPE',
            'Syringes & Needles',
            'Lab Supplies',
            'Test Kits',
            'Disinfectants',
            'Consumables',
            'Dressings',
            'Nutritional Supplements',
            'Oxygen Supplies',
            'Other'
        ];

        $categories = Inventory::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        $categories = array_values(array_unique(array_merge($defaultCategories, $categories)));

        $stats = [
            'total_items' => $totalItems,
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
            'expiring_soon' => $expiringSoonCount,
        ];

        return view('admin.inventory', compact('inventory', 'categories', 'stats'));
    }

    public function addInventory(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255'
        ]);

        // Check for duplicate item name (case-insensitive)
        if (Inventory::whereRaw('LOWER(item_name) = ?', [strtolower($request->item_name)])->exists()) {
            return redirect()->back()->with('error', 'Item with this name already exists.');
        }

        $inventory = Inventory::create($request->all());
        $inventory->updateStatus();

        // Create transaction record
        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'performable_type' => \App\Models\Admin::class,
            'performable_id' => Auth::guard('admin')->id(),
            'transaction_type' => 'restock',
            'quantity' => $request->current_stock,
            'balance_before' => 0,
            'balance_after' => $request->current_stock,
            'notes' => 'Initial stock'
        ]);

        // Create initial batch
        InventoryBatch::create([
            'inventory_id' => $inventory->id,
            'batch_number' => 'BATCH-' . strtoupper(Str::random(8)),
            'quantity' => $request->current_stock,
            'remaining_quantity' => $request->current_stock,
            'previous_stock' => 0,
            'total_stock_after' => $request->current_stock,
            'expiry_date' => $request->expiry_date,
            'received_date' => now(),
            'supplier' => $request->supplier,
        ]);

        return redirect()->back()->with('success', 'Inventory item added successfully.');
    }

    public function updateInventory(Request $request, Inventory $inventory)
    {
        $request->validate([
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'supplier' => 'nullable|string|max:255'
        ]);

        $oldStock = $inventory->current_stock;
        $newStock = $request->current_stock;
        $difference = $newStock - $oldStock;

        $inventory->update($request->all());
        $inventory->updateStatus();

        // Create transaction record if stock changed
        if ($difference != 0) {
            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'performable_type' => \App\Models\Admin::class,
                'performable_id' => Auth::guard('admin')->id(),
                'transaction_type' => $difference > 0 ? 'restock' : 'usage',
                'quantity' => abs($difference),
                'balance_before' => $oldStock,
                'balance_after' => $newStock,
                'notes' => 'Stock adjustment'
            ]);
        }

        return redirect()->back()->with('success', 'Inventory updated successfully.');
    }

    public function restockInventory(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $previousStock = $inventory->current_stock;
        $inventory->current_stock += (int) $request->quantity;
        $newStock = $inventory->current_stock;
        
        $previousExpiry = $inventory->expiry_date;
        
        if ($request->filled('expiry_date')) {
            $inventory->expiry_date = $request->expiry_date;
        }
        $inventory->save();
        $inventory->updateStatus();

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'performable_type' => \App\Models\Admin::class,
            'performable_id' => Auth::guard('admin')->id(),
            'transaction_type' => 'restock',
            'quantity' => (int) $request->quantity,
            'balance_before' => $previousStock,
            'balance_after' => $newStock,
            'previous_expiry_date' => $previousExpiry,
            'notes' => $request->notes,
        ]);

        // Create new batch
        InventoryBatch::create([
            'inventory_id' => $inventory->id,
            'batch_number' => 'BATCH-' . strtoupper(Str::random(8)),
            'quantity' => (int) $request->quantity,
            'remaining_quantity' => (int) $request->quantity,
            'previous_stock' => $previousStock,
            'total_stock_after' => $newStock,
            'expiry_date' => $request->expiry_date ?: $previousExpiry,
            'received_date' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Stock restocked successfully.');
    }

    public function deductInventory(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $quantity = (int) $request->quantity;
        $previousStock = $inventory->current_stock;
        $inventory->current_stock = max(0, $inventory->current_stock - $quantity);
        $newStock = $inventory->current_stock;
        
        $inventory->save();
        $inventory->updateStatus();

        // FIFO Deduction from batches
        $remainingToDeduct = $quantity;
        $batches = $inventory->batches()
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->orderBy('received_date', 'asc')
            ->get();

        foreach ($batches as $batch) {
            if ($remainingToDeduct <= 0) break;

            $deductFromBatch = min($remainingToDeduct, $batch->remaining_quantity);
            $batch->remaining_quantity -= $deductFromBatch;
            $batch->save();
            $remainingToDeduct -= $deductFromBatch;
        }

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'performable_type' => \App\Models\Admin::class,
            'performable_id' => Auth::guard('admin')->id(),
            'transaction_type' => 'usage',
            'quantity' => $quantity,
            'balance_before' => $previousStock,
            'balance_after' => $newStock,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Stock deducted successfully.');
    }

    public function searchPatients(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%");
        })
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone', 'age', 'address']);

        return response()->json($patients);
    }

    public function walkIn(Request $request)
    {
        $query = Appointment::with(['patient', 'approvedByAdmin'])
            ->where('is_walk_in', true);

        // Search functionality
        $searchInput = $request->input('search', $request->input('q'));
        if (filled($searchInput)) {
            $search = trim($searchInput);
            $query->where(function ($sub) use ($search) {
                $sub->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('patient_phone', 'like', "%{$search}%")
                    ->orWhere('service_type', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->filled('service')) {
            $query->where('service_type', $request->service);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('appointment_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('appointment_date', '<=', $request->to);
        }

        // Sort: Today first, then by priority, then by earliest arrival
        $walkIns = $query->orderByDesc('appointment_date')
            ->orderByRaw("CASE 
                WHEN priority = 'emergency' THEN 1 
                WHEN priority = 'priority' THEN 2 
                ELSE 3 END")
            ->orderBy('appointment_time', 'asc')
            ->get();

        // Get services for filter dropdown
        $services = [
            'General Checkup',
            'Prenatal',
            'Medical Check-up',
            'Immunization',
            'Family Planning',
        ];
        if (class_exists(Service::class) && Schema::hasTable('services')) {
            $dbServices = Service::where('active', true)->pluck('name')->toArray();
            if (!empty($dbServices)) {
                $services = array_values(array_unique(array_merge($services, $dbServices)));
            }
        }

        // Stats for today
        $todayWalkIns = Appointment::where('is_walk_in', true)
            ->whereDate('appointment_date', today())
            ->count();

        $todayCompleted = Appointment::where('is_walk_in', true)
            ->whereDate('appointment_date', today())
            ->where('status', 'completed')
            ->count();

        $todayWaiting = Appointment::where('is_walk_in', true)
            ->whereDate('appointment_date', today())
            ->where('status', 'pending')
            ->count();

        $todayInProgress = Appointment::where('is_walk_in', true)
            ->whereDate('appointment_date', today())
            ->where('status', 'in_progress')
            ->count();

        return view('admin.walk-in', compact('walkIns', 'services', 'todayWalkIns', 'todayCompleted', 'todayWaiting', 'todayInProgress'));
    }

    public function addWalkIn(Request $request)
    {
        // If user_id is provided, we're using an existing patient
        if ($request->filled('user_id')) {
            $request->validate([
                'user_id' => 'required|exists:patient,id',
                'service_type' => 'required|string',
                'notes' => 'nullable|string|max:1000'
            ]);

            $patient = Patient::findOrFail($request->user_id);
            
            // Find the service by name
            $service = Service::where('name', $request->service_type)->first();
            if (!$service) {
                return redirect()->back()->with('error', 'Invalid service selected.');
            }

            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'patient_name' => $patient->name,
                'patient_phone' => $patient->phone ?? '',
                'patient_address' => $patient->address ?? 'N/A',
                'appointment_date' => now()->toDateString(),
                'appointment_time' => now()->toTimeString(),
                'service_type' => $request->service_type,
                'notes' => $request->notes,
                'is_walk_in' => true,
                'status' => 'pending',
                'priority' => $request->input('priority', 'regular'), // Default to regular if not specified
                'approved_by_admin_id' => ($assignedAdmin = AppointmentHelper::getLeastBusyAdmin()) ? $assignedAdmin->id : Auth::guard('admin')->id(),
                'approved_at' => now()
            ]);
            
            // Attach service
            $appointment->services()->attach($service->id);

            return redirect()->back()->with('success', 'Walk-in patient added successfully.');
        }

        // Otherwise, create new walk-in by REGISTERING a new patient
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'patient_address' => 'required|string|max:500',
            'service_type' => 'required|string',
            'notes' => 'nullable|string|max:1000',
            // Optional fields for registration if we wanted to ask for them, 
            // but for now we'll generate placeholders to minimize friction
        ]);
        
        // Find the service by name
        $service = Service::where('name', $request->service_type)->first();
        if (!$service) {
            return redirect()->back()->with('error', 'Invalid service selected.');
        }

        // Auto-register the patient
        // Generate a unique placeholder email
        $timestamp = now()->timestamp;
        $random = Str::random(6);
        $placeholderEmail = "walkin_{$timestamp}_{$random}@system.local";
        $placeholderPassword = bcrypt('WalkIn@123'); // Default password for walk-ins

        $patient = Patient::create([
            'name' => $request->patient_name,
            'email' => $placeholderEmail,
            'password' => $placeholderPassword,
            'phone' => $request->patient_phone,
            'address' => $request->patient_address,
            // Required fields with defaults
            'gender' => 'other', // Default to other if not asked
            'barangay' => 'Other', 
            'barangay_other' => 'Walk-In', // Mark as Walk-In location
            'birth_date' => now()->subYears(18)->toDateString(), // Default to 18 years old to avoid age restrictions
            'age' => 18,
            'email_verified_at' => now(),
        ]);

        // Create empty Immunization Record (ITR)
        \App\Models\PatientImmunization::create([
            'patient_id' => $patient->id,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id, // Linked to the newly created patient
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_address' => $request->patient_address,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->toTimeString(),
            'service_type' => $request->service_type,
            'notes' => $request->notes,
            'is_walk_in' => true,
            'status' => 'pending', // Default status for new appointments
            'priority' => $request->input('priority', 'regular'),
            'approved_by_admin_id' => ($assignedAdmin = AppointmentHelper::getLeastBusyAdmin()) ? $assignedAdmin->id : Auth::guard('admin')->id(),
            'approved_at' => now()
        ]);
        
        // Attach service
        $appointment->services()->attach($service->id);

        return redirect()->back()->with('success', 'Walk-in patient registered and added to queue successfully.');
    }

    public function reports()
    {
        $appointmentStats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'approved' => Appointment::where('status', 'approved')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count()
        ];

        $inventoryStats = [
            'total_items' => Inventory::count(),
            'low_stock' => Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock' => Inventory::where('current_stock', 0)->count(),
            'expired' => Inventory::where('expiry_date', '<', now())->count()
        ];

        // Service types data for the doughnut chart
        $serviceTypes = Appointment::selectRaw('service_type, count(*) as count')
            ->groupBy('service_type')
            ->get();

        // Insights Data
        // 1. Service Efficiency Matrix (Analytics)
        $servicePerformance = Appointment::selectRaw("
                service_type, 
                count(*) as total,
                sum(case when status = 'completed' then 1 else 0 end) as completed,
                sum(case when status = 'cancelled' then 1 else 0 end) as cancelled,
                sum(case when status = 'pending' then 1 else 0 end) as pending
            ")
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get();

        // 2. Inventory by Category (Analytics)
        $inventoryByCategory = Inventory::selectRaw('category, count(*) as count, sum(current_stock) as total_stock')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 3. Performance Trends (Weekly, Monthly, Yearly)
        $driver = DB::getDriverName();
        $dayOfWeekSql = $driver === 'pgsql' ? 'EXTRACT(DOW FROM appointment_date) + 1' : 'DAYOFWEEK(appointment_date)';
        $dayOfMonthSql = $driver === 'pgsql' ? 'EXTRACT(DAY FROM appointment_date)' : 'DAY(appointment_date)';
        $monthSql = $driver === 'pgsql' ? 'EXTRACT(MONTH FROM appointment_date)' : 'MONTH(appointment_date)';

        // Helper to initialize status maps
        $initStatusMap = function($labelsCount) {
            return [
                'completed' => array_fill(0, $labelsCount, 0),
                'cancelled' => array_fill(0, $labelsCount, 0),
                'pending'   => array_fill(0, $labelsCount, 0),
            ];
        };

        // --- WEEKLY ---
        $weeklyRaw = Appointment::selectRaw("$dayOfWeekSql as label_key, status, count(*) as count")
            ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['completed', 'cancelled', 'pending'])
            ->groupBy('label_key', 'status')
            ->get();

        $weeklyTrend = $initStatusMap(7);
        foreach ($weeklyRaw as $row) {
            $index = $row->label_key - 1; // 1-7
            if (isset($weeklyTrend[$row->status][$index])) {
                $weeklyTrend[$row->status][$index] = $row->count;
            }
        }

        // --- MONTHLY ---
        $daysInMonth = now()->daysInMonth;
        $monthlyRaw = Appointment::selectRaw("$dayOfMonthSql as label_key, status, count(*) as count")
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->whereIn('status', ['completed', 'cancelled', 'pending'])
            ->groupBy('label_key', 'status')
            ->get();

        $monthlyTrend = $initStatusMap($daysInMonth);
        foreach ($monthlyRaw as $row) {
            $index = $row->label_key - 1; // 1-31
            if (isset($monthlyTrend[$row->status][$index])) {
                $monthlyTrend[$row->status][$index] = $row->count;
            }
        }

        // --- YEARLY ---
        $yearlyRaw = Appointment::selectRaw("$monthSql as label_key, status, count(*) as count")
            ->whereYear('appointment_date', now()->year)
            ->whereIn('status', ['completed', 'cancelled', 'pending'])
            ->groupBy('label_key', 'status')
            ->get();

        $yearlyTrend = $initStatusMap(12);
        foreach ($yearlyRaw as $row) {
            $index = $row->label_key - 1; // 1-12
            if (isset($yearlyTrend[$row->status][$index])) {
                $yearlyTrend[$row->status][$index] = $row->count;
            }
        }

        // Consolidate
        $performanceTrends = [
            'weekly'  => $weeklyTrend,
            'monthly' => $monthlyTrend,
            'yearly'  => $yearlyTrend,
        ];

        return view('admin.reports', compact(
            'appointmentStats', 
            'inventoryStats', 
            'serviceTypes', 
            'servicePerformance',
            'inventoryByCategory',
            'performanceTrends'
        ));
    }

    public function analytics()
    {
        // Reusing the main reports logic for now, or we can create a dedicated analytics view
        return $this->reports();
    }

    public function patientReports(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfMonth();

        $patientRangeQuery = Patient::query()->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('barangay')) {
            $patientRangeQuery->where('barangay', $request->barangay);
            if ($request->barangay === 'Other' && $request->filled('barangay_other')) {
                $patientRangeQuery->where('barangay_other', 'like', '%' . $request->barangay_other . '%');
            }
        }

        // Patient statistics
        $totalPatients = (clone $patientRangeQuery)->count();
        $maleCount = (clone $patientRangeQuery)->where('gender', 'male')->count();
        $femaleCount = (clone $patientRangeQuery)->where('gender', 'female')->count();
        $newPatientsInRange = $totalPatients;

        // Age distribution
        $ageGroups = [
            '0-17' => (clone $patientRangeQuery)->whereBetween('age', [0, 17])->count(),
            '18-30' => (clone $patientRangeQuery)->whereBetween('age', [18, 30])->count(),
            '31-50' => (clone $patientRangeQuery)->whereBetween('age', [31, 50])->count(),
            '51-70' => (clone $patientRangeQuery)->whereBetween('age', [51, 70])->count(),
            '71+' => (clone $patientRangeQuery)->where('age', '>', 70)->count(),
        ];

        // Barangay distribution (Get all barangays context regardless of filter? Or just filtered?)
        // Usually distribution helps see "all". If I filter, I only see one.
        // Let's keep distribution based on DATE only (unfiltered by barangay) so user can see comparison?
        // OR if filter is active, show only that? Usually charts show distribution of the selection.
        // But if I select "Barangay 11", distribution is 100% Barangay 11.
        // Let's create a separate query for distribution if we want "All" context. 
        // But for "Show which patient belongs to specific barangay", the $patients list is key.
        // I will keep $barangayDistribution based on the filtered query for consistency, or unfiltered?
        // Let's use UNFILTERED for the chart so they can see "Oh 12 is minimal compared to 11".
        $barangayDistribution = Patient::query()
             ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // Lists of patients (Filtered)
        $patients = (clone $patientRangeQuery)
            ->orderBy('barangay') // Sort by barangay to group them in the list
            ->orderBy('name')
            ->get();

        // Recent registrations in range
        $recentPatients = Patient::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(10)
            ->get();

        $filterStartDate = $startDate->toDateString();
        $filterEndDate = $endDate->toDateString();
        $selectedBarangay = $request->barangay;

        return view('admin.reports.patients', compact(
            'totalPatients',
            'maleCount',
            'femaleCount',
            'newPatientsInRange',
            'ageGroups',
            'barangayDistribution',
            'patients', // New list
            'recentPatients',
            'filterStartDate',
            'filterEndDate',
            'selectedBarangay'
        ));
    }

    public function inventoryReports(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfMonth();

        $inventoryRangeQuery = Inventory::query()->whereBetween('created_at', [$startDate, $endDate]);

        // Inventory statistics
        $totalItems = (clone $inventoryRangeQuery)->count();
        $lowStockCount = (clone $inventoryRangeQuery)->whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = (clone $inventoryRangeQuery)->where('current_stock', 0)->count();
        $expiringSoonCount = (clone $inventoryRangeQuery)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->count();

        // Category breakdown
        $categoryBreakdown = (clone $inventoryRangeQuery)
            ->selectRaw('category, count(*) as count, sum(current_stock) as total_stock')
            ->groupBy('category')
            ->get();

        // Low stock items
        $lowStockItems = (clone $inventoryRangeQuery)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();

        // Expiring soon items
        $expiringSoonItems = (clone $inventoryRangeQuery)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get();

        // Recent transactions within range
        $recentTransactions = InventoryTransaction::with(['inventory', 'performable'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(10)
            ->get();

        $filterStartDate = $startDate->toDateString();
        $filterEndDate = $endDate->toDateString();

        return view('admin.reports.inventory', compact(
            'totalItems',
            'lowStockCount',
            'outOfStockCount',
            'expiringSoonCount',
            'categoryBreakdown',
            'lowStockItems',
            'expiringSoonItems',
            'recentTransactions',
            'filterStartDate',
            'filterEndDate'
        ));
    }

    public function services()
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function createService()
    {
        return view('admin.services.create');
    }

    public function storeService(Request $request)
    {
        Log::info('storeService called', $request->all());
        $request->validate([
            'name' => 'required|string|max:255|unique:services',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->has('active')
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function editService(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function updateService(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->has('active')
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function deleteService(Service $service)
    {
        // Check if service has appointments
        if ($service->appointments()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete service because it has associated appointments. Deactivate it instead.');
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }

    public function exportAppointmentsExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get all patients
        $patients = Patient::orderBy('name')->get();

        // Get only approved and completed appointments within date range
        $appointments = Appointment::with('patient')
            ->whereBetween('appointment_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('appointment_date')
            ->get();

        // Get all inventory items
        $inventory = Inventory::with('transactions')->orderBy('item_name')->get();

        // Get walk-in patients (appointments marked as walk-in)
        $walkIns = Appointment::with('patient')
            ->where('is_walk_in', true)
            ->whereBetween('appointment_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->orderBy('appointment_date')
            ->get();

        $filename = 'barangay_health_report_' . $request->start_date . '_to_' . $request->end_date . '.xlsx';
        return Excel::download(new AppointmentRangeExport($patients, $appointments, $inventory, $walkIns), $filename);
    }

    public function exportAppointmentsPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get all patients
        $patients = Patient::orderBy('name')->get();

        // Get only approved and completed appointments within date range
        $appointments = Appointment::with('patient')
            ->whereBetween('appointment_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('appointment_date')
            ->get();

        // Get all inventory items
        $inventory = Inventory::with('transactions')->orderBy('item_name')->get();

        // Get walk-in patients (appointments marked as walk-in)
        $walkIns = Appointment::with('patient')
            ->where('is_walk_in', true)
            ->whereBetween('appointment_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->orderBy('appointment_date')
            ->get();

        $html = view('admin.reports.comprehensive-pdf', [
            'startDate' => Carbon::parse($request->start_date),
            'endDate' => Carbon::parse($request->end_date),
            'patients' => $patients,
            'appointments' => $appointments,
            'inventory' => $inventory,
            'walkIns' => $walkIns,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'barangay_health_report_' . $request->start_date . '_to_' . $request->end_date . '.pdf';
        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            $filename,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * Get available slots for a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = $request->date;

        // Debug: Check what appointments exist for this date
        $appointments = \App\Models\Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->get();

        \Log::info("Appointments for date {$date}: " . $appointments->toJson());

        $slots = AppointmentHelper::getAvailableSlots($date);

        \Log::info("Slots data for date {$date}: " . json_encode($slots));

        return response()->json([
            'date' => $date,
            'slots' => $slots,
            'total_slots' => count($slots),
            'available_count' => count(array_filter($slots, fn($slot) => $slot['available'])),
            'occupied_count' => count(array_filter($slots, fn($slot) => !$slot['available'])),
        ]);
    }

    /**
     * Get calendar data for a month
     */
    public function getCalendarData(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = $request->year;
        $month = $request->month;

        $calendarData = AppointmentHelper::getCalendarData($year, $month);

        // Debug: Log calendar data
        \Log::info("Calendar data for {$year}-{$month}: " . json_encode($calendarData));

        return response()->json([
            'year' => $year,
            'month' => $month,
            'calendar' => $calendarData,
        ]);
    }

    // Patient Reports Export Methods
    public function exportPatientsExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        return Excel::download(new \App\Exports\PatientReportExport($startDate, $endDate), 'patient_reports_' . $request->start_date . '_to_' . $request->end_date . '.xlsx');
    }

    public function exportPatientsPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // 1. Summary Stats
        $totalPatients = Patient::count();
        $maleCount = Patient::where('gender', 'male')->count();
        $femaleCount = Patient::where('gender', 'female')->count();
        $newPatients = Patient::whereBetween('created_at', [$startDate, $endDate])->count();

        // 2. Distributions
        $ageGroups = [
            '0-17' => Patient::whereBetween('age', [0, 17])->count(),
            '18-30' => Patient::whereBetween('age', [18, 30])->count(),
            '31-50' => Patient::whereBetween('age', [31, 50])->count(),
            '51-70' => Patient::whereBetween('age', [51, 70])->count(),
            '71+' => Patient::where('age', '>', 70)->count(),
        ];

        $barangayDistribution = Patient::selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // 3. Top Patients (Filtered by Date Range)
        $topPatients = Patient::withCount(['appointments' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('appointment_date', [$startDate, $endDate]);
            }])
            ->whereHas('appointments', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('appointment_date', [$startDate, $endDate]);
            })
            ->orderByDesc('appointments_count')
            ->limit(20)
            ->get();

        // 4. Recent Registrations (Filtered by Date Range)
        $recentPatients = Patient::whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $html = view('admin.reports.patients-pdf', compact(
            'startDate', 'endDate',
            'totalPatients', 'maleCount', 'femaleCount', 'newPatients',
            'ageGroups', 'barangayDistribution',
            'topPatients', 'recentPatients'
        ))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        $filename = 'patient_reports_' . $request->start_date . '_to_' . $request->end_date . '.pdf';
        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    // Inventory Reports Export Methods
    public function exportInventoryExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $inventory = Inventory::orderBy('item_name', 'asc')->get();
        
        // Get all transactions within the date range
        $transactions = InventoryTransaction::with(['inventory', 'performable'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Create multi-sheet export
        $export = new class ($inventory, $transactions, $startDate, $endDate) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            protected $inventory;
            protected $transactions;
            protected $startDate;
            protected $endDate;

            public function __construct($inventory, $transactions, $startDate, $endDate)
            {
                $this->inventory = $inventory;
                $this->transactions = $transactions;
                $this->startDate = $startDate;
                $this->endDate = $endDate;
            }

            public function sheets(): array
            {
                return [
                    new InventorySummarySheet($this->inventory, $this->startDate, $this->endDate),
                    new TransactionHistorySheet($this->transactions),
                ];
            }
        };

        return Excel::download($export, 'inventory_reports_' . $request->start_date . '_to_' . $request->end_date . '.xlsx');
    }
}

// Inventory Summary Sheet
class InventorySummarySheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
    protected $inventory;
    protected $startDate;
    protected $endDate;

    public function __construct($inventory, $startDate, $endDate)
    {
        $this->inventory = $inventory;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->inventory->map(function ($item) {
            // Calculate Stocks Used (Usage + Deduct transactions)
            $stocksUsed = \App\Models\InventoryTransaction::where('inventory_id', $item->id)
                ->whereIn('transaction_type', ['usage', 'deduct', 'dispense'])
                ->where(function($q) {
                     $q->where('transaction_type', '!=', 'restock');
                })
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->sum('quantity');

            return [
                'Item Name' => $item->item_name,
                'Category' => $item->category,
                'Current Stock' => $item->current_stock . ' ' . $item->unit,
                'Stocks Used' => $stocksUsed . ' ' . $item->unit,
                'Status' => str_replace('_', ' ', ucfirst($item->status)),
                'Expiry Date' => $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return ['Item Name', 'Category', 'Current Stock', 'Stocks Used', 'Status', 'Expiry Date'];
    }

    public function title(): string
    {
        return 'Inventory Summary';
    }
}

// Transaction History Sheet
class TransactionHistorySheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions->map(function ($transaction) {
            return [
                'Date' => $transaction->created_at->format('M d, Y h:i A'),
                'Item Name' => $transaction->inventory->item_name ?? 'N/A',
                'Type' => ucfirst($transaction->transaction_type),
                'Quantity' => $transaction->quantity,
                'User' => $transaction->performable ? $transaction->performable->name : 'System',
                'Notes' => $transaction->notes ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Date', 'Item Name', 'Type', 'Quantity', 'User', 'Notes'];
    }

    public function title(): string
    {
        return 'Transaction History';
    }

    public function exportInventoryPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $inventory = Inventory::with('transactions')->orderBy('item_name')->get();

        $html = view('admin.reports.inventory-pdf', compact('inventory', 'startDate', 'endDate'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'inventory_reports_' . $request->start_date . '_to_' . $request->end_date . '.pdf';
        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * View a patient's medical profile
     */
    public function viewPatientMedicalProfile(Patient $patient)
    {
        // Load immunization relationship
        $patient->load('immunization');

        return view('admin.patient-medical-profile', compact('patient'));
    }

    /**
     * Update a patient's medical profile
     */
    public function updatePatientMedicalProfile(Request $request, Patient $patient)
    {
        if ($patient->age < 6) {
            return redirect()->back()->with('error', 'Medical profile is only available for patients 6 years and older.');
        }

        $request->validate([
            'mother_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|in:single,married,widowed,separated,co-habitation',
            'educational_attainment' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'accompanying_person' => 'nullable|string|max:255',
            'accompanying_relationship' => 'nullable|string|max:255',
        ]);

        // Update Patient Record
        $patient->update([
            'mother_name' => $request->mother_name,
            'father_name' => $request->father_name,
            'religion' => $request->religion,
            'marital_status' => $request->marital_status,
            'educational_attainment' => $request->educational_attainment,
            'occupation' => $request->occupation,
            'accompanying_person' => $request->accompanying_person,
            'accompanying_relationship' => $request->accompanying_relationship,
            'spouse_name' => $request->spouse_name,
            'spouse_age' => $request->spouse_age,
            'spouse_occupation' => $request->spouse_occupation,
            'maiden_name' => $request->maiden_name,
            'smoker' => $request->has('smoker'),
            'smoker_packs_per_year' => $request->smoker_packs_per_year,
            'drinks_alcohol' => $request->has('drinks_alcohol'),
            'alcohol_specify' => $request->alcohol_specify,
            'illicit_drug_use' => $request->has('illicit_drug_use'),
            'multiple_sexual_partners' => $request->has('multiple_sexual_partners'),
            'is_pwd' => $request->has('is_pwd'),
            'pwd_specify' => $request->pwd_specify,
            'has_sti' => $request->has('has_sti'),
            'has_allergies' => $request->has('has_allergies'),
            'allergies_specify' => $request->allergies_specify,
            'social_history_others' => $request->social_history_others,
            'family_hypertension' => $request->has('family_hypertension'),
            'family_diabetes' => $request->has('family_diabetes'),
            'family_goiter' => $request->has('family_goiter'),
            'family_cancer' => $request->has('family_cancer'),
            'family_history_others' => $request->family_history_others,
            'history_uti' => $request->has('history_uti'),
            'history_hypertension' => $request->has('history_hypertension'),
            'history_diabetes' => $request->has('history_diabetes'),
            'history_goiter' => $request->has('history_goiter'),
            'history_cancer' => $request->has('history_cancer'),
            'history_tuberculosis' => $request->has('history_tuberculosis'),
            'medical_history_others' => $request->medical_history_others,
            'previous_surgeries' => $request->previous_surgeries,
            'maintenance_medicine' => $request->maintenance_medicine,
        ]);

        // Update Immunization Record
        $immunizationData = [
            'bcg' => $request->has('imm_bcg'),
            'dpt1' => $request->has('imm_dpt1'),
            'dpt2' => $request->has('imm_dpt2'),
            'dpt3' => $request->has('imm_dpt3'),
            'opv1' => $request->has('imm_opv1'),
            'opv2' => $request->has('imm_opv2'),
            'opv3' => $request->has('imm_opv3'),
            'measles' => $request->has('imm_measles'),
            'hepatitis_b1' => $request->has('imm_hepatitis_b1'),
            'hepatitis_b2' => $request->has('imm_hepatitis_b2'),
            'hepatitis_b3' => $request->has('imm_hepatitis_b3'),
            'hepatitis_a' => $request->has('imm_hepatitis_a'),
            'varicella' => $request->has('imm_varicella'),
            'hpv' => $request->has('imm_hpv'),
            'pneumococcal' => $request->has('imm_pneumococcal'),
            'mmr' => $request->has('imm_mmr'),
            'flu_vaccine' => $request->has('imm_flu_vaccine'),
            'none' => $request->has('imm_none'),
            'covid_vaccine_name' => $request->covid_vaccine_name,
            'covid_first_dose' => $request->covid_first_dose,
            'covid_second_dose' => $request->covid_second_dose,
            'covid_booster1' => $request->covid_booster1,
            'covid_booster2' => $request->covid_booster2,
        ];

        $patient->immunization()->updateOrCreate(
            ['patient_id' => $patient->id],
            $immunizationData
        );

        return redirect()->back()->with('success', 'Patient medical profile updated successfully.');
    }
}
