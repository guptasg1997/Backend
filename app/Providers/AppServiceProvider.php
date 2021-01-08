<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);


        //check that app is local////delete if not necessary
        // if ($this->app->isLocal()) {
        //     //if local register your services you require for development
        //     //$this->app->register('Barryvdh\Debugbar\ServiceProvider');
        // } else {
        // //else register your services you require for production
        //     $this->app['request']->server->set('HTTPS', true);
        // }////

    }
}
