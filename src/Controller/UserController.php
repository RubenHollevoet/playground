<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('user/login.html.twig', [
            'fbLoginUrl' => $this->container->get('app.service.facebook_user_provider')->getLoginUrl(),
            'error' => $error,
        ]);
    }
}
