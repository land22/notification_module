<?php
namespace App\Services;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Repository\UserRepository;
use App\Entity\Notifications;
class Server implements MessageComponentInterface
{
    private $clients;
    private $botName = 'defaultUser';
    private $defaultChannel = 'general';
    private $users = [];
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->clients = new \SplObjectStorage();
        $this->users = [];
        $this->container = $container;
    }
    private function subcribeToChannel(ConnectionInterface $from, $channel, $user){
        $this->users[$from->resourceId]['channels'][$channel] = $channel;
        $this->sendMessageToChannel(
            $from,
            $channel,
            $this->botName,
            $user.'Joind #'.$channel
        );
    }
    private function unsubcribeFromChannel(ConnectionInterface $from, $channel, $user){
        if (\array_key_exists($channel, $this->clients[$from->resourceId]['channels'])){
            unset($this->clients[$from->resourceId]['channels']);
        }
        $this->sendMessageToChannel(
            $from,
            $channel,
            $this->botName,
            $user.'left #'.$channel
        );
    }
    private function sendMessageToChannel(ConnectionInterface $from, $channel, $user, $message){
        if (!isset($this->clients[$from->resourceId]['channels'][$channel])){
            return false;
        }
        foreach ($this->clients as $connectionId => $userConnection){
            if (\array_key_exists($channel, $userConnection['channels'])){
                $userConnection['connection']->send(json_encode([
                    'action'=>'message',
                    'channel'=>$channel,
                    'user'=>$user,
                    'message'=>$message
                ]));
            }
            return true;
        }
    }
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = [
            'connection' => $conn,
            'user' => '',
            'channels' => [],
        ];
        $conn->send(json_encode([
            'action' => 'message',
            'channel' => $this->defaultChannel,
            'user' => $this->botName,
            'message' => sprintf('Connection établie  #%d!', $conn->resourceId),
            'messageClass' => 'success',
        ]));
    }
    public function onClose(ConnectionInterface $closedConnection)
    {
        $this->sendMessageToChannel(
            $closedConnection,
            $this->defaultChannel,
            $this->botName,
            $this->users[$closedConnection->resourceId]['user'].'a été déconnecté !!!'
        );
        unset($this->clients[$closedConnection->resourceId]);
        echo sprintf('connection #%d a été déconnecté\n', $closedConnection->resourceId);
    }
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send(json_encode([
            'action' => 'message',
            'channel' => $this->defaultChannel,
            'user' => $this->botName,
            'message' => 'Une erreur a été trouvé : '.$e->getMessage(),
        ]));
        $conn->close();
    }
    public function onMessage(ConnectionInterface $from, $message)
    {
        $messageData = json_encode($message);
        if ($messageData === null){
            return false;
        }
        if (empty($this->users[$from->resourceId]['user']) && $messageData->user) {
            $this->users[$from->resourceId]['user'] = $messageData->user;
        }
        $action = $messageData->action ?? 'inconnue';
        $channel = $messageData->channel ?? $this->defaultChannel;
        $user = $messageData->user ?? $this->botName;
        $message = $messageData->message ?? '';
        $jwt_manager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        $token = new JWTUserToken();
        $token->setRawToken($user);
        $payload = $jwt_manager->decode($token);
        $em = $this->container->get('doctrine')->getManager();
        $notification = new Notifications();
        $notification->setCreatedAt( new \Datime());
        $notification->setContenu($message);
        $notification->setEmail($payload['username']);
        $notification->setToken($token);
        $em->persist($notification);
        $em->flush();
        switch ($action) {
            case 'subcribe':
                $this->subcribeToChannel($from, $channel, $payload['username']);
                return true;
            case 'unsubcribe':
                $this->unsubcribeFromChannel($from, $channel, $payload['username']);
                return true;
            case 'message':
                return $this->sendMessageToChannel($from, $channel, $payload['username'], $message);
            default:
                echo sprintf('Cette Action "%s" n\'est pas encore prise en compte !', $action);
                break;
        }
        return false;
    }

}