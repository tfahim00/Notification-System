<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class CheckUser extends Controller
{
    /**
     * Check if user exists by ID
     */
    public function checkUser($id): JsonResponse
    {
        try {
            $exists = User::where('id', $id)->exists();

            if ($exists) {
                return response()->json([
                    'exists' => true,
                    'message' => 'User found'
                ], 200);
            }

            return response()->json([
                'exists' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch all users
     */
    public function getAllUsers(): JsonResponse
    {
        \Log::info('getAllUsers method called');
        
        try {
            \Log::info('Attempting to fetch users');
            $users = User::all();
            \Log::info('Users fetched successfully', ['count' => $users->count()]);

            return response()->json([
                'success' => true,
                'count' => $users->count(),
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in getAllUsers', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}