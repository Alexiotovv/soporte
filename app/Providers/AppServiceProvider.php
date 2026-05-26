<?php

namespace App\Providers;

use App\Models\BrandingSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        $brandingSetting = null;

        if (Schema::hasTable('branding_settings')) {
            $brandingSetting = BrandingSetting::query()->first();
        }

        View::share('brandingSetting', $brandingSetting);
    }


}
