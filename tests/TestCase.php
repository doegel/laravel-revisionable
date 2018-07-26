<?php

namespace Revisionable\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Jenssegers\Mongodb\MongodbServiceProvider::class,
            \Revisionable\RevisionableServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $database = require __DIR__ . '/config/database.php';

        $app['config']->set('database.default', 'mongodb');
        $app['config']->set('database.connections.mongodb', $database['connections']['mongodb']);

        $revisionable = require __DIR__ . '/config/revisionable.php';

        $app['config']->set('revisionable.models', $revisionable['models']);
    }
}