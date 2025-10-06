<?php

namespace App\Services\Notifications\Strategies;

use App\Mail\NotificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationStrategy implements NotificationStrategyInterface
{
    public function send(User $user, string $message): bool
    {
        try {
            Mail::to($user->email)->send(new NotificationMail($user, $message));
            
            Log::info("Email sent to {$user->email}: {$message}");
            return true;
        } catch (\Exception $e) {
            Log::error("Email failed for {$user->email}: " . $e->getMessage());
            return false;
        }
    }
}