<?php

namespace Lux\Commands;

use
    Lux\Traits\QuestionTrait,
    Lux\Traits\MessageTrait,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Question\Question;

class TitleVideoCommand extends Command
{
    use
        MessageTrait,
        QuestionTrait;

    protected static $defaultName = 'video:title';
    protected static $defaultDescription = 'Altera o título do vídeo.';

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Arquivo de entrada')
            ->addArgument('title', InputArgument::REQUIRED, 'Título do vídeo')
            ->addOption('filename', 'f', InputArgument::OPTIONAL, 'Nome do arquivo de saída')
            ->addOption('destination', 'd', InputArgument::OPTIONAL, 'Diretório de destino')
            ->addOption('type', 't', InputArgument::OPTIONAL, 'Tipo de arquivo de saída');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $title = $input->getArgument('title');
        $type = $input->getOption('type');
        $filename = $input->getOption('filename') ?? $title;

        $dir = '';

        if ($input->hasParameterOption(['--destination', '-d'], true)) {
            $dir = $input->getOption('destination');
        } else {
            $dir = trim($this->questionPath('Especifique o caminho de destino ? ', $input, $output));
        }

        if (preg_match('/^(~)/', $dir, $match)) {
            $dir = preg_replace('/^(~)/', $_ENV['PATH_USER'], $dir);
        }

        if (!is_dir($dir) && strtolower($this->question("Diretório <fg=yellow>$dir</> não existe deseja cria-lo? [S/N] ", $input, $output) == 's')) {
            shell_exec("mkdir -p $dir");
        }

        if (!is_dir($dir)) {
            $output->writeln($this->error('Diretório inválido'));
            return Command::INVALID;
        }



        $output->writeln($this->info('Alterando o título do vídeo...'));

        $type = $type ?? pathinfo($file, PATHINFO_EXTENSION);

        $destination = $dir . DIRECTORY_SEPARATOR . "$filename.$type";

        `ffmpeg -i '$file' -map_metadata -1 -metadata title='$title' -c:v copy -c:a copy '$destination'`;

        $output->writeln($this->success('Concluído'));
        return Command::SUCCESS;
    }
}
