<?php


namespace App\Controller;


use App\Service\FacebookUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private $facebookUserProvider;

    /**
     * UserController constructor.
     */
    public function __construct(FacebookUserProvider $facebookUserProvider)
    {
        $this->facebookUserProvider = $facebookUserProvider;
    }

    public function loginResponse() {
        return $this->facebookUserProvider->handleResponse();
    }
}
