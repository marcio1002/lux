<?php

namespace Lux\Commands;

use Lux\Traits\MessageTrait;

use
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use
    Psr\Http\Message\ServerRequestInterface,
    React\Http\HttpServer,
    React\Http\Message\Response,
    React\Socket\SocketServer;

class ServeCommand extends Command
{
    use MessageTrait;

    protected static $defaultName = 'serve';
    protected static $defaultDescription = 'Inicia uma conexão de um servidor.';

    protected function configure()
    {
        $this
            ->addArgument('dir', InputArgument::REQUIRED, 'Diretório a ser servido')
            ->addOption('port', 'p', InputArgument::OPTIONAL, 'Porta do servidor')
            ->addOption('host', 'ht', InputArgument::OPTIONAL, 'Host do servidor');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        $host = $input->getOption('host') ?? '127.0.0.1';
        $port = $input->getOption('port') ?? 8080;

        $http = new HttpServer(
            fn (ServerRequestInterface $_) => Response::html(file_get_contents($dir))
        );

        $socket = new SocketServer("$host:$port");

        $http->listen($socket);

        $output->writeln($this->success('Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress())));

        return Command::SUCCESS;
    }
}