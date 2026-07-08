<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\SiteSetting;
use App\Services\CartService;
use Illuminate\Support\Facades\View;
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
        View::composer('frontend.*', function ($view) {
            $view->with([
                'cartSummary' => app(CartService::class)->summary(),
                'siteSettings' => $this->siteSettings(),
            ]);
        });

        View::composer('backend.layouts.master', function ($view) {
            $view->with([
                'orderNotifications' => Order::latest()->take(50)->get(),
                'orderNotificationCount' => Order::count(),
                'siteSettings' => $this->siteSettings(),
            ]);
        });
    }

    private function siteSettings(): array
    {
        return array_merge([
            'header_logo' => 'frontend/assets/images/logo/logo-transperent.png',
            'footer_logo' => 'frontend/assets/images/logo/logo.png',
            'company_whatsapp_number' => '',
        ], SiteSetting::allKeyed());
    }
}
