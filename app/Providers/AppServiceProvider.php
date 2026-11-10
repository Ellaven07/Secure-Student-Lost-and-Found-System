<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        View::composer(
            ['layouts.app', 'layouts.student'],
            function ($view) {
                $unreadNotificationCount = 0;

                if (auth()->check()) {
                    $unreadNotificationCount = DB::table('notifications')
                        ->where('user_id', auth()->id())
                        ->whereNull('read_at')
                        ->count();
                }

                $view->with(
                    'unreadNotificationCount',
                    $unreadNotificationCount
                );
            }
        );
    }
}
