<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Admin;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate Limiting: 5 attempts per minute
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Additional input sanitization
        $credentials = $request->only('email', 'password');
        $email = strip_tags(trim($request->input('email')));

        // Try to find user in all three tables
        $patient = Patient::where('email', $email)->first();
        $admin = Admin::where('email', $email)->first();
        $superAdmin = SuperAdmin::where('email', $email)->first();

        // Determine which guard to use
        $guard = null;
        $redirectUrl = null;
        $welcomeMessage = null;

        if ($patient) {
            $guard = 'patient';
            $redirectUrl = route('patient.dashboard');
        } elseif ($admin) {
            $guard = 'admin';
            $redirectUrl = route('admin.dashboard');
            $welcomeMessage = 'Welcome back, ' . $admin->name . '! You have successfully logged in.';
        } elseif ($superAdmin) {
            $guard = 'super_admin';
            $redirectUrl = route('superadmin.dashboard');
            $welcomeMessage = 'Welcome back, ' . $superAdmin->name . '! You have successfully logged in.';
        }

        // Generic error message to prevent User Enumeration
        $genericError = ['email' => 'Invalid login credentials.'];

        // If no user found in any table
        if (!$guard) {
            RateLimiter::hit($throttleKey);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['errors' => $genericError], 422);
            }

            return back()->withErrors($genericError)->withInput($request->only('email'));
        }

        // Attempt authentication with the appropriate guard
        if (Auth::guard($guard)->attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            if ($welcomeMessage) {
                session()->flash('announcement', $welcomeMessage);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['redirect' => $redirectUrl]);
            }

            return redirect()->to($redirectUrl);
        }

        // Authentication failed
        RateLimiter::hit($throttleKey);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['errors' => $genericError], 422);
        }

        return back()->withErrors($genericError)->withInput($request->only('email'));
    }

    /**
     * Check if email is available for registration.
     */
    public function checkEmailAvailability(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = Patient::where('email', $request->email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This email is already registered.' : 'Email is available.'
        ]);
    }

    /**
     * Handle user registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|email|unique:patient,email',
            'gender' => 'required|in:male,female,other',
            'phone' => [
                'required',
                'digits:11',
            ],
            'address' => 'required|string|max:500',
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
            'phone.required' => 'Phone number is required.',
            'phone.digits' => 'Phone number must be exactly 11 digits (e.g. 09123456789).',
            'address.required' => 'Address is required.',
            'gender.required' => 'Please select a gender.',
            'barangay.in' => 'Please select Barangay 11, Barangay 12, or choose Other.',
            'barangay_other.required' => 'Please specify your barangay.',
            'purok.required' => 'Please select a purok for the chosen barangay.',
            'purok.in' => 'Please choose a valid purok option.',
            'birth_date.before' => 'Birth date must be in the past.',
        ]);

        $age = Carbon::parse($validated['birth_date'])->age;

        $patient = Patient::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'barangay' => $validated['barangay'],
            'barangay_other' => $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null,
            'purok' => $validated['barangay'] === 'Other' ? null : ($validated['purok'] ?? null),
            'birth_date' => $validated['birth_date'],
            'age' => $age,
            'password' => bcrypt($validated['password']),
        ]);

        Auth::guard('patient')->login($patient);
        return redirect()->route('patient.dashboard');
    }

    /**
     * Handle user logout request.
     */
    public function logout(Request $request)
    {
        // Logout from all guards
        Auth::guard('patient')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('super_admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
