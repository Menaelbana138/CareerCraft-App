<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CareerPath;
use App\Models\User;
use App\Services\AIService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function __construct(
        protected RecommendationService $recommender,
        protected AIService             $ai,
    ) {}

    /**
     * GET /api/user/roadmap
     * Returns career path recommendations (DB-based + AI-based merged).
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // DB-based recommendations (skill matching)
        $dbPaths = $this->recommender->suggestedCareerPathsForUser($user);

        // AI-based recommendations
        $aiRecommendations = $this->ai->isAvailable()
            ? $this->ai->recommendCareerPaths($user)
            : [];

        // User's current career paths with progress
        $userPaths = $user->careerPaths()
            ->withPivot(['status', 'progress'])
            ->get();

        return response()->json([
            'career_paths'       => $dbPaths,
            'ai_recommendations' => $aiRecommendations,
            'user_paths'         => $userPaths,
        ]);
    }

    /**
     * GET /api/user/roadmap/paths/{careerPathId}
     * Returns detail + skill gap + courses for a single career path.
     */
    public function show(Request $request, CareerPath $careerPathId)
    {
        /** @var User $user */
        $user = $request->user();

        $userSkillIds = $user->skills()->pluck('skills.id')->map(fn ($id) => (int) $id)->all();
        $required     = array_map('intval', $careerPathId->required_skills ?? []);
        $missingIds   = array_diff($required, $userSkillIds);

        $courses = $this->recommender->coursesForMissingSkills($missingIds);

        // Pivot for this user if enrolled
        $pivot = $user->careerPaths()
            ->where('career_paths.id', $careerPathId->id)
            ->withPivot(['status', 'progress'])
            ->first();

        return response()->json([
            'career_path'    => $careerPathId,
            'missing_skills' => $missingIds,
            'courses'        => $courses,
            'user_progress'  => $pivot?->pivot ?? null,
        ]);
    }
}
