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
     *
     * @OA\Get(
     *     path="/api/ai/status",
     *     summary="AI service availability",
     *     tags={"AI"},
     *     @OA\Response(response=200, description="Returns availability flag")
     * )
     */
    public function status()
    {
        return response()->json(['available' => $this->ai->isAvailable()]);
    }

    /**
     * GET /api/ai/career-recommendations
     *
     * @OA\Get(
     *     path="/api/ai/career-recommendations",
     *     summary="Get AI career recommendations",
     *     tags={"AI"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Career recommendations returned"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=429, description="Too many requests")
     * )
     */
    public function careerRecommendations(Request $request)
    {
        $user = $request->user();
        $recommendations = $this->ai->recommendCareerPaths($user);

        return response()->json(['recommendations' => $recommendations]);
    }

    /**
     * GET /api/ai/job-match/{jobId}
     *
     * @OA\Get(
     *     path="/api/ai/job-match/{jobId}",
     *     summary="AI job match score",
     *     tags={"AI"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="jobId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Match score returned"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=429, description="Too many requests")
     * )
     */
    public function jobMatch(Request $request, Job $jobId)
    {
        $user  = $request->user();
        $score = $this->ai->jobMatchScore($user, $jobId);

        return response()->json(['match_score' => $score]);
    }

    /**
     * POST /api/ai/advice
     *
     * @OA\Post(
     *     path="/api/ai/advice",
     *     summary="Get AI career advice",
     *     tags={"AI"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="question", type="string", example="How do I become a backend developer?")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Advice returned"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=429, description="Too many requests")
     * )
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
     * GET /api/ai/suggested-skills
     *
     * @OA\Get(
     *     path="/api/ai/suggested-skills",
     *     summary="Suggested skills for a career path",
     *     tags={"AI"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="career_path", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Skills list returned"),
     *     @OA\Response(response=429, description="Too many requests")
     * )
     */
    public function suggestedSkills(Request $request)
    {
        $careerPath = $request->query('career_path', '');
        $skills     = $this->ai->suggestedSkills($careerPath);

        return response()->json(['skills' => $skills]);
    }

    /**
     * POST /api/ai/resume-review
     *
     * @OA\Post(
     *     path="/api/ai/resume-review",
     *     summary="AI resume review",
     *     tags={"AI"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="resume_text", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Review returned"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=429, description="Too many requests")
     * )
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
     *
     * @OA\Post(
     *     path="/api/ai/interview-simulate",
     *     summary="Simulated interview turns",
     *     tags={"AI"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="job_id", type="integer"),
     *             @OA\Property(property="job_title", type="string"),
     *             @OA\Property(property="job_context", type="string", nullable=true),
     *             @OA\Property(property="conversation", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Next question / feedback"),
     *     @OA\Response(response=429, description="Too many requests")
     * )
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
     *
     * @OA\Post(
     *     path="/api/ai/chatbot",
     *     summary="Chat with AI assistant",
     *     tags={"AI"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="What skills should I learn?"),
     *             @OA\Property(property="history", type="array", nullable=true, @OA\Items(type="object")),
     *             @OA\Property(property="user_context", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reply from chatbot"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=429, description="Too many requests")
     * )
     */
    public function chatbot(Request $request)
    {
        $data = $request->validate([
            'message'      => ['required', 'string', 'max:2000'],
            'history'      => ['nullable', 'array'],
            'user_context' => ['nullable', 'string', 'max:1000'],
        ]);

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
