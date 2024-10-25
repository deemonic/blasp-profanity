<?php

namespace Blaspsoft\Blasp;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('blasp.php'),
                __DIR__ . '/../config/config_en.php' => config_path('blasp_en.php'),
                __DIR__ . '/../config/config_fr.php' => config_path('blasp_fr.php'),
            ], 'blasp-config');
        }

        app('validator')->extend('blasp_check', function($attribute, $value, $parameters, $validator) {
            $language = $parameters[0] ?? null;

            $blaspService = new BlaspService($language);

            return !$blaspService->check($value)->hasProfanity();
        }, 'The :attribute contains profanity.');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'blasp');
        $this->mergeConfigFrom(__DIR__ . '/../config/config_en.php', 'blasp_en');
        $this->mergeConfigFrom(__DIR__ . '/../config/config_fr.php', 'blasp_fr');

        $this->app->bind('blasp', function() {
            return new BlaspService();
        });
    }
}
