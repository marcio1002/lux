<?php

namespace Lux\Providers;

use 
    Lux\Providers\Provicer,
    Symfony\Component\Console\Application;

class AppProvider extends Provider
{
  private Application $app;

  public function __construct(Application $app)
  {
    $this->__init();
    $this->initProviders();
    $this->initCommands($app);
    $this->app = $app;
  }

    public function boot()
    {
      $this->app->run();
    }
}