<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
    public function index()
    {
        return $this->render('game/home.html.twig');
    }
}