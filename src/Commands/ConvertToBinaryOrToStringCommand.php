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
    protected static $defaultDescription = 'Converte string para binário ou binário para string.';

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
        $binary = "";
        $word = "";

        if ($input->hasParameterOption(['--bin', '-b'], true)) {
            for ($i = 0; $i < strlen($to_convert); $i++) {
                $ascii = ord($to_convert[$i]);
                $binary .= decbin($ascii);
            }

            $output->writeln('Em binário: ');
            $output->writeln($this->info($binary));
            return self::SUCCESS;
        }

        if ($input->hasParameterOption(['--str', '-s'], true)) {
            for ($i = 0; $i < strlen($to_convert); $i+=8) {
                $bin = substr($to_convert, $i, 8);
                $decimal = bindec($bin);
                $word .= sprintf("%c", $decimal);
            }

            $output->writeln('Em letra: ');
            $output->writeln($this->info($word));
            
            return self::SUCCESS;
        }
    }
}
