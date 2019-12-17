<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->isLocal()){
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');
        \View::composer('*',function ($view){
            $channels = \Cache::rememberForever('channels',function (){
                return \App\Models\Channel::all();
            });
            $view->with('channels',$channels);
        });
       // \View::share('channels',\App\Models\Channel::all());
        \Validator::extend('spamfree','App\Rules\SpamFree@passes');
    }
}
