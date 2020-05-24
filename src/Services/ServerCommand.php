<?php
namespace App\Services;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use App\Services\Server;
class ServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->SetName('Projet:notification:server')
            ->SetDescription('Start notification server');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(
            new HttpServer(new WsServer(new Server())),
            8080,
            '127.0.0.1'
            );
        $server->run();
    }
}