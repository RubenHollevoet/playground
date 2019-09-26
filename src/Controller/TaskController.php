<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;

class TaskController
{
    public function index()
    {
        return new Response('honepage');
    }
}
