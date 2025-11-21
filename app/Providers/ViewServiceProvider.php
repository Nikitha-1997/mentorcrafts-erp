<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Service;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
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
         // Share all active services and their customers with all views
        View::composer('*', function ($view) {
            $servicesMenu = Service::with('customers')->where('is_active', 1)->get();
            $view->with('servicesMenu', $servicesMenu);
        });
    }
}
