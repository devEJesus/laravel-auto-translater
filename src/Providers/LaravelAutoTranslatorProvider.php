<?php

namespace LaravelAutoTranslator\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelAutoTranslator\Console\Commands\TranslatorCommand;
use LaravelAutoTranslator\Core\Translator;
use LaravelAutoTranslator\Storage\LangDirectory;

class LaravelAutoTranslatorProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            TranslatorCommand::class,
        ]);

        $this->app->bind(LangDirectory::class, Translator::class);
    }

    public function boot(): void {}
}
