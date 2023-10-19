<?php

namespace Zubs\Translator\Providers;

use Illuminate\Support\ServiceProvider;

class TranslatorProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
