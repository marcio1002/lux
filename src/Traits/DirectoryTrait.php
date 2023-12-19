<?php

namespace Lux\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait DirectoryTrait
{
    public function homeDirFullPath(string $dir): string
    {
        return preg_replace('/^(~)/', $_ENV['PATH_USER'], $dir);
    }

    public function questionDir(string $dir, InputInterface $input, OutputInterface $output)
    {
        if (
            !is_dir($dir) &&
            strtolower(
                $this->question(
                    message: "Diretório <fg=yellow>$dir</> não existe deseja cria-lo? [S/N] ",
                    input: $input,
                    output: $output
                )
            ) === 's'
        ) {
            shell_exec("mkdir -p $dir");
        }
    }
}
