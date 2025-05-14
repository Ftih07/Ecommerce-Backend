<?php

namespace App\Providers;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

class SecurityServiceProvider extends ServiceProvider
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
    public function boot(Kernel $kernel): void
    {
        $kernel->pushMiddleware(SecurityHeaders::class);
    }
}
