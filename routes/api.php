<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\UserSkillsController;
use App\Http\Controllers\Api\RoadmapController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\Admin\UserAdminController;
use App\Http\Controllers\Api\Admin\CareerPathAdminController;
use App\Http\Controllers\Api\Admin\SkillAdminController;
use App\Http\Controllers\Api\Admin\CourseAdminController;
use App\Http\Controllers\Api\Admin\RecommendationAdminController;
use App\Http\Controllers\Api\Company\ApplicationController as CompanyApplicationController;
use App\Http\Controllers\Api\Company\ProfileController as CompanyProfileController;

/*
|--------------------------------------------------------------------------
| API Routes (Production-ready, consolidated per Tech Lead review)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user/me', function (Request $request) {
    return $request->user();
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'time' => now()->toISOString(),
    ]);
});

// Unified auth (type: user|company in body)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/google', [AuthController::class, 'googleLogin']);

    // Password reset (public)
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Email verification link callback (public — arrived from email)
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');
});

// AI status (public - no auth)
Route::get('/ai/status', [AiController::class, 'status']);

// RESTful jobs (scope from auth: company → own jobs, public → all)
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{jobId}', [JobController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/resend-verification', [AuthController::class, 'resendVerification']);

    // Company: profile + POST jobs + GET applications + PUT application status
    Route::middleware(['tokenable:company'])->group(function () {
        Route::get('/company/profile', [CompanyProfileController::class, 'show']);
        Route::put('/company/profile', [CompanyProfileController::class, 'update']);
        Route::post('/jobs', [JobController::class, 'store']);
        Route::get('/jobs/{jobId}/applications', [JobController::class, 'applications']);
        Route::put('/jobs/{jobId}/applications/{applicationId}', [CompanyApplicationController::class, 'updateStatus']);
    });

    // User APIs
    Route::middleware(['tokenable:user', 'role:user,admin'])->group(function () {
        Route::get('/user/profile', [UserProfileController::class, 'show']);
        Route::put('/user/profile', [UserProfileController::class, 'update']);

        Route::get('/user/skills', [UserSkillsController::class, 'index']);
        Route::post('/user/skills', [UserSkillsController::class, 'store']);

        // Merged: roadmap + recommendations
        Route::get('/user/roadmap', [RoadmapController::class, 'index']);
        Route::get('/user/roadmap/paths/{careerPathId}', [RoadmapController::class, 'show']);

        Route::get('/user/dashboard', [DashboardController::class, 'show']);

        Route::post('/applications', [ApplicationController::class, 'store']);
        Route::get('/applications', [ApplicationController::class, 'myApplications']);

        Route::get('/user/assessments', [AssessmentController::class, 'index']);
        Route::post('/user/assessments', [AssessmentController::class, 'store']);
        Route::get('/user/assessments/{assessmentId}', [AssessmentController::class, 'show']);
        Route::get('/user/assessments/{assessmentId}/result', [AssessmentController::class, 'result']);

        Route::get('/user/messages', [MessageController::class, 'index']);
        Route::post('/user/messages', [MessageController::class, 'store']);

        Route::get('/user/notifications', [NotificationController::class, 'index']);
        Route::get('/user/report', [ReportController::class, 'careerReport']);

        // AI endpoints — throttle 20 req/min per user/IP (stack with global api limiter)
        Route::middleware('throttle:ai')->group(function () {
            Route::get('/ai/career-recommendations', [AiController::class, 'careerRecommendations']);
            Route::get('/ai/job-match/{jobId}', [AiController::class, 'jobMatch']);
            Route::post('/ai/advice', [AiController::class, 'advice']);
            Route::get('/ai/suggested-skills', [AiController::class, 'suggestedSkills']);
            Route::post('/ai/resume-review', [AiController::class, 'resumeReview']);
            Route::post('/ai/interview-simulate', [AiController::class, 'interviewSimulate']);
            Route::post('/ai/chatbot', [AiController::class, 'chatbot']);
        });
    });

    // Admin APIs
    Route::prefix('admin')->middleware(['tokenable:user', 'role:admin'])->group(function () {
        Route::get('/users', [UserAdminController::class, 'index']);
        Route::put('/users/{userId}/suspend', [UserAdminController::class, 'suspend']);
        Route::post('/users/{userId}/recommendations', [RecommendationAdminController::class, 'store']);

        Route::get('/skills', [SkillAdminController::class, 'index']);
        Route::post('/skills', [SkillAdminController::class, 'store']);
        Route::put('/skills/{skillId}', [SkillAdminController::class, 'update']);
        Route::delete('/skills/{skillId}', [SkillAdminController::class, 'destroy']);

        Route::get('/courses', [CourseAdminController::class, 'index']);
        Route::post('/courses', [CourseAdminController::class, 'store']);
        Route::put('/courses/{courseId}', [CourseAdminController::class, 'update']);
        Route::delete('/courses/{courseId}', [CourseAdminController::class, 'destroy']);

        Route::post('/career-paths', [CareerPathAdminController::class, 'store']);
        Route::put('/career-paths/{careerPathId}', [CareerPathAdminController::class, 'update']);
        Route::delete('/career-paths/{careerPathId}', [CareerPathAdminController::class, 'destroy']);
    });
});
