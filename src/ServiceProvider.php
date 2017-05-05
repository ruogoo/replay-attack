<?php
/**
 * This file is part of ruogoo.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */

namespace Ruogoo\ReplayAttack;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Ruogoo\ReplayAttack\Middleware\ReplayAttack;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register()
    {
        $this->app['router']->aliasMiddleware('replay-attack', ReplayAttack::class);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/replay_attack.php' => config_path('replay_attack.php'),
            ], 'config');

            $this->mergeConfigFrom(__DIR__ . '/../config/replay_attack.php', 'replay_attack');
        }
    }
}
