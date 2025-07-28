<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'message' => 'User profile retrieved successfully',
                'user' => $user->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            // Basic validation
            $validationErrors = [];
            
            if ($request->has('name') && empty(trim($request->name))) {
                $validationErrors['name'] = 'Name is required';
            }
            
            if ($request->has('email')) {
                if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                    $validationErrors['email'] = 'Invalid email format';
                } elseif (User::where('email', $request->email)->where('id', '!=', $user->id)->exists()) {
                    $validationErrors['email'] = 'Email already exists';
                }
            }
            
            if ($request->has('phone') && !empty($request->phone)) {
                if (!preg_match('/^[\d\s\-\+\(\)]+$/', $request->phone)) {
                    $validationErrors['phone'] = 'Invalid phone format';
                }
            }

            if (!empty($validationErrors)) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validationErrors,
                ], 422);
            }

            // Update user data
            $updateData = [];
            
            if ($request->has('name')) {
                $updateData['name'] = trim($request->name);
            }
            
            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }
            
            if ($request->has('phone')) {
                $updateData['phone'] = $request->phone;
            }
            
            if ($request->has('bio')) {
                $updateData['bio'] = $request->bio;
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user->fresh()->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            // Basic validation
            $validationErrors = [];
            
            if (!$request->has('current_password')) {
                $validationErrors['current_password'] = 'Current password is required';
            }
            
            if (!$request->has('new_password')) {
                $validationErrors['new_password'] = 'New password is required';
            } elseif (strlen($request->new_password) < 8) {
                $validationErrors['new_password'] = 'New password must be at least 8 characters';
            }
            
            if (!$request->has('new_password_confirmation')) {
                $validationErrors['new_password_confirmation'] = 'Password confirmation is required';
            } elseif ($request->new_password !== $request->new_password_confirmation) {
                $validationErrors['new_password_confirmation'] = 'Password confirmation does not match';
            }

            if (!empty($validationErrors)) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validationErrors,
                ], 422);
            }

            $user = $request->user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                ], 401);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => 'Password changed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to change password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        try {
            // Basic validation
            if (!$request->hasFile('avatar')) {
                return response()->json([
                    'message' => 'Avatar file is required',
                ], 422);
            }

            $file = $request->file('avatar');
            
            // Check file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return response()->json([
                    'message' => 'Invalid file type. Only JPEG, PNG, JPG, GIF allowed',
                ], 422);
            }

            // Check file size (2MB max)
            if ($file->getSize() > 2 * 1024 * 1024) {
                return response()->json([
                    'message' => 'File too large. Maximum size is 2MB',
                ], 422);
            }

            $user = $request->user();

            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Generate unique filename
            $filename = 'avatars/' . $user->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $path = $file->storeAs('', $filename, 'public');

            // Update user avatar
            $user->update([
                'avatar' => $path
            ]);

            return response()->json([
                'message' => 'Avatar uploaded successfully',
                'avatar_url' => Storage::url($path),
                'avatar_path' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload avatar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->avatar) {
                // Delete file from storage
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Update user record
                $user->update([
                    'avatar' => null
                ]);
            }

            return response()->json([
                'message' => 'Avatar deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete avatar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}