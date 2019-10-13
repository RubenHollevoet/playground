<?php


namespace App\Controller;


use App\Entity\QrCode;
use App\Entity\Subscription;
use App\Service\FacebookUserProvider;
use App\Service\ImageService;
use AppTestBundle\Entity\FunctionalTests\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function camera() : Response
    {
        return $this->render('game/camera.html.twig', [
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

    public function postQrCode(Request $request, EntityManagerInterface $em, ImageService $imageService) : JsonResponse
    {

        $response = json_decode($request->getContent());

        /**
         * @var  $qrCode QrCode
         */
        $qrCode = $em->getRepository(QrCode::class)->findOneBy(['code' => $response->code]);

        if(!$qrCode) {
            return new JsonResponse([
                'status' => 'unknown',
            ]);
        }

        $image = $imageService->base64ToJpeg($response->photo, '/uploads/qr/', $this->getUser()->getId().'_'.$qrCode->getTask()->getId().'_'.date('Y-m-d-H-i-s').'.jpg');

        $subscription = new Subscription();
        $subscription->setStatus(Subscription::STATUS_PRE_APPROVED);
        $subscription->setTask($qrCode->getTask());
        $subscription->setUser($this->getUser());
        $subscription->setImage($image);

        $em->persist($subscription);
        $em->flush();

//        var_dump($request->request->get('code')); die;

        $response = [
            'status' => 'ok',
            'points' => 10,
        ];

        return new JsonResponse($response);
    }
}
