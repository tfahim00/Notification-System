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






// Route::get('/db-test', function () {
//     try {
//         $count = \App\Models\User::count();
//         return response()->json([
//             'success' => true,
//             'user_count' => $count
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'error' => $e->getMessage()
//         ]);
//     }
// });

// Route::get('/users-simple', function () {
//     $users = \App\Models\User::all();
//     return $users;
// });