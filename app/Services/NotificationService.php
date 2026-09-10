<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    public function send(
        User $user,
        string $title,
        string $message,
        string $type = 'system',
        ?string $actionUrl = null
    ): AppNotification {
        return AppNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);
    }

    public function markAllAsRead(User $user): void
    {
        $user->appNotifications()->where('is_read', false)->update(['is_read' => true]);
    }
}
