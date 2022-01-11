<?php

namespace Lux\Providers;

use 
    Lux\Providers\Provider,
    Dotenv\Dotenv,
    Symfony\Component\Finder\Finder;

class EnvProvider extends Provider
{

    public function __construct( Finder $fs, int $priority = 0)
    {
        
    }

    public function boot()
    {
        Dotenv::createMutable(dirname(__DIR__, 2))->load();
        
        $this->instance(new Finder, function(Finder $fs, $app) {
            /**
             * @var \Lux\Providers\Provider $app 
             */
            return $fs->in(__DIR__ . '/');
        });
    }

    public function register()
    {
        $system = [
            'linux' => 'echo "/home/$USER"',
            'windows' => 'powershell.exe Get-Location ~',
            'mac' => 'echo "/home/$USER"'
        ];

        $os = strtolower(PHP_OS);

        if(isset($system[$os])) {
            $command = $system[$os];
        } else {
            $command = 'pwd ~';
        }

        $userPath = trim(shell_exec($command));

        $_ENV['PATH_USER'] = $userPath;
    }
}