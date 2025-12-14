<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\AuthHelper;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => AuthHelper::user()]);
    }

    public function update(Request $request)
    {
        $user = AuthHelper::user();

        // Determine correct table based on user type
        $table = match (true) {
            $user->isPatient() => 'patient',
            $user->isAdmin() => 'admin',
            $user->isSuperAdmin() => 'super_admin',
            default => 'patient',
        };

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:{$table},email,{$user->id}",
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => [
                'nullable',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{}|,.<>\/?]).+$/',
            ],
        ], [
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($user instanceof \App\Models\Patient) {
            if ($request->filled('birth_date')) {
                $user->birth_date = $request->birth_date;
            }
            $user->address = $request->address;
        }

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
