<?php

namespace Lux\Commands;

use
    Lux\Traits\DirectoryTrait,
    Lux\Traits\QuestionTrait,
    Lux\Traits\MessageTrait;

use
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument;

class DownloadVideoCommand extends Command
{
    use
        QuestionTrait,
        MessageTrait,
        DirectoryTrait;

    protected static $defaultName = 'video:down';
    protected static $defaultDescription = 'Baixa vídeos em vários protocolos.';

    protected function configure()
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'Url do vídeo')
            ->addOption('dir', 'd', InputArgument::OPTIONAL, 'Diretório de destino')
            ->addOption('filename', 'f', InputArgument::OPTIONAL, 'Nome do arquivo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = '';
        $separator = DIRECTORY_SEPARATOR;
        $url = $input->getArgument('url');

        if (!$input->hasParameterOption(['--dir', '-d'], true))
            $dir = trim($this->questionPath('Onde deseja salvar o vídeo? ', $input, $output));
        else
            $dir = $input->getOption('dir');

        if (!$input->hasParameterOption(['--filename', '-f'], true))
            $filename = trim($this->question('Qual o nome do arquivo? ', $input, $output));
        else
            $filename = $input->getOption('filename');

        
        $dir = $this->homeDirFullPath($dir);
        
        $this->questionDir($dir, $input, $output);

        if (!is_dir($dir)) {
            $output->writeln($this->error('Diretório inválido'));
            return Command::INVALID;
        }

        $output->writeln($this->info('Baixando vídeo...'));

        $destination = "{$dir}{$separator}{$filename}.mp4";

        `ffmpeg -protocol_whitelist file,tls,http,https,tcp,crypto,ftp -i '$url' -c copy -bsf:a aac_adtstoasc '$destination' -hide_banner`;

        $output->writeln($this->success('Concluído!'));

        return Command::SUCCESS;
    }
}
