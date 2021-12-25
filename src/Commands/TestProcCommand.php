<?php

namespace Lux\Commands;


use
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Helper\ProgressBar;

class TestProcCommand extends Command
{
    protected static $defaultName = 'down:test';
    protected static $defaultDescription = 'Baixa vídeos através do HLS.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /* $command = 'php /home/marcio-zorion/Documentos/scripts/phppatterns/src/Behavioral/Observer/index.php';
        $descriptor = [STDIN, STDOUT, STDERR];

        $process = proc_open($command, $descriptor, $pipes); */


        $process = new ProgressBar($output, 100);
    }
}