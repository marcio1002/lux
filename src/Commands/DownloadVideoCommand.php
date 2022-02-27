<?php

namespace Lux\Commands;

use
    Lux\Traits\QuestionTrait,
    Lux\Traits\MessageTrait,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument;

class DownloadVideoCommand extends Command
{
    use 
        QuestionTrait,
        MessageTrait;

    protected static $defaultName = 'video:down';
    protected static $defaultDescription = 'Baixa vídeos em HLS.';

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'Url do vídeo')
            ->addOption('dir', 'd', InputArgument::OPTIONAL, 'Diretório de destino')
            ->addOption('filename', 'f', InputArgument::OPTIONAL, 'Nome do arquivo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = '';
        if (!$input->hasParameterOption(['--dir', '-d'], true))
            $dir = trim($this->questionPath('Onde deseja salvar o vídeo? ', $input, $output));
        else
            $dir = $input->getOption('dir');

        if (!$input->hasParameterOption(['--filename', '-f'], true))
            $filename = trim($this->question('Qual o nome do arquivo? ', $input, $output));
        else
            $filename = $input->getOption('filename');

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

        $url = $input->getArgument('url');

        $output->writeln($this->info('Baixando vídeo...'));

        $destination = $dir . DIRECTORY_SEPARATOR . "$filename.mp4";

        `ffmpeg -protocol_whitelist file,tls,http,https,tcp -i '$url' -c copy -bsf:a aac_adtstoasc '$destination' -hide_banner`;

        $output->writeln($this->success('Concluído!'));

        return Command::SUCCESS;
    }
}
