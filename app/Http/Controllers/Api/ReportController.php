<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AIService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected AIService             $ai,
        protected RecommendationService $recommender,
    ) {}

    /**
     * GET /api/user/report
     */
    public function careerReport(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->load(['skills', 'profile', 'applications', 'careerPaths', 'assessments.result']);

        $userSkillIds  = $user->skills->pluck('id')->map(fn ($id) => (int) $id)->all();
        $careerPaths   = $this->recommender->suggestedCareerPathsForUser($user);
        $topPath       = $careerPaths->first();
        $missingIds    = $topPath
            ? array_diff(array_map('intval', $topPath->required_skills ?? []), $userSkillIds)
            : [];
        $courses       = $this->recommender->coursesForMissingSkills($missingIds, 3);

        // AI recommendations
        $aiRecs = $this->ai->isAvailable() ? $this->ai->recommendCareerPaths($user) : [];

        // Assessment history
        $assessmentHistory = $user->assessments->map(fn ($a) => [
            'date'  => $a->date,
            'type'  => $a->type,
            'score' => $a->result?->score,
        ]);

        return response()->json([
            'user'                 => $user->only(['id', 'name', 'email']),
            'profile'              => $user->profile,
            'skills'               => $user->skills,
            'applications_count'   => $user->applications->count(),
            'career_paths'         => $careerPaths->take(5),
            'top_path'             => $topPath,
            'missing_skills_count' => count($missingIds),
            'recommended_courses'  => $courses,
            'ai_recommendations'   => array_slice($aiRecs, 0, 5),
            'assessment_history'   => $assessmentHistory,
            'generated_at'         => now()->toISOString(),
        ]);
    }
}
