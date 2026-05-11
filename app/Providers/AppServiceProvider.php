<?php

namespace App\Providers;

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureTokenableType;
use App\Services\AIService;
use App\Services\GoogleIdTokenService;
use App\Services\RecommendationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIService::class);
        $this->app->singleton(RecommendationService::class);
        $this->app->singleton(GoogleIdTokenService::class);
    }

    public function boot(): void
    {
        //
    }
}
