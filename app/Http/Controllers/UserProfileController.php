<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'profile_photo' => $user->profile_photo_url,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'debug' => 'Controller works!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'current_password' => 'required|string',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai'
            ], 422);
        }

        try {
            // Update basic info
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                
                // Delete old photo if exists
                if ($user->profile_photo) {
                    $oldPhotoPath = $user->profile_photo;
                    if (str_starts_with($oldPhotoPath, '/storage/')) {
                        $oldPhotoPath = substr($oldPhotoPath, strlen('/storage/'));
                    }
                    if (str_contains($oldPhotoPath, Storage::url(''))) {
                        $oldPhotoPath = str_replace(Storage::url(''), '', $oldPhotoPath);
                    }
                    Storage::disk('public')->delete($oldPhotoPath);
                }

                // Store new photo
                $photoPath = $photo->store('profile-photos', 'public');
                $user->profile_photo = $photoPath;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diperbarui',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'profile_photo' => $user->profile_photo_url,
                    'role' => $user->role,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
