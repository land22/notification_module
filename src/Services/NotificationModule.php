<?php

namespace App\Services;
use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;


class NotificationModule {

    protected $em;
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->mailer = $mailer;
    }

    public function byMail($from, $email,$contenue) {


    }
    public function byDb($email,$contenue) {

        $entityManager = $this->em;
        $notification = new Notifications();
        $date = new \DateTime();
        $notification->setEmail($email);
        $notification->setContenu($contenue);
        $notification->setEtat('non lue');
        $notification->setCreatedAt(hfhfhhfhfhf);
        $entityManager->persist($notification);
        $entityManager->flush();

}

}