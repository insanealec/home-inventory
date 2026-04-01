<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Facades\Actions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Actions::registerCommands();
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Fortify login: 5 attempts per minute per email + IP
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', '');

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        // Fortify two-factor: 5 attempts per minute per session + IP
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->getId().$request->ip());
        });

        // API token creation/deletion: 20 per minute per user (or IP if unauthenticated)
        RateLimiter::for('tokens', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?? $request->ip());
        });

        // General API: tiered by plan — free 60/min, pro 300/min, keyed per user
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            $limit = $user?->isPro() ? 300 : 60;

            return Limit::perMinute($limit)->by($user?->id ?? $request->ip());
        });
    }
}
