<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected ?string $internalUrl;
    protected ?string $internalSecret;
    protected ?string $openaiKey;

    public function __construct()
    {
        $this->internalUrl = rtrim(config('services.ai.url', ''), '/');
        $this->internalSecret = config('services.ai.secret');
        $this->openaiKey = config('services.openai.key');
    }

    /**
     * Headers for every HTTP call to the internal AI service (Railway: AI_SERVICE_SECRET).
     */
    protected function internalHeaders(): array
    {
        if ($this->internalSecret === null || $this->internalSecret === '') {
            return [];
        }

        return ['X-Internal-Key' => $this->internalSecret];
    }

    /**
     * AI available if Internal Service OR OpenAI is configured.
     */
    public function isAvailable(): bool
    {
        if (!empty($this->internalUrl)) {
            try {
                $r = Http::withHeaders($this->internalHeaders())
                    ->timeout(5)
                    ->get($this->internalUrl . '/health');
                return $r->successful() && ($r->json('ai_available') ?? false);
            } catch (\Throwable $e) {
                Log::warning('AI service health check failed', ['error' => $e->getMessage()]);
                return false;
            }
        }
        return !empty($this->openaiKey);
    }

    public function recommendCareerPaths(User $user): array
    {
        $userSkills = $user->skills()->pluck('name')->implode(', ');

        if (!empty($this->internalUrl)) {
            $r = $this->internalPost('/career-recommendations', ['user_skills' => $userSkills]);
            return $r['recommendations'] ?? [];
        }

        return $this->recommendCareerPathsOpenAI($userSkills);
    }

    public function jobMatchScore(User $user, Job $job): ?int
    {
        $userSkills = $user->skills()->pluck('name')->implode(', ');
        $jobSkills = $job->relationLoaded('skills') && $job->skills->isNotEmpty()
            ? $job->skills->pluck('name')->implode(', ')
            : $job->title;

        if (!empty($this->internalUrl)) {
            $r = $this->internalPost('/job-match', [
                'user_skills' => $userSkills,
                'job_title' => $job->title,
                'job_skills' => $jobSkills,
            ]);
            return $r['match_score'] ?? null;
        }

        return $this->jobMatchScoreOpenAI($userSkills, $job->title, $jobSkills);
    }

    public function careerAdvice(string $question, ?User $user = null): ?string
    {
        $context = $user ? 'User has skills: ' . $user->skills()->pluck('name')->implode(', ') . '. ' : '';

        if (!empty($this->internalUrl)) {
            $r = $this->internalPost('/advice', ['question' => $question, 'context' => $context]);
            return $r['answer'] ?? null;
        }

        return $this->careerAdviceOpenAI($question, $context);
    }

    public function suggestedSkills(string $careerPathTitle): array
    {
        if (!empty($this->internalUrl)) {
            $r = $this->internalPost('/suggested-skills', ['career_path' => $careerPathTitle]);
            return $r['skills'] ?? [];
        }

        return $this->suggestedSkillsOpenAI($careerPathTitle);
    }

    public function resumeReview(string $resumeText): ?array
    {
        if (!empty($this->internalUrl)) {
            $r = $this->internalPost('/resume-review', ['resume_text' => substr($resumeText, 0, 10000)]);
            return $r ?: null;
        }

        return $this->resumeReviewOpenAI($resumeText);
    }

    public function interviewSimulate(int $jobId, string $jobTitle, string $jobContext, array $conversation): array
    {
        if (!empty($this->internalUrl)) {
            $r = $this->internalPost('/interview-simulate', [
                'job_id' => $jobId,
                'job_title' => $jobTitle,
                'job_context' => $jobContext,
                'conversation' => $conversation,
            ]);
            return $r ?? ['error' => 'AI not available'];
        }

        return $this->interviewSimulateOpenAI($jobTitle, $jobContext, $conversation);
    }

    protected function internalPost(string $path, array $body): ?array
    {
        try {
            $r = Http::withHeaders($this->internalHeaders())
                ->timeout(60)
                ->post($this->internalUrl . $path, $body);
            if (!$r->successful()) {
                Log::warning('AI service error', ['path' => $path, 'status' => $r->status()]);
                return null;
            }
            return $r->json();
        } catch (\Throwable $e) {
            Log::error('AI service request failed', ['path' => $path, 'error' => $e->getMessage()]);
            return null;
        }
    }

    // --- OpenAI fallback ---

    protected function recommendCareerPathsOpenAI(string $userSkills): array
    {
        if (empty($this->openaiKey)) {
            return [];
        }
        $prompt = "User skills: {$userSkills}. Suggest 3-5 career paths as JSON array of objects with keys: title, description, match_score (1-100), reason. Be concise.";
        $response = $this->openaiChat($prompt);
        return $response ? $this->parseJsonArray($response) : [];
    }

    protected function jobMatchScoreOpenAI(string $userSkills, string $jobTitle, string $jobSkills): ?int
    {
        if (empty($this->openaiKey)) {
            return null;
        }
        $prompt = "User skills: {$userSkills}. Job: {$jobTitle}. Required: {$jobSkills}. Reply with ONLY a number 0-100 (match score).";
        $response = $this->openaiChat($prompt, 50);
        if (!$response) {
            return null;
        }
        preg_match('/\d+/', trim($response), $m);
        return isset($m[0]) ? min(100, max(0, (int) $m[0])) : null;
    }

    protected function careerAdviceOpenAI(string $question, string $context): ?string
    {
        if (empty($this->openaiKey)) {
            return null;
        }
        return $this->openaiChat($context . "Career advice question: {$question}. Give a helpful, concise answer (2-4 sentences).");
    }

    protected function suggestedSkillsOpenAI(string $careerPathTitle): array
    {
        if (empty($this->openaiKey)) {
            return [];
        }
        $prompt = "For career path '{$careerPathTitle}', list 5-8 essential skills as JSON array of strings. Example: [\"Skill1\", \"Skill2\"]";
        $response = $this->openaiChat($prompt);
        $parsed = $this->parseJsonArray($response);
        return is_array($parsed) ? array_map('strval', $parsed) : [];
    }

    protected function resumeReviewOpenAI(string $resumeText): ?array
    {
        if (empty($this->openaiKey)) {
            return null;
        }
        $prompt = "Review this CV/Resume and respond with a JSON object: {\"score\": 0-100, \"strengths\": [\"s1\", \"s2\"], \"improvements\": [\"i1\", \"i2\"], \"summary\": \"brief feedback\"}. Resume:\n\n" . substr($resumeText, 0, 4000);
        $response = $this->openaiChat($prompt, 800);
        if (!$response) {
            return null;
        }
        if (preg_match('/\{[\s\S]*\}/', $response, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return ['score' => null, 'summary' => $response, 'strengths' => [], 'improvements' => []];
    }

    protected function interviewSimulateOpenAI(string $jobTitle, string $jobContext, array $conversation): array
    {
        if (empty($this->openaiKey)) {
            return ['error' => 'AI not available'];
        }
        $convText = '';
        foreach ($conversation as $i => $turn) {
            $convText .= "Q" . ($i + 1) . ": " . ($turn['question'] ?? '') . "\nA" . ($i + 1) . ": " . ($turn['answer'] ?? '') . "\n";
        }
        if (empty($conversation)) {
            $prompt = "You are an interviewer for: {$jobTitle}. Context: {$jobContext}. Ask the first interview question (1 sentence). Reply with ONLY the question, no prefix.";
            $q = $this->openaiChat($prompt, 150);
            return ['question' => $q ?? 'Tell me about yourself.', 'question_number' => 1, 'completed' => false];
        }
        $prompt = "Job: {$jobTitle}. Context: {$jobContext}. Interview so far:\n{$convText}\nGive brief feedback on the last answer (1 sentence), then the next question. Format: FEEDBACK: ... QUESTION: ...";
        $response = $this->openaiChat($prompt, 300);
        if (!$response) {
            return ['feedback' => 'Good.', 'question' => 'Any other strengths?', 'question_number' => count($conversation) + 1, 'completed' => false];
        }
        $feedback = '';
        $question = '';
        if (preg_match('/FEEDBACK:\s*(.+?)(?=QUESTION:|$)/is', $response, $m)) {
            $feedback = trim($m[1]);
        }
        if (preg_match('/QUESTION:\s*(.+?)$/is', $response, $m)) {
            $question = trim($m[1]);
        }
        if (empty($question)) {
            $question = 'Do you have any questions for us?';
        }
        $num = count($conversation) + 1;
        $completed = $num >= 5;
        return [
            'feedback' => $feedback,
            'question' => $question,
            'question_number' => $num,
            'completed' => $completed,
            'overall_feedback' => $completed ? $this->openaiChat("Based on this interview:\n{$convText}\nGive 2-3 sentence overall feedback.", 200) : null,
        ];
    }

    protected function openaiChat(string $prompt, int $maxTokens = 500): ?string
    {
        try {
            $response = Http::withToken($this->openaiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-3.5-turbo'),
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => $maxTokens,
                ]);
            if (!$response->successful()) {
                Log::warning('OpenAI API error', ['status' => $response->status()]);
                return null;
            }
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        } catch (\Throwable $e) {
            Log::error('OpenAI request failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function parseJsonArray(?string $content): array
    {
        if (!$content) {
            return [];
        }
        if (preg_match('/\[[\s\S]*\]/', trim($content), $m)) {
            $decoded = json_decode($m[0], true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * POST /api/ai/chatbot
     * Sends message + history to Flask /chatbot (Gemini).
     */
    public function chatbot(string $message, array $history = [], string $userContext = ''): array
    {
        if (!empty($this->internalUrl)) {
            $r = $this->internalPost('/chatbot', [
                'message'      => $message,
                'history'      => $history,
                'user_context' => $userContext,
            ]);
            if ($r && isset($r['reply'])) {
                return $r;
            }
        }

        // Fallback to OpenAI if Flask not available
        if (!empty($this->openaiKey)) {
            $prompt = ($userContext ? $userContext . ' ' : '') . $message;
            $reply  = $this->openaiChat($prompt);
            return ['reply' => $reply ?? 'Sorry, I could not generate a response.'];
        }

        return ['reply' => 'Chatbot is not available. Please configure AI service.'];
    }
}
