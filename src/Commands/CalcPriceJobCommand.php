<?php

namespace Lux\Commands;

use Lux\Traits\MessageTrait;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CalcPriceJobCommand extends Command
{
    use MessageTrait;

    protected static $defaultName = 'job:calc';
    protected static $defaultDescription = 'Calcula o valor de horas trabalhada.';

    protected function configure()
    {
        $this->addArgument('duration', InputArgument::REQUIRED, 'duração de horas trabalhada');
        $this->addArgument('per_hour', InputArgument::OPTIONAL, 'O valor a ganhar por hora');
        $this->addArgument('assistance', InputArgument::OPTIONAL, 'O valor do auxílio');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $duration = $input->getArgument('duration');
        $per_hour = $input->getArgument('per_hour') ?? 18;
        $assistance = $input->getArgument('assistance') ?? 150;

        [$hours, $minutes, $seconds] = explode(':', $duration);

        $total_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        $interval = \DateInterval::createFromDateString("$total_seconds seconds");
        $total_hours = $interval->h + ($interval->i / 60) + ($interval->s / 3600);

        $total_value = ($total_hours * $per_hour) + $assistance;

        $output->writeln($this->success('R$ ' . number_format($total_value, 2, ',', '.')));

        return self::SUCCESS;
    }
}
