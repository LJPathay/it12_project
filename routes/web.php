<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

// Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

// Booking Policy Page
Route::get('/policy', fn() => view('policy'))->name('policy');

// Contact Us Page
Route::get('/contact', fn() => view('contact'))->name('contact');

// Services Page (static)
Route::get('/services', fn() => view('partials.services'))->name('services');

// How It Works Page
Route::get('/how-it-works', fn() => view('partials.how-it-works'))->name('how-it-works');

// UI Style Guide (Dev only)
Route::get('/style-guide', fn() => view('style-guide'))->name('style-guide');

// Booking Page
Route::get('/booking', function () {
    if (!Auth::check()) {
        // User not logged in — show the home page with login modal open
        return redirect('/')->with('showLoginModal', true);
    }
    return view('partials.book-appointment');
})->name('booking');

// Authentication Routes
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendOtp'])
    ->middleware('throttle:3,1') // 3 attempts per minute
    ->name('password.email');
Route::get('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showOtpForm'])->name('password.otp');
Route::post('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])
    ->middleware('throttle:5,1') // 5 attempts per minute for OTP verification
    ->name('password.verify');
Route::get('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])
    ->middleware('throttle:3,1') // 3 attempts per minute
    ->name('password.update');

// Profile Routes
Route::middleware(['auth.multi'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/check-email', [App\Http\Controllers\AuthController::class, 'checkEmailAvailability']);

// Patient Routes
Route::middleware(['patient'])->group(function () {
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [PatientController::class, 'appointments'])->name('appointments');
        Route::get('/book-appointment', [PatientController::class, 'bookAppointment'])->name('book-appointment');
        Route::post('/book-appointment', [PatientController::class, 'storeAppointment'])->name('store-appointment');
        Route::get('/appointments/slots', [PatientController::class, 'getAvailableSlots'])->name('appointments.slots');
        Route::get('/appointments/calendar', [PatientController::class, 'getCalendarData'])->name('appointments.calendar');
        Route::get('/appointment/{appointment}', [PatientController::class, 'showAppointment'])->name('appointment.show');
        Route::post('/appointment/{appointment}/cancel', [PatientController::class, 'cancelAppointment'])->name('appointment.cancel');
        Route::put('/appointment/{appointment}/cancel', [PatientController::class, 'cancelAppointment'])->name('cancel-appointment');
    });
});

// Admin Routes
Route::middleware(['admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
        Route::post('/patient/create', [AdminController::class, 'createPatient'])->name('patient.create');
        Route::put('/patient/{patient}/update', [AdminController::class, 'updatePatient'])->name('patient.update');
        Route::post('/patient/{patient}/archive', [AdminController::class, 'archivePatient'])->name('patient.archive');
        Route::get('/patients/archive', [AdminController::class, 'archivedPatients'])->name('patients.archive');
        Route::post('/patient/{id}/restore', [AdminController::class, 'restorePatient'])->name('patient.restore');
        Route::delete('/patient/{id}/force-delete', [AdminController::class, 'forceDeletePatient'])->name('patient.force-delete');
        Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
        Route::post('/appointment/create', [AdminController::class, 'createAppointment'])->name('appointment.create');
        Route::post('/appointment/block', [AdminController::class, 'blockDate'])->name('appointment.block');
        Route::post('/appointment/{appointment}/update', [AdminController::class, 'updateAppointmentStatus'])->name('appointment.update');
        Route::get('/appointments/slots', [AdminController::class, 'getAvailableSlots'])->name('appointments.slots');
        Route::get('/appointments/calendar', [AdminController::class, 'getCalendarData'])->name('appointments.calendar');
        Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
        Route::post('/inventory/add', [AdminController::class, 'addInventory'])->name('inventory.add');
        Route::post('/inventory/{inventory}/update', [AdminController::class, 'updateInventory'])->name('inventory.update');
        Route::post('/inventory/{inventory}/restock', [AdminController::class, 'restockInventory'])->name('inventory.restock');
        Route::post('/inventory/{inventory}/deduct', [AdminController::class, 'deductInventory'])->name('inventory.deduct');
        Route::get('/patients/search', [AdminController::class, 'searchPatients'])->name('patients.search');
        Route::get('/walk-in', [AdminController::class, 'walkIn'])->name('walk-in');
        Route::post('/walk-in', [AdminController::class, 'addWalkIn'])->name('walk-in.store');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/analytics', [AdminController::class, 'analytics'])->name('reports.analytics');
        Route::get('/reports/patients', [AdminController::class, 'patientReports'])->name('reports.patients');
        Route::get('/reports/inventory', [AdminController::class, 'inventoryReports'])->name('reports.inventory');
        Route::get('/reports/export/appointments', [AdminController::class, 'exportAppointmentsExcel'])->name('reports.export.appointments');
        Route::get('/reports/export/appointments/pdf', [AdminController::class, 'exportAppointmentsPdf'])->name('reports.export.appointments.pdf');
        Route::get('/reports/export/patients', [AdminController::class, 'exportPatientsExcel'])->name('reports.export.patients');
        Route::get('/reports/export/patients/pdf', [AdminController::class, 'exportPatientsPdf'])->name('reports.export.patients.pdf');
        Route::get('/reports/export/inventory', [AdminController::class, 'exportInventoryExcel'])->name('reports.export.inventory');
        Route::get('/reports/export/inventory/pdf', [AdminController::class, 'exportInventoryPdf'])->name('reports.export.inventory.pdf');


        // Services Management
        Route::get('/services', [AdminController::class, 'services'])->name('services.index');
        Route::get('/services/create', [AdminController::class, 'createService'])->name('services.create');
        Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
        Route::get('/services/{service}/edit', [AdminController::class, 'editService'])->name('services.edit');
        Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('services.update');
        Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('services.destroy');

        // Patient Medical Profile
        Route::get('/patient/{patient}/medical-profile', [AdminController::class, 'viewPatientMedicalProfile'])->name('patient.medical-profile');
        Route::post('/patient/{patient}/medical-profile', [AdminController::class, 'updatePatientMedicalProfile'])->name('patient.medical-profile.update');
    });
});

// Super Admin Routes
Route::middleware(['super_admin'])->group(function () {
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::get('/users/archive', [SuperAdminController::class, 'archivedUsers'])->name('users.archive');
        Route::post('/user/create', [SuperAdminController::class, 'createUser'])->name('user.create');
        Route::post('/user/{type}/{id}/update', [SuperAdminController::class, 'updateUser'])->name('user.update');
        Route::post('/user/{type}/{id}/delete', [SuperAdminController::class, 'deleteUser'])->name('user.delete');
        Route::post('/user/{type}/{id}/restore', [SuperAdminController::class, 'restoreUser'])->name('user.restore');
        Route::delete('/user/{type}/{id}/force-delete', [SuperAdminController::class, 'forceDeleteUser'])->name('user.force-delete');
        Route::get('/system-logs', [SuperAdminController::class, 'systemLogs'])->name('system-logs');
        Route::get('/audit-trail', [SuperAdminController::class, 'auditTrail'])->name('audit-trail');
        Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
        Route::get('/backup', [SuperAdminController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [SuperAdminController::class, 'createBackup'])->name('backup.create');
        Route::post('/backup/schedule', [SuperAdminController::class, 'scheduleBackup'])->name('backup.schedule');
        Route::get('/backup/{backup}/download', [SuperAdminController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{backup}/delete', [SuperAdminController::class, 'deleteBackup'])->name('backup.delete');
    });
});

