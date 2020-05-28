<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notifications;
use App\Services\NotificationModule;
use App\Services\CookieGenerator;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\PublisherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(NotificationModule $notificationModule, CookieGenerator $cookieGenerator )
    {

        $reponse = $this->render('home/index.html.twig',[]);
        $reponse->headers->setCookie($cookieGenerator->generate());
        return $reponse;
    }

}
