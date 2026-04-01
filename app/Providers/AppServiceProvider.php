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
        // Fortify login: keyed per email + IP
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', '');

            return Limit::perMinute(config('rate-limiting.auth'))->by($email.$request->ip());
        });

        // Fortify two-factor: keyed per session + IP
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(config('rate-limiting.auth'))->by($request->session()->getId().$request->ip());
        });

        // Token create/delete: keyed per user
        RateLimiter::for('tokens', function (Request $request) {
            return Limit::perMinute(config('rate-limiting.tokens'))->by($request->user()?->id ?? $request->ip());
        });

        // General API: tiered by plan, keyed per user
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            $limit = config($user?->isPro() ? 'rate-limiting.api.pro' : 'rate-limiting.api.free');

            return Limit::perMinute($limit)->by($user?->id ?? $request->ip());
        });
    }
}
