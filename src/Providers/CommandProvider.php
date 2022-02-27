<?php

namespace Lux\Providers;

use Lux\Providers\Provider,
    Dotenv\Dotenv,
    Symfony\Component\Finder\Finder;


class CommandProvider extends Provider
{

    public function boot()
    {
        Dotenv::createMutable(dirname(__DIR__, 2))->load();

        $this->app->instance(new Finder, function (Finder $fs) {
            /**
             * @var \Lux\Providers\Provider $app 
             */
            return $fs->in(dirname(__DIR__, 2) . '/');
        });
    }
}
