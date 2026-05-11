<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * PUT /api/jobs/{jobId}/applications/{applicationId}
     * Company updates an application status: pending|reviewed|accepted|rejected
     */
    public function updateStatus(Request $request, Job $jobId, Application $applicationId)
    {
        /** @var Company $company */
        $company = $request->user();

        if (!$company instanceof Company || $jobId->company_id !== $company->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($applicationId->job_id !== $jobId->id) {
            return response()->json(['message' => 'Application does not belong to this job.'], 404);
        }

        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,reviewed,accepted,rejected'],
        ]);

        $applicationId->update(['status' => $data['status']]);

        return response()->json(['application' => $applicationId->fresh()]);
    }
}
