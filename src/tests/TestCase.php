<?php

namespace Dagim\TelebirrApi\Tests;

use Dagim\TelebirrApi\Providers\TelebirrServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * 
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TelebirrServiceProvider::class,
        ];
    }

    /**
     * Define environment setup
     * 
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Set up environment configurations, if any
    }
}
