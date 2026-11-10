<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->getAuthIdentifier();

        $notificationQuery = DB::table('notifications')->where('user_id', $userId);
        $totalNotifications = (clone $notificationQuery)->count();
        $unreadNotifications = (clone $notificationQuery)->whereNull('read_at')->count();
        $readNotifications = $totalNotifications - $unreadNotifications;
        $notifications = (clone $notificationQuery)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('student.notifications', [
            'notifications' => $notifications,
            'totalNotifications' => $totalNotifications,
            'unreadNotifications' => $unreadNotifications,
            'readNotifications' => $readNotifications,
            'isAdmin' => $request->user()->isAdmin(),
        ]);
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        DB::table('notifications')
            ->where('id', $notification)
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return back();
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        DB::table('notifications')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return back();
    }

    public function clear(Request $request): RedirectResponse
    {
        DB::table('notifications')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->delete();

        return back();
    }
}
