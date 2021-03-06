<?php

namespace Integration\TestCase;

use Orchestra\Model\User;
use Orchestra\Installation\Installation;
use Orchestra\Testing\TestCase as ApplicationTestCase;

abstract class TestCase extends ApplicationTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->make('config')->set(['auth.providers.users.model' => User::class]);
    }

    /**
     * Resolve application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        $app = parent::resolveApplication();

        $app->useVendorPath(__DIR__.'/../../vendor');

        return $app;
    }

    /**
     * Install Orchestra Platform and get the administrator user.
     *
     * @return \Orchestra\Model\User
     */
    protected function createAdminUser(Installation $installer = null)
    {
        $this->withFactories(__DIR__.'/../factories');

        if (is_null($installer)) {
            $installer = $this->makeInstaller();
        }

        $user = factory(User::class, 'admin')->create();

        $installer->create($user, [
            'site_name' => 'Orchestra Platform',
            'email'     => 'hello@orchestraplatform.com',
        ]);

        $this->artisan('migrate');

        $this->app['orchestra.installed'] = true;

        $this->beforeApplicationDestroyed(function () {
            $this->app['orchestra.installed'] = false;
        });

        return $user;
    }
}
