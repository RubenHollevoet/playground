<?php
/**
 * Created by PhpStorm.
 * User: ruben.hollevoet
 * Date: 17/04/18
 * Time: 18:24
 */

namespace App\Security;


use App\Entity\User;
use App\Service\FacebookUserProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Component\HttpFoundation\Session\Session;

class FacebookFormAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    private $em;
//    private $router;
    private $facebookUserProvider;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    private $flashBag;

    private $session;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, TokenStorageInterface $tokenStorage, FacebookUserProvider $facebookUserProvider, SessionInterface $session, FlashBagInterface $flashBag)
    {
        $this->em = $em;
//        $this->router = $router;
        $this->facebookUserProvider = $facebookUserProvider;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;

        $this->flashBag = $flashBag;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {

        return $this->session->has('fb_access_token');

//        return $this->tokenStorage->getToken();

//        return $request->headers->has('X-AUTH-TOKEN');

//        return true;
    }

    public function getCredentials(Request $request)
    {
//        return 'ok';

//        dump($this->facebookUserProvider->getCurrentUser()); die;

        return $this->facebookUserProvider->getCurrentUser();
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->em->getRepository(User::class)
            ->find(1);

        $fbUserId = $credentials['id'];

        if(!$this->facebookUserProvider->createOrUpdateUser($credentials)) {
            //error when creating user
            $this->facebookUserProvider->resetFacebookUserSession();
            $this->session->getFlashBag()->add('error', 'Er liep iets mis, je Facebook profiel kon helaas niet geregistreerd worden. Neem contact op met Kazou indien het probleem zich blijft voordoen.');
        }

        return $this->em->getRepository(User::class)
            ->findOneBy(['fb_userId' => $fbUserId]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

//    protected function getLoginUrl()
//    {
//        return $this->router->generate('security_login');
//    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;


        // if the user hits a secure page and start() was called, this was
        // the URL they were on, and probably where you want to redirect to
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->router->generate('expenses');
        }

        return new RedirectResponse($targetPath);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $this->flashBag->add('danger', 'Je hebt niet de juiste rechten deze pagina te bekijken.');

        return new RedirectResponse('/');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
