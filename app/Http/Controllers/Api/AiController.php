<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\AIService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(protected AIService $ai) {}

    /**
     * GET /api/ai/status
     */
    public function status()
    {
        return response()->json(['available' => $this->ai->isAvailable()]);
    }

    /**
     * GET /api/ai/career-recommendations
     */
    public function careerRecommendations(Request $request)
    {
        $user = $request->user();
        $recommendations = $this->ai->recommendCareerPaths($user);

        return response()->json(['recommendations' => $recommendations]);
    }

    /**
     * GET /api/ai/job-match/{jobId}
     */
    public function jobMatch(Request $request, Job $jobId)
    {
        $user  = $request->user();
        $score = $this->ai->jobMatchScore($user, $jobId);

        return response()->json(['match_score' => $score]);
    }

    /**
     * POST /api/ai/advice   { question }
     */
    public function advice(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $answer = $this->ai->careerAdvice($data['question'], $request->user());

        return response()->json(['answer' => $answer]);
    }

    /**
     * GET /api/ai/suggested-skills?career_path=Data+Scientist
     */
    public function suggestedSkills(Request $request)
    {
        $careerPath = $request->query('career_path', '');
        $skills     = $this->ai->suggestedSkills($careerPath);

        return response()->json(['skills' => $skills]);
    }

    /**
     * POST /api/ai/resume-review   { resume_text }
     */
    public function resumeReview(Request $request)
    {
        $data = $request->validate([
            'resume_text' => ['required', 'string', 'max:10000'],
        ]);

        $result = $this->ai->resumeReview($data['resume_text']);

        return response()->json($result ?? ['error' => 'AI not available']);
    }

    /**
     * POST /api/ai/interview-simulate
     * { job_id, job_title, job_context, conversation: [] }
     */
    public function interviewSimulate(Request $request)
    {
        $data = $request->validate([
            'job_id'       => ['required', 'integer'],
            'job_title'    => ['required', 'string', 'max:255'],
            'job_context'  => ['nullable', 'string', 'max:2000'],
            'conversation' => ['nullable', 'array'],
        ]);

        $result = $this->ai->interviewSimulate(
            $data['job_id'],
            $data['job_title'],
            $data['job_context'] ?? '',
            $data['conversation'] ?? [],
        );

        return response()->json($result);
    }

    /**
     * POST /api/ai/chatbot
     * { message, history: [], user_context }
     */
    public function chatbot(Request $request)
    {
        $data = $request->validate([
            'message'      => ['required', 'string', 'max:2000'],
            'history'      => ['nullable', 'array'],
            'user_context' => ['nullable', 'string', 'max:1000'],
        ]);

        // Build user context from authenticated user's skills
        $user = $request->user();
        $userContext = $data['user_context'] ?? '';
        if (empty($userContext) && $user) {
            $skills = $user->skills()->pluck('name')->implode(', ');
            if ($skills) {
                $userContext = "User skills: {$skills}.";
            }
        }

        $result = $this->ai->chatbot(
            $data['message'],
            $data['history'] ?? [],
            $userContext,
        );

        return response()->json($result);
    }
}
