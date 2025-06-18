<?php

namespace App\Providers;

use App\Models\Faktur;
use App\Observers\FakturObserver;
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
    public function boot(): void
    {
        // Faktur::observe(FakturObserver::class);
    }
}
