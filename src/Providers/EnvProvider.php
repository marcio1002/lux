<?php

namespace Lux\Providers;

use Dotenv\Dotenv;

class EnvProvider
{

    public function boot($app)
    {
        Dotenv::createMutable(dirname(__DIR__, 2))->load();
    }

    public function register($app)
    {
        $system = [
            'linux' => 'echo "/home/$USER"',
            'windows' => 'powershell.exe Get-Location',
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