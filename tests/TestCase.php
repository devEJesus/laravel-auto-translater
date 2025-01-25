<?php

namespace Tests;

use LaravelAutoTranslator\Providers\LaravelAutoTranslatorProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelAutoTranslatorProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Set up any environment configuration
    }
}
