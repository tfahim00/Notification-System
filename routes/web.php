<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CheckUser;
use Illuminate\Support\Facades\Mail;


Route::get('/', function () {
    return view('welcome');
});

// Route::post('/notifications/send', [NotificationController::class, 'sendManual']);


Route::get('users', [CheckUser::class, 'getAllUsers']);

Route::get('/test-mail', function () {
    Mail::raw('This is a test email!', function ($message) {
        $message->to('test@example.com')
                ->subject('Test MailHog Email');
    });

    return 'Mail sent!';
});






Route::get('/test-rabbitmq', function() {
    try {
        $queue = app('queue')->connection('rabbitmq');
        
        // Try to get queue size
        $queueName = config('queue.connections.rabbitmq.queue');
        
        return response()->json([
            'status' => '✅ SUCCESS',
            'message' => 'RabbitMQ connected successfully!',
            'queue_driver' => config('queue.default'),
            'rabbitmq_config' => [
                'host' => config('queue.connections.rabbitmq.hosts.0.host'),
                'port' => config('queue.connections.rabbitmq.hosts.0.port'),
                'user' => config('queue.connections.rabbitmq.hosts.0.user'),
                'queue' => $queueName,
            ],
            'management_ui' => 'http://localhost:15672',
            'credentials' => 'admin / admin123'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => '❌ ERROR',
            'message' => $e->getMessage(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500);
    }
});