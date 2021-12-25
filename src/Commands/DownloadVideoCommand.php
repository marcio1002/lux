<?php

namespace Lux\Commands;

use
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Question\Question,
    Symfony\Component\Console\Style\SymfonyStyle;

class DownloadVideoCommand extends Command
{

    protected static $defaultName = 'down:video';
    protected static $defaultDescription = 'Baixa vídeos através do HLS.';

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'Url do vídeo')
            ->addOption('dir', 'd', InputArgument::OPTIONAL, 'Diretório de destino')
            ->addOption('filename', 'f', InputArgument::OPTIONAL, 'Nome do arquivo');
    }

    private function question(string $message, InputInterface $input, OutputInterface $output, array $options = []): string
    {
        $helper = $this->getHelper('question');
        $question = new Question("<info>$message</info>\n> ");


        foreach (array_keys($options) as $key) {
            switch ($key) {
                case 'validator':
                    $question->setValidator($options['validator']);
                    break;

                case 'autocomplete_callback':
                    $question->setAutocompleterCallback($options['autocomplete_callback']);
                    break;

                case 'autocomplete_values':
                    $question->setAutocompleterValues($options['autocomplete_values']);
                    break;
            }
        }


        return $helper->ask($input, $output, $question);
    }

    private function questionPath(string $message, InputInterface $input, OutputInterface $output): string
    {
        $autoCompletePath = function (string $path) {
            try {
                $separator = DIRECTORY_SEPARATOR;

                $path = empty(trim($path)) ?  $_ENV['PATH_USER'] : $path;

                if (preg_match('/^(~)/', $path, $match)) $path = preg_replace('/^(~)/', $_ENV['PATH_USER'], $path);

                $path = substr($path, strlen($path) - 1, strlen($path)) == $separator  ? $path : trim($path) . $separator;

                if (is_dir($path)) {
                    $results = array_map(fn ($dirOrFile) => "${path}${dirOrFile}", scandir($path));
                    return [...array_filter($results, fn ($dirOrFile) => !preg_match("/[\w\d]+\\$separator\.{1,2}$/", $dirOrFile) && is_dir($dirOrFile))];
                }

                return [];
            } catch (\Exception | \Throwable $e) {
                return [];
            }
        };

        return $this->question($message, $input, $output, ['autocomplete_callback' => $autoCompletePath]);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = '';

        $io = new SymfonyStyle($input, $output);

        if (!$input->hasParameterOption(['--dir', '-d'], true))
            $dir = trim($this->questionPath('Onde deseja salvar o vídeo? ', $input, $output));
        else
            $dir = $input->getOption('dir');

        if (!$input->hasParameterOption(['--filename', '-f'], true)) 
            $filename = trim($this->question('Qual o nome do arquivo? ', $input, $output));
        else
            $filename = $input->getOption('filename');

        if (preg_match('/^(~)/', $dir, $match)) $dir = preg_replace('/^(~)/', $_ENV['PATH_USER'], $dir);

        if (!is_dir($dir) && ($createDir = $io->confirm("Diretório \033[00;33;1m$dir\033[m não existe deseja cria-lo?"))) {
            shell_exec("mkdir -p $dir");
        }

        if (!is_dir($dir)) {
            $io->error("Diretório inválido");
            return Command::INVALID;
        }

        $url = $input->getArgument('url');

        $output->writeln('<fg=#fff;bg=#3B26A9;options=bold> Baixando vídeo </>');

        $destination = $dir . DIRECTORY_SEPARATOR . "$filename.mp4";


        $res = system("ffmpeg -protocol_whitelist file,tls,http,https,tcp -i '$url' -c copy -bsf:a aac_adtstoasc $destination");
        $output->writeln("<fg=#080808;bg=#10F34A;options=bold> Concluído </>");

        return Command::SUCCESS;
    }
}
