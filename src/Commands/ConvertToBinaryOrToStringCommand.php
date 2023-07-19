<?php

namespace Lux\Commands;

use Lux\Traits\MessageTrait;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertToBinaryOrToStringCommand extends Command
{
    use MessageTrait;

    protected static $defaultName = 'to:bin';
    protected static $defaultDescription = 'Baixa vídeos em vários protocolos.';

    protected function configure()
    {
        $this->addArgument('to_convert', InputArgument::REQUIRED, 'O binário ou string a ser convertido no tipo especificado');

        $this->addOption('bin', 'b', InputOption::VALUE_OPTIONAL, 'Converter a entrada em binário', true);
        $this->addOption('str', 's', InputOption::VALUE_OPTIONAL, 'Converter a entrada em string', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (
            !$input->hasParameterOption(['--bin', '-b']) &&
            !$input->hasParameterOption(['--str', '-s'])
        ) {
            $output->writeln($this->error('Você precisa especificar pelo menos uma opção para converter'));
            return self::FAILURE;
        }

        $to_convert = $input->getArgument('to_convert');

        if ($input->hasParameterOption(['--bin', '-b'])) {
            $to_bin = decbin($to_convert);

            $output->writeln('Em binário: ');
            $output->writeln($this->info($to_bin));
        }

        if ($input->hasParameterOption(['--str', '-s'])) {
            $to_str = bindec($to_convert);

            $output->writeln('Em letra: ');
            $output->writeln($this->info($to_str));
        }

        return self::SUCCESS;
    }
}