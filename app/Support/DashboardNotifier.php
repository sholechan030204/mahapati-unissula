<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\DashboardUpdateNotification;

class DashboardNotifier
{
    public static function admins(string $title, string $message, string $url): void
    {
        User::query()->where('role', 'admin')->each(fn (User $admin) => self::user($admin, $title, $message, $url));
    }

    public static function user(?User $user, string $title, string $message, string $url): void
    {
        $user?->notify(new DashboardUpdateNotification([
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ]));
    }
}
