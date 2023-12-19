<?php

namespace Lux\Commands;

use Lux\Traits\DirectoryTrait;
use Lux\Traits\MessageTrait;
use Lux\Traits\QuestionTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ToImageCommand extends Command
{
    use
        QuestionTrait,
        MessageTrait,
        DirectoryTrait;

    protected static $defaultName = 'to:img';
    protected static $defaultDescription = 'Extrai a imagem de um vídeo';

    public function configure()
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Vídeo para extrair a imagem')
            ->addOption('dir', 'd', InputArgument::OPTIONAL, 'Diretório de destino')
            ->addOption('filename', 'f', InputArgument::OPTIONAL, 'Nome do arquivo de saída')
            ->addOption(
                'time',
                't',
                InputArgument::OPTIONAL,
                'O tempo no formato 00:00:00 para captura da imagem. Esse argumento é obrigatório na ausência do argumento frames'
            )
            ->addOption(
                'frames',
                'r',
                InputArgument::OPTIONAL,
                'Taxa frames ou quantidade por segundos. exemplo 1/1 irá extrair a cada 1 segundo e 1/10 irá extrair a cada 10 segundos'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $separator = DIRECTORY_SEPARATOR;
        $file = $input->getArgument('file');
        $filename = $input->getOption('filename') ?? pathinfo($file, PATHINFO_FILENAME);
        $dir = '';

        $is_does_option = !$input->hasParameterOption(['--frames', '-r'], true) &&
            !$input->hasParameterOption(['--time', '-t'], true);

        if ($is_does_option) {
            $output->writeln(
                $this->error('Você precisa especificar pelo menos uma opção para extrair a imagem')
            );

            return Command::FAILURE;
        }

        if ($input->hasParameterOption(['--dir', '-d'], true)) {
            $dir = $input->getOption('dir');
        } else {
            $dir = trim($this->questionPath('nde deseja salvar a imagem(ns) ?', $input, $output));
        }

        $dir = $this->homeDirFullPath($dir);

        $this->questionDir($dir, $input, $output);

        if (!is_dir($dir)) {
            $output->writeln($this->error('Diretório inválido'));
            return Command::INVALID;
        }

        $destination = "{$dir}{$separator}{$filename}";

        $output->writeln($this->info('Extraindo imagem...'));

        if ($input->hasParameterOption(['--frames', '-r'], true)) {
            $frames = $input->getOption('frames');

            `ffmpeg -i '$file' -r $frames -q:v 1 '{$destination}%03d.png'`;
        } else {
            $time = $input->getOption('time');
            `ffmpeg -i '$file' -ss $time -vframes 1 -q:v 1 '{$destination}.png'`;
        }

        $output->writeln($this->success('Imagem extraída!'));

        return Command::SUCCESS;
    }
}
