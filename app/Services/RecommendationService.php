<?php

namespace App\Services;

use App\Models\CareerPath;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RecommendationService
{
    public function suggestedCareerPathsForUser(User $user): Collection
    {
        $userSkillIds = $user->skills()->pluck('skills.id')->map(fn ($id) => (int) $id)->all();

        // Fetch all career paths and rank by how many required skills the user already has.
        // Paths with at least one matching skill float to the top; ties broken by id desc.
        return CareerPath::query()
            ->latest('id')
            ->get()
            ->map(function (CareerPath $path) use ($userSkillIds) {
                $required = array_map('intval', $path->required_skills ?? []);
                $matched  = count(array_intersect($required, $userSkillIds));
                $total    = count($required);

                $path->match_score    = $total > 0 ? (int) round(($matched / $total) * 100) : 0;
                $path->matched_skills = $matched;
                $path->total_skills   = $total;

                return $path;
            })
            ->sortByDesc('match_score')
            ->values();
    }

    /**
     * Recommend courses based on missing skill ids.
     */
    public function coursesForMissingSkills(array $missingSkillIds, int $limitPerSkill = 5): Collection
    {
        if (empty($missingSkillIds)) {
            return collect();
        }

        return Course::query()
            ->whereIn('skill_id', $missingSkillIds)
            ->latest('id')
            ->limit(max(1, $limitPerSkill * count($missingSkillIds)))
            ->get();
    }
}

