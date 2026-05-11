<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;

class UserSkillsController extends Controller
{
    /**
     * GET /api/user/skills
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user   = $request->user();
        $skills = $user->skills()->withPivot('proficiency')->get();

        return response()->json(['skills' => $skills]);
    }

    /**
     * POST /api/user/skills
     * Body: { skills: [ { skill_id: 1, proficiency: "beginner|intermediate|expert" } ] }
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'skills'               => ['required', 'array', 'min:1'],
            'skills.*.skill_id'    => ['required', 'integer', 'exists:skills,id'],
            'skills.*.proficiency' => ['nullable', 'string', 'in:beginner,intermediate,expert'],
        ]);

        $sync = collect($data['skills'])->mapWithKeys(fn ($s) => [
            $s['skill_id'] => ['proficiency' => $s['proficiency'] ?? 'intermediate'],
        ])->all();

        $user->skills()->sync($sync);

        return response()->json([
            'skills' => $user->skills()->withPivot('proficiency')->get(),
        ]);
    }
}
