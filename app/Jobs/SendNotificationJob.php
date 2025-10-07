<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Notifications\NotificationManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [10, 30, 60];

    protected $userId;
    protected $message;
    protected $channel;

    public function __construct(int $userId, string $message, string $channel)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->channel = $channel;
    }

    public function handle(NotificationManager $notificationManager)
    {
        try {
            $user = User::findOrFail($this->userId);
            
            Log::info("🐰 [RabbitMQ] Processing notification for user {$this->userId} via {$this->channel}");
            
            $result = $notificationManager->send(
                $this->channel,
                $user,
                $this->message
            );

            if (!$result) {
                throw new \Exception("Notification failed for user {$this->userId}");
            }

            Log::info("✅ [RabbitMQ] Notification sent successfully to user {$this->userId}");

        } catch (\Exception $e) {
            Log::error("❌ [RabbitMQ] Job failed for user {$this->userId}: " . $e->getMessage());
            Log::error("🔄 [RabbitMQ] Attempt {$this->attempts()} of {$this->tries}");
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical("💀 [RabbitMQ] Job permanently failed for user {$this->userId}: " . $exception->getMessage());
    }
}