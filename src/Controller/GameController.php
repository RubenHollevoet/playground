<?php


namespace App\Controller;


use App\Service\FacebookUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
    public function index(FacebookUserProvider $facebookUserProvider) : Response
    {
        $FbLoginUrl = $facebookUserProvider->getLoginUrl();

        return $this->render('game/home.html.twig', [
            'fbLoginUrl' => $FbLoginUrl
        ]);
    }

    public function tasks() : Response
    {
        $tasks = [
            [
                'name' => 'opdracht 1',
                'score' => 10
            ],
            [
                'name' => 'opdracht 5',
                'score' => 5
            ],
        ];

        return $this->render('game/tasks.html.twig',
            [
                'tasks' => $tasks
            ]
        );
    }

    public function rewards() : Response
    {
        return $this->render('game/rewards.html.twig');
    }

    public function rules() : Response
    {
        return $this->render('game/rules.html.twig');
    }

    public function events() : Response
    {
        return $this->render('game/events.html.twig');
    }

    public function leaderboard() : Response
    {
        return $this->render('game/leaderboard.html.twig');
    }
}
