<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::post('/notifications/send', [NotificationController::class, 'sendManual']);
Route::post('/notifications/send-bulk', [NotificationController::class, 'sendBulk']);

Route::get('/test-bulk-setup', function() {
    try {
        // Check if users exist
        $users = \App\Models\User::all();
        
        // Check if NotificationManager is working
        $manager = app(\App\Services\Notifications\NotificationManager::class);
        
        return response()->json([
            'users_count' => $users->count(),
            'users' => $users,
            'manager_loaded' => $manager ? 'Yes' : 'No',
            'available_positions' => $users->pluck('position')->unique()->values()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::post('/test-send-bulk-debug', function(\Illuminate\Http\Request $request) {
    try {
        Log::info('Test bulk request received', $request->all());
        
        // Step 1: Validate
        $validated = $request->validate([
            'position' => 'required|string',
            'message' => 'required|string',
            'channels' => 'required|array',
            'channels.*' => 'in:email,sms',
        ]);
        
        Log::info('Validation passed', $validated);
        
        // Step 2: Get users
        $users = \App\Models\User::where('position', $validated['position'])->get();
        Log::info('Users found: ' . $users->count());
        
        if ($users->isEmpty()) {
            return response()->json([
                'error' => 'No users found',
                'position' => $validated['position']
            ], 404);
        }
        
        // Step 3: Get notification manager
        $manager = app(\App\Services\Notifications\NotificationManager::class);
        Log::info('Manager loaded');
        
        // Step 4: Test sending to first user only
        $firstUser = $users->first();
        Log::info('Testing with first user: ' . $firstUser->email);
        
        $result = $manager->sendViaMultipleChannels(
            $validated['channels'],
            $firstUser,
            $validated['message']
        );
        
        Log::info('Send result', ['result' => $result]);
        
        return response()->json([
            'success' => true,
            'test_user' => $firstUser,
            'result' => $result,
            'total_users_found' => $users->count()
        ]);
        
    } catch (\Exception $e) {
        Log::error('Test bulk error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});