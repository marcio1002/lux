<?php

namespace Lux\Providers;

use
    Lux\Providers\Provider,
    Dotenv\Dotenv;

class EnvProvider extends Provider
{
    public function register()
    {
        $system = [
            'linux' => 'echo "/home/$USER"',
            'windows' => 'powershell.exe Get-Location ~',
            'mac' => 'echo "/home/$USER"'
        ];

        $os = strtolower(PHP_OS);

        if (array_key_exists($os, $system)) {
            $command = $system[$os];
        } else {
            $command = 'pwd ~';
        }

        if (isset($command))
            $userPath = trim(shell_exec($command));

        $_ENV['PATH_USER'] = $userPath;
    }

    public function boot()
    {
        Dotenv::createMutable(dirname(__DIR__, 2))->load();
    }
}