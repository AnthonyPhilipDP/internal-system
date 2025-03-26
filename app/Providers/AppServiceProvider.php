<?php

namespace App\Providers;

use Illuminate\Contracts\Http\Kernel;
use App\Http\Middleware\AppServiceBoot;
use Illuminate\Support\ServiceProvider;

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
    public function boot(Kernel $kernel): void
    {
        $kernel->pushMiddleware(AppServiceBoot::class);
    }
}
