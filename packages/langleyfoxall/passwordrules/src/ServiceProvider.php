<?php

namespace langleyfoxall\passwordrules;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'passwordrules');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/passwordrules'),
        ]);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
    }
}
