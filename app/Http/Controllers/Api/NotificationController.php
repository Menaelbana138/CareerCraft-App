<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/user/notifications
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(30);

        // Mark all as read when fetched
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json($notifications);
    }
}
