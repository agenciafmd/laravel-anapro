<?php

namespace Agenciafmd\Anapro\Providers;

use Illuminate\Support\ServiceProvider;

class AnaproServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 
    }

    public function register()
    {
        $this->loadConfigs();
    }

    protected function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-anapro.php', 'laravel-anapro');
    }
}
