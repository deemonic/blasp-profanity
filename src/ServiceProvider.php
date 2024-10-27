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
                __DIR__ . '/../config/en/config.php' => config_path('en/blasp.php'),
                __DIR__ . '/../config/config_fr.php' => config_path('fr/blasp.php'),
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
        $this->mergeConfigFrom(__DIR__ . '/../config/en/config.php', 'en/blasp');
        $this->mergeConfigFrom(__DIR__ . '/../config/fr/config.php', 'fr/blasp');

        $this->app->bind('blasp', function() {
            return new BlaspService();
        });
    }
}
