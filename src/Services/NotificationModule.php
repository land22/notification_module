<?php

namespace App\Services;
use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class NotificationModule {

    protected $em;
    private $mailer;
    protected $bus;

    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $entityManager, MessageBusInterface $bus )
    {
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->bus = $bus;
    }

    public function informer($contenue) {

    $update = new Update('http://localhost:8000/',json_encode([
        'message' => $contenue,
    ]))?:String;
        $this->bus->dispatch($update);

    }

    public function byMail($from, $email,$contenue) {
        $message = new \Swift_Message('Notifications !!!');
        $message->setFrom($from);
        $message->setTo($email);
        $message->setSubject('Votre informations');
        $message->setBody($contenue, 'text/html');
        $this->mailer->send($message);


    }


}