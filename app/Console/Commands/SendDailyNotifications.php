<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Notifications\NotificationManager;
use Illuminate\Console\Command;

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
    protected $description = 'Send daily notifications to all notification receivers';

    private NotificationManager $notificationManager;

    public function __construct(NotificationManager $notificationManager)
    {
        parent::__construct();
        $this->notificationManager = $notificationManager;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily notifications...');

        $users = User::all();
        $successCount = 0;

        foreach ($users as $user) {
            $message = "Daily reminder for {$user->username} - Position: {$user->position}";
            
            $result = $this->notificationManager->send('email', $user, $message);
            
            if ($result) {
                $successCount++;
            }
        }

        $this->info("Sent {$successCount}/{$users->count()} notifications successfully");
        
        return Command::SUCCESS;
    }
}
