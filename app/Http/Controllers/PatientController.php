<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Helpers\AppointmentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('patient')->user();
        $appointments = $user->appointments()->latest()->paginate(10);

        return view('patient.dashboard', compact('appointments'));
    }

    public function appointments()
    {
        $user = Auth::guard('patient')->user();
        $appointments = $user->appointments()->latest()->paginate(20);

        return view('patient.appointments', compact('appointments'));
    }

    public function bookAppointment()
    {
        $user = Auth::guard('patient')->user();
        $services = Service::whereRaw('active = TRUE')->get();
        return view('patient.book-appointment', compact('user', 'services'));
    }

    public function storeAppointment(Request $request)
    {
        $patient = Auth::guard('patient')->user();

        $rules = [
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000'
        ];

        // REMOVED ITR Validation as per request to remove requirement

        $request->validate($rules);

        $service = Service::find($request->service_id);
        
        // Check if the patient already has an appointment for the same service on the same day
        $pastAppointments = Appointment::where('patient_id', $patient->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('appointment_date', $request->appointment_date)
            ->where('service_type', $service->name)
            ->count();

        if ($pastAppointments > 0) {
            return redirect()->back()->with('error', 'You already have an active appointment for this service on the selected date. Please choose another date or service.');
        }

        // Validate Slot Availability/Quota
        $slots = AppointmentHelper::getAvailableSlots($request->appointment_date, $service->name);
        $normalizedTime = substr($request->appointment_time, 0, 5); // Ensure H:i format
        
        $selectedSlot = collect($slots)->firstWhere('time', $normalizedTime);
        
        if (!$selectedSlot || !$selectedSlot['available']) {
             return redirect()->back()->with('error', 'Selected time slot is full or no longer available for this service type.');
        }

        $assignedAdmin = AppointmentHelper::getLeastBusyAdmin();

        $appointment = Appointment::create([
            'patient_id' => Auth::guard('patient')->id(),
            'approved_by_admin_id' => $assignedAdmin ? $assignedAdmin->id : null,
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_address' => $patient->address ?? 'N/A',
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_type' => $service->name, // Keep for legacy/display compatibility
            'notes' => $request->notes,
            'medical_history' => $request->medical_history,
            'status' => 'pending'
        ]);

        // Attach service to appointment
        \Log::info('Attaching service to appointment', [
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'service_name' => $service->name
        ]);
        
        $appointment->services()->attach($service->id);
        
        // REMOVED Forced ITR Update Block as per request

        return redirect()->route('patient.dashboard')->with('success', 'Appointment booked successfully! We will contact you soon for confirmation.');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        if ($appointment->patient_id !== Auth::guard('patient')->id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if ($appointment->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot cancel completed appointment.');
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Appointment cancelled successfully.');
    }

    public function showAppointment(Appointment $appointment)
    {
        if ($appointment->patient_id !== Auth::guard('patient')->id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('patient.appointment-details', compact('appointment'));
    }

    /**
     * Get available slots for a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'service_type' => 'nullable|string', // Optional but recommended
        ]);

        $date = $request->date;
        $serviceType = $request->input('service_type');
        
        $slots = AppointmentHelper::getAvailableSlots($date, $serviceType);

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

        return response()->json([
            'year' => $year,
            'month' => $month,
            'calendar' => $calendarData,
        ]);
    }

    /**
     * Show the patient's medical profile.
     */
    public function medicalProfile()
    {
        $user = Auth::guard('patient')->user();
        return view('patient.medical-profile', compact('user'));
    }

    /**
     * Update the patient's medical profile.
     */
    public function updateMedicalProfile(Request $request)
    {
        $patient = Auth::guard('patient')->user();

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
            'consent_signed' => 'required|accepted',
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
            'consent_signed' => true,
            'consent_signed_at' => $patient->consent_signed_at ?? now(),
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

        return redirect()->back()->with('success', 'Medical profile updated successfully.');
    }
}
