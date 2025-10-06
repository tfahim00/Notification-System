<?php

namespace App\Services\Notifications\Strategies;

use App\Models\User;
use App\Models\NotificationReceiver;

interface NotificationStrategyInterface
{
    public function send(User $user, string $message): bool;
}