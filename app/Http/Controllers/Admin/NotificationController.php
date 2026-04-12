<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications', compact('notifications'));
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getCount(): JsonResponse
    {
        $count = auth()->user()->unread_notifications_count;

        return response()->json(['count' => $count]);
    }

    public function getRecent(): JsonResponse
    {
        $notifications = auth()->user()
            ->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'message', 'type', 'url', 'created_at']);

        return response()->json(['notifications' => $notifications]);
    }
}
