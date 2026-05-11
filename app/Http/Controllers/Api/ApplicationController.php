<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * POST /api/applications   { job_id }
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
        ]);

        // Prevent duplicate applications
        $exists = Application::query()
            ->where('user_id', $user->id)
            ->where('job_id', $data['job_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You already applied to this job.'], 422);
        }

        $application = Application::query()->create([
            'user_id' => $user->id,
            'job_id'  => $data['job_id'],
            'status'  => 'pending',
        ]);

        return response()->json(['application' => $application->load('job')], 201);
    }

    /**
     * GET /api/applications
     */
    public function myApplications(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $applications = $user->applications()
            ->with(['job:id,title,location,company_id', 'job.company:id,name'])
            ->latest('id')
            ->paginate(20);

        return response()->json($applications);
    }
}
