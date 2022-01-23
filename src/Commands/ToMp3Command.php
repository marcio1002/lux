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

class ToMp3Command extends Command
{
    use 
        QuestionTrait,
        MessageTrait;

    protected static $defaultName = 'to:mp3';
    protected static $defaultDescription = 'Extrai audio de um vídeo.';

    public function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Arquivo de entrada')
            ->addOption('dir', 'd', InputArgument::OPTIONAL, 'Diretório de destino')
            ->addOption('filename', 'f', InputArgument::OPTIONAL, 'Nome do arquivo de saída');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $filename = $input->getOption('filename') ?? pathinfo($file, PATHINFO_FILENAME);
        $dir = '';

        if($input->hasParameterOption(['--dir', '-d'], true)) {
            $dir = $input->getOption('dir');
        } else {
            $dir = trim($this->questionPath('Especifique o caminho de destino ? ', $input, $output));
        }

        $destination = "$dir/$filename.mp3";

        $output->writeln($this->info('Extraindo audio...'));

        `ffmpeg -i '$file' -vn -acodec libmp3lame -ab 128k '$destination'`;
        
        $output->writeln($this->success('Concluído!'));
        return Command::SUCCESS;
    }
}