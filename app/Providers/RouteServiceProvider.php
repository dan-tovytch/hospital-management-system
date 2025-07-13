<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function ($request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)
                ->by($email . $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Você excedeu o número de tentativas de login. Tente novamente em alguns minutos.',
                    ], 429);
                });
        });
    }
}
