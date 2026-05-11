<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected AIService $ai) {}

    /**
     * GET /api/user/dashboard
     */
    public function show(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->load(['skills', 'profile', 'applications', 'careerPaths']);

        $skillsCount      = $user->skills->count();
        $applicationsCount = $user->applications->count();
        $careerPathsCount  = $user->careerPaths->count();

        $inProgressPaths = $user->careerPaths
            ->filter(fn ($p) => ($p->pivot->status ?? '') === 'in_progress')
            ->count();

        // AI status
        $aiAvailable = $this->ai->isAvailable();

        // Latest recommendations (lightweight — no extra HTTP call here unless AI is up)
        $aiRecommendations = $aiAvailable ? $this->ai->recommendCareerPaths($user) : [];

        return response()->json([
            'user'              => $user->only(['id', 'name', 'email', 'profile_picture', 'role']),
            'profile'           => $user->profile,
            'stats'             => [
                'skills'            => $skillsCount,
                'applications'      => $applicationsCount,
                'career_paths'      => $careerPathsCount,
                'in_progress_paths' => $inProgressPaths,
            ],
            'ai_available'         => $aiAvailable,
            'ai_recommendations'   => array_slice($aiRecommendations, 0, 3),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ]);
    }
}
