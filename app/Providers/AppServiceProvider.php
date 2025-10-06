<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Notifications\NotificationManager;
use App\Services\Notifications\Strategies\EmailNotificationStrategy;
use App\Services\Notifications\Strategies\SmsNotificationStrategy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NotificationManager::class, function ($app) {
            $manager = new NotificationManager();
            $manager->registerStrategy('email', new EmailNotificationStrategy());
            // $manager->registerStrategy('sms', new SmsNotificationStrategy());
            return $manager;
        });
    }

    public function boot(): void
    {
        //
    }
}