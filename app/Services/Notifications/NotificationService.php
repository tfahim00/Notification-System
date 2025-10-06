<?php

namespace App\Services\Notifications;

use App\Models\User;
use App\Services\Notifications\Strategies\NotificationStrategyInterface;

class NotificationService
{
    private NotificationStrategyInterface $strategy;

    public function __construct(NotificationStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(NotificationStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function notify(User $user, string $message): bool
    {
        return $this->strategy->send($user, $message);
    }

    public function notifyMultiple(array $users, string $message): array
    {
        $results = [];
        foreach ($users as $user) {
            $results[$user->id] = $this->notify($user, $message);
        }
        return $results;
    }
}