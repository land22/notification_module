<?php

namespace App\Services;
use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;


class NotificationModule {

    protected $em;
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->mailer = $mailer;
    }

    public function informer($email,$contenue) {

        $entityManager = $this->em;
        $notification = new Notifications();
        $date = new \DateTime();
        $notification->setEmail($email);
        $notification->setContenu($contenue);
        $notification->setEtat('true');
        $notification->setCreatedAt($date);
        $entityManager->persist($notification);
        $entityManager->flush();

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