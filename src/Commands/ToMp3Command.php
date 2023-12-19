<?php

namespace Lux\Commands;

use Lux\Traits\DirectoryTrait;
use
    Lux\Traits\QuestionTrait,
    Lux\Traits\MessageTrait;

use
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument;

class ToMp3Command extends Command
{
    use
        QuestionTrait,
        MessageTrait,
        DirectoryTrait;

    protected static $defaultName = 'to:mp3';
    protected static $defaultDescription = 'Extrai audio de um vídeo.';

    public function configure()
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Vídeo a ser convertido')
            ->addOption('dir', 'd', InputArgument::OPTIONAL, 'Diretório de destino')
            ->addOption('filename', 'f', InputArgument::OPTIONAL, 'Nome do arquivo de saída');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $separator = DIRECTORY_SEPARATOR;
        $file = $input->getArgument('file');
        $filename = $input->getOption('filename') ?? pathinfo($file, PATHINFO_FILENAME);
        $dir = '';

        if ($input->hasParameterOption(['--dir', '-d'], true)) {
            $dir = $input->getOption('dir');
        } else {
            $dir = trim($this->questionPath('Onde deseja salvar audio ?', $input, $output));
        }

        $dir = $this->homeDirFullPath($dir);

        $this->questionDir($dir, $input, $output);

        if (!is_dir($dir)) {
            $output->writeln($this->error('Diretório inválido'));
            return Command::INVALID;
        }

        $destination = "{$dir}{$separator}{$filename}.mp3";

        $output->writeln($this->info('Extraindo audio...'));

        `ffmpeg -i '$file' -vn -acodec libmp3lame -ab 128k '$destination'`;

        $output->writeln($this->success('Concluído!'));

        return Command::SUCCESS;
    }
}
