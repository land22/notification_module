<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notifications;
use App\Services\NotificationModule;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(NotificationModule $notificationModule)
    {
     // exemple d'utilisation du module NotificationModule
      $notificationModule->byDb('landrywabo8@gmail.com','Création d\'un compte sur notre application');
      $notificationModule->byMail('landrywabo8@gmail.com','landrywabo8@gmail.com','Création d\'un compte sur notre application');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
