<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\User;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $assessments = $user->assessments()->with('result')->latest('date')->paginate(20);

        return response()->json($assessments);
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'type'  => ['nullable', 'string', 'in:general,personality_career_test,career_strengths_finder'],
            'date'  => ['required', 'date'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        // Prevent duplicate assessment on the same date for the same user
        $alreadyExists = $user->assessments()
            ->whereDate('date', $data['date'])
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'You already have an assessment recorded for this date.',
            ], 422);
        }

        $assessment = $user->assessments()->create([
            'type' => $data['type'] ?? Assessment::TYPE_GENERAL,
            'date' => $data['date'],
        ]);
        $assessment->result()->create(['score' => $data['score']]);

        return response()->json(['assessment' => $assessment->load('result')], 201);
    }

    public function show(Request $request, Assessment $assessmentId)
    {
        /** @var User $user */
        $user = $request->user();

        if ($assessmentId->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['assessment' => $assessmentId->load('result')]);
    }

    public function result(Request $request, Assessment $assessmentId)
    {
        /** @var User $user */
        $user = $request->user();

        if ($assessmentId->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $result = $assessmentId->result;

        return response()->json(['result' => $result]);
    }
}
