<?php

namespace Rymanalu\DuskForSentinel;

use Laravel\Dusk\Console\InstallCommand;
use Laravel\Dusk\Console\DuskCommand;
use Laravel\Dusk\Console\DuskFailsCommand;
use Laravel\Dusk\Console\MakeCommand;
use Laravel\Dusk\Console\PageCommand;
use Laravel\Dusk\Console\PurgeCommand;
use Laravel\Dusk\Console\ComponentCommand;
use Laravel\Dusk\Console\ChromeDriverCommand;
use Illuminate\Support\Facades\Route;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\ServiceProvider;

class DuskForSentinelServiceProvider extends DuskServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->environment('production')) {
            Route::group(array_filter([
                'prefix' => config('dusk.path', '_dusk'),
                'domain' => config('dusk.domain', null),
                'middleware' => config('dusk.middleware', 'web'),
            ]), function () {
                Route::get('/login/{userId}/{guard?}', [
                    'uses' => 'Rymanalu\DuskForSentinel\Http\Controllers\UserController@login',
                    'as' => 'dusk.login',
                ]);

                Route::get('/logout/{guard?}', [
                    'uses' => 'Rymanalu\DuskForSentinel\Http\Controllers\UserController@logout',
                    'as' => 'dusk.logout',
                ]);

                Route::get('/user/{guard?}', [
                    'uses' => 'Rymanalu\DuskForSentinel\Http\Controllers\UserController@user',
                    'as' => 'dusk.user',
                ]);
            });
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                DuskCommand::class,
                DuskFailsCommand::class,
                MakeCommand::class,
                PageCommand::class,
                PurgeCommand::class,
                ComponentCommand::class,
                ChromeDriverCommand::class,
            ]);
        }
    }
}
