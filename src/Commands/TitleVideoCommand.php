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
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Finder\Finder;

class TitleVideoCommand extends Command
{
    use
        MessageTrait,
        QuestionTrait,
        DirectoryTrait;

    private Finder $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        parent::__construct();
    }

    protected static $defaultName = 'video:title';
    protected static $defaultDescription = 'Altera o título do vídeo.';


    protected function configure()
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

        if ($input->hasParameterOption(['--destination', '-d'], true))
            $dir = $input->getOption('destination');
        else
            $dir = trim($this->questionPath('Onde deseja salvar o vídeo ?', $input, $output));

        $dir = $this->homeDirFullPath($dir);
        $this->questionDir($dir, $input, $output);

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
