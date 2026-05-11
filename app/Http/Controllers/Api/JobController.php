<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Http\Request;

/**
 * Unified RESTful jobs: GET/POST /api/jobs
 * Company scope from Authorization token.
 */
class JobController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Company auth → return company's jobs
        if ($user instanceof Company) {
            $jobs = Job::query()
                ->where('company_id', $user->id)
                ->with(['company:id,name'])
                ->latest('id')
                ->paginate(20);
            return response()->json($jobs);
        }

        // Public or user auth → public job list with optional filters
        $query = Job::query()->with(['company:id,name']);

        // Filter by keyword (title or description)
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by location
        if ($location = $request->query('location')) {
            $query->where('location', 'like', "%{$location}%");
        }

        // Filter by skill ids  e.g. ?skills[]=1&skills[]=3
        if ($skillIds = $request->query('skills')) {
            $skillIds = array_filter((array) $skillIds, 'is_numeric');
            if (!empty($skillIds)) {
                $query->whereHas('skills', fn ($q) => $q->whereIn('skills.id', $skillIds));
            }
        }

        $jobs = $query->latest('id')->paginate(20);

        return response()->json($jobs);
    }

    public function store(Request $request)
    {
        /** @var Company $company */
        $company = $request->user();

        if (!$company instanceof Company) {
            return response()->json(['message' => 'Forbidden. Company only.'], 403);
        }

        $data = $request->validate([
            'title'                    => ['required', 'string', 'max:255'],
            'description'              => ['required', 'string'],
            'skills'                   => ['required', 'array', 'min:1'],
            'skills.*.skill_id'        => ['required', 'integer', 'exists:skills,id'],
            'skills.*.required_level'  => ['sometimes', 'string', 'in:beginner,intermediate,expert'],
            'location'                 => ['required', 'string', 'max:255'],
        ]);

        $job = Job::query()->create([
            'company_id'  => $company->id,
            'title'       => $data['title'],
            'description' => $data['description'],
            'location'    => $data['location'],
        ]);

        $skillsSync = collect($data['skills'])->mapWithKeys(fn ($s) => [
            $s['skill_id'] => ['required_level' => $s['required_level'] ?? 'intermediate'],
        ])->all();

        $job->skills()->sync($skillsSync);

        return response()->json(['job' => $job->load('skills')], 201);
    }

    public function show(Request $request, Job $jobId)
    {
        $jobId->load(['company:id,name']);

        return response()->json(['job' => $jobId]);
    }

    public function applications(Request $request, Job $jobId)
    {
        /** @var Company $company */
        $company = $request->user();

        if (!$company instanceof Company || $jobId->company_id !== $company->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $apps = Application::query()
            ->where('job_id', $jobId->id)
            ->with(['user:id,name,email,profile_picture'])
            ->latest('id')
            ->paginate(30);

        return response()->json($apps);
    }
}
