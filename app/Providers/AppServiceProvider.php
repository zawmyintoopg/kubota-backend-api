<?php

namespace App\Providers;

use App\Models\Shift;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
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
        Paginator::useBootstrapFive();
        if(session()->has('locale')){
            app()->setLocale(session('locale'));
        }
         View::composer('*', function ($view) {
                $view->with(
                    'currentShift',
                    Auth::check()
                        ? Shift::where('user_id', Auth::id())
                            ->where('status', 'open')
                            ->first()
                        : null
                );
            });

    }
}
