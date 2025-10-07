<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected $position;
    protected $message;
    protected $channels;

    public function __construct(string $position, string $message, array $channels)
    {
        $this->position = $position;
        $this->message = $message;
        $this->channels = $channels;
    }

    public function handle()
    {
        try {
            Log::info("🐰 [RabbitMQ] Processing bulk notification for position: {$this->position}");
            
            $users = User::where('position', $this->position)->get();
            
            Log::info("👥 [RabbitMQ] Found {$users->count()} users");

            foreach ($users as $user) {
                foreach ($this->channels as $channel) {
                    SendNotificationJob::dispatch($user->id, $this->message, $channel);
                }
            }

            Log::info("✅ [RabbitMQ] Dispatched jobs for {$users->count()} users");

        } catch (\Exception $e) {
            Log::error("❌ [RabbitMQ] Bulk job failed: " . $e->getMessage());
            throw $e;
        }
    }
}