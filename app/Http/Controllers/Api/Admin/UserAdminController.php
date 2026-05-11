<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    /**
     * GET /api/admin/users
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->with(['skills'])
            ->latest('id')
            ->paginate(30);

        return response()->json($users);
    }

    /**
     * PUT /api/admin/users/{userId}/suspend
     * Toggle suspend/unsuspend
     */
    public function suspend(Request $request, User $userId)
    {
        if ($userId->suspended_at) {
            $userId->forceFill(['suspended_at' => null])->save();
            $userId->tokens()->delete();
            $message = 'User unsuspended.';
        } else {
            $userId->forceFill(['suspended_at' => now()])->save();
            $userId->tokens()->delete();
            $message = 'User suspended.';
        }

        return response()->json(['message' => $message, 'user' => $userId->fresh()]);
    }
}
