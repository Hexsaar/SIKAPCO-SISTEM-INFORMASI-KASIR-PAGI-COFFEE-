<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function notifyAdmins(string $title, string $message, string $type = 'info', ?string $url = null)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'url' => $url,
            ]);
        }
    }

    public static function notifyUser(User $user, string $title, string $message, string $type = 'info', ?string $url = null)
    {
        Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
        ]);
    }

    public static function notifyUserPending(User $user)
    {
        self::notifyAdmins(
            'New User Registration',
            "{$user->name} has registered and is waiting for approval. Click to review and assign role.",
            'warning',
            route('admin.employees.pending')
        );
    }

    public static function notifyUserApproved(User $user, string $role)
    {
        self::notifyUser(
            $user,
            'Account Approved',
            "Your account has been approved as {$role}. You can now login.",
            'success',
            route('login')
        );
    }

    public static function notifyUserRejected(User $user)
    {
        self::notifyUser(
            $user,
            'Account Rejected',
            'Your registration has been rejected by admin.',
            'error'
        );
    }
}
