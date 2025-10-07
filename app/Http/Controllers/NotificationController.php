<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Notifications\NotificationManager;
use App\Jobs\SendNotificationJob;
use App\Jobs\SendBulkNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    private NotificationManager $notificationManager;

    public function __construct(NotificationManager $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    public function sendManual(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'message' => 'required|string',
                'channel' => 'required|in:email,sms',
            ]);

            $user = User::findOrFail($request->user_id);
            Log::info('User found successfully: ' . $user->id);

            // $result = $this->notificationManager->send(
            //     $request->channel,
            //     $user,
            //     $request->message
            // );

            // return response()->json([
            //     'success' => $result,
            //     'message' => $result ? 'Notification sent successfully' : 'Notification failed'
            // ]);

            // Dispatch job to RabbitMQ
            SendNotificationJob::dispatch(
                $request->user_id,
                $request->message,
                $request->channel
            );
            Log::info("🐰 Notification queued in RabbitMQ for user {$request->user_id}");

            return response()->json([
                'success' => true,
                'message' => '🐰 Notification queued successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in sendManual: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    // public function sendBulk(Request $request)
    // {
    //     try {
    //         Log::info('sendBulk method called');
    //         Log::info('Request data: ', $request->all());

    //         $request->validate([
    //             'position' => 'required|string',
    //             'message' => 'required|string',
    //             'channels' => 'required|array',
    //             'channels.*' => 'in:email,sms',
    //         ]);

    //         Log::info('Validation passed');

    //         $users = User::where('position', $request->position)->get();
            
    //         Log::info('Users found: ' . $users->count());

    //         if ($users->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => "No users found with position: {$request->position}",
    //                 'total_users' => 0
    //             ], 404);
    //         }

    //         $results = [];
    //         foreach ($users as $user) {
    //             try {
    //                 Log::info("Processing user: {$user->id} - {$user->email}");
                    
    //                 $results[$user->id] = $this->notificationManager->sendViaMultipleChannels(
    //                     $request->channels,
    //                     $user,
    //                     $request->message
    //                 );
                    
    //                 Log::info("User {$user->id} processed successfully");
    //             } catch (\Exception $e) {
    //                 Log::error("Error processing user {$user->id}: " . $e->getMessage());
    //                 $results[$user->id] = ['error' => $e->getMessage()];
    //             }
    //         }

    //         Log::info('sendBulk completed successfully');

    //         return response()->json([
    //             'success' => true,
    //             'results' => $results,
    //             'total_users' => $users->count(),
    //             'message' => 'Bulk notifications sent successfully'
    //         ]);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         Log::error('Validation error in sendBulk');
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         Log::error('Error in sendBulk: ' . $e->getMessage());
    //         Log::error('Stack trace: ' . $e->getTraceAsString());
            
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred',
    //             'error' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => basename($e->getFile())
    //         ], 500);
    //     }
    // }

    public function sendBulk(Request $request)
    {
        try {
            Log::info('sendBulk method called');
            Log::info('Request data: ', $request->all());

            $request->validate([
                'position' => 'required|string',
                'message' => 'required|string',
                'channels' => 'required|array',
                'channels.*' => 'in:email,sms',
            ]);

            Log::info('Validation passed');

            // Check if users exist first (without loading all data)
            $userCount = User::where('position', $request->position)->count();
            
            Log::info('Users found: ' . $userCount);

            if ($userCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No users found with position: {$request->position}",
                    'total_users' => 0
                ], 404);
            }

            Log::info('Dispatching bulk notification job to queue');

            // Dispatch the bulk job to RabbitMQ
            SendBulkNotificationJob::dispatch(
                $request->position,
                $request->message,
                $request->channels
            );
            // ->onQueue('notifications')

            Log::info('Bulk notification job dispatched successfully');

            return response()->json([
                'success' => true,
                'message' => 'Bulk notifications are being processed in the background',
                'total_users' => $userCount,
                'queued' => true,
                'job_dispatched' => true
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in sendBulk');
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in sendBulk: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }
}