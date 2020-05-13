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

      $notificationModule->byDb('bbbbbb','ffffff');
      $notificationModule->byMail('bbbbb','bbbbbb','dddddd');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
