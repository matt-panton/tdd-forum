<?php

namespace App\Providers;

use App\Channel;
use App\Repositories\Trending;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer('thread._trending', function ($view) {
            $view->with([
                'trendingThreads' => resolve(Trending::class)->get()
            ]);
        });

        View::composer('*', function ($view) {
            $channels = Cache::rememberForever('channels.all', function () {
                return Channel::all();
            });
            
            $view->with(compact('channels'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
