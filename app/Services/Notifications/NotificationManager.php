<?php

namespace App\Services\Notifications;

use App\Models\User;
use App\Services\Notifications\Strategies\NotificationStrategyInterface;

class NotificationManager
{
    private array $strategies = [];

    public function registerStrategy(string $name, NotificationStrategyInterface $strategy): void
    {
        $this->strategies[$name] = $strategy;
    }

    public function send(string $strategyName, User $user, string $message): bool
    {
        if (!isset($this->strategies[$strategyName])) {
            throw new \InvalidArgumentException("Strategy '{$strategyName}' not found");
        }

        $service = new NotificationService($this->strategies[$strategyName]);
        return $service->notify($user, $message);
    }

    public function sendViaMultipleChannels(array $strategyNames, User $user, string $message): array
    {
        $results = [];
        foreach ($strategyNames as $strategyName) {
            $results[$strategyName] = $this->send($strategyName, $user, $message);
        }
        return $results;
    }
}