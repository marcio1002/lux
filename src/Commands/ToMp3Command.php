<?php

namespace Lux\Commands;


use
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Question\Question;

class ToMp3Command extends Command
{
    protected static $defaultName = 'to:mp3';
    protected static $defaultDescription = 'Extrai audio de im vídeo.';

    public function configure(): void
    {
        $this
            ->addArgument('video', InputArgument::REQUIRED, 'Arquivo de vídeo')
            ->addOption('dir', 'd', InputArgument::OPTIONAL, 'Diretório de destino');
    }

    private function question(string $message, InputInterface $input, OutputInterface $output, array $options = []): string
    {
        $helper = $this->getHelper('question');
        $question = new Question("<info>$message</info>\n>");


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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}