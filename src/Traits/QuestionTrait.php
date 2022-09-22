<?php

namespace Lux\Traits;

use
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Question\Question;

trait QuestionTrait
{
    protected function question(string $message, InputInterface $input, OutputInterface $output, array $options = []): string
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

    protected function questionPath(string $message, InputInterface $input, OutputInterface $output): string
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
}