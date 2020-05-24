<?php
namespace App\Services;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
class Server implements MessageComponentInterface
{
    private $clients;
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $conn->send(sprintf('New connection: Hello #%d', $conn->resourceId));
    }
    public function onClose(ConnectionInterface $closedConnection)
    {
        $this->clients->detach($closedConnection);
        echo sprintf('connection #%d a été déconnecté\n', $closedConnection->resourceId);
    }
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('Une erreur trouvée:'.$e->getMessage());
        $conn->close();
    }
    public function onMessage(ConnectionInterface $from, $message)
    {
        $totalClients = count($this->clients) - 1;
        echo vsprintf(
            'connnection #%1$d envoi de message "%2$s" à %3$d autre connection%4$S'."\n",[
                $from->resourceId,
                $message,
                $totalClients,
                $totalClients === 1 ? '' : 's'
            ]);
        foreach ($this->clients as $client) {
            if($from !== $client){
                $client->send($message);
            }
        }
    }
}