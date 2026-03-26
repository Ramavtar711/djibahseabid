<?php

namespace App\Providers;

use App\Models\Lot;
use App\Services\SettlementLifecycleService;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap();

        if (app()->runningInConsole()) {
            return;
        }

        Lot::query()
            ->where('status', 'scheduled auction')
            ->whereNotNull('auction_start_at')
            ->where('auction_start_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('auction_end_at')
                    ->orWhere('auction_end_at', '>', now());
            })
            ->update([
                'status' => 'active',
            ]);

        app(SettlementLifecycleService::class)->processExpiredSettlements();
    }
}


