<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\SendNotificationJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue daily notifications for all users via RabbitMQ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🐰 Starting daily notifications via RabbitMQ...');
        
        Log::info('[Scheduled] Starting daily notifications');

        $users = User::all();
        $queuedCount = 0;

        foreach ($users as $user) {
            try {
                $message = "Daily reminder for {$user->username} - Position: {$user->position}";
                
                // Dispatch to RabbitMQ queue instead of sending immediately
                SendNotificationJob::dispatch($user->id, $message, 'email');
                
                $queuedCount++;
                
                $this->info("✓ Queued notification for {$user->username}");
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to queue for {$user->username}: {$e->getMessage()}");
                Log::error("[Scheduled] Failed to queue for user {$user->id}: {$e->getMessage()}");
            }
        }

        $this->info("🎉 Queued {$queuedCount}/{$users->count()} notifications successfully");
        
        Log::info("[Scheduled] Queued {$queuedCount} notifications in RabbitMQ");

        return Command::SUCCESS;
    }
}