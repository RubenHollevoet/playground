<?php
/**
 * Created by PhpStorm.
 * User: ruben.hollevoet
 * Date: 21/04/18
 * Time: 08:40
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;

class FacebookUserProvider
{

    private $param_facebook_oauth_redirect;
    private $param_facebook_app_id;
    private $param_facebook_app_secret;
    private $param_upload_directory;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Router
     */
    private $router;

    private $em;

//    private $parameterBag;


    /**
     * FacebookUserProvider constructor.
     */
    public function __construct(EntityManagerInterface $em, SessionInterface $session, RouterInterface $router, ParameterBagInterface $parameterBag)
    {
        $this->em = $em;
        $this->param_facebook_oauth_redirect = $_SERVER['FACEBOOK_REDIRECT'];
        $this->param_facebook_app_id = $_SERVER['FACEBOOK_APP_ID'];
        $this->param_facebook_app_secret = $_SERVER['FACEBOOK_APP_SECRET'];
//        $this->param_upload_directory = $parameterBag->get('upload_directory');
        $this->session = $session;
        $this->router = $router;
    }

    public function handleResponse()
    {
        $fb = $this->getFacebook();

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken($this->param_facebook_oauth_redirect);
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo '.. Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($this->param_facebook_app_id); // Replace {app-id} with your app id
        // If you know the user ID this access token belongs to, you can validate it here
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        $this->session->set('fb_access_token', (string) $accessToken);

        return new RedirectResponse($this->router->generate('app.game.home'));
    }

    public function getCurrentUser()
    {
        $fb_access_token = $this->session->get('fb_access_token');
        if($fb_access_token)
        {
            $fb = $this->getFacebook();

            try {
                // Returns a `Facebook\FacebookResponse` object
                $fb_response = $fb->get('/me?fields=id,first_name,last_name,email,picture.width(800).height(800).redirect(0)', $this->session->get('fb_access_token'));
            } catch(FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            $fbUser = $fb_response->getGraphUser();


            return $fbUser;
        }
        else
        {
            return;
        }
    }

    public function createOrUpdateUser($credentials) {

        $user = $this->em->getRepository(User::class)->findOneBy(['fbId' => $credentials->getId()]);

        if(!$user) {
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $credentials->getField('email')]);
        }

        if($user) {
            //if account doesn't have a FB ID yet (if it has been created using standard method), add it
            if(!$user->getFbId()) {
                $user->setFbId($credentials->getId());
                $this->em->persist($user);
                $this->em->flush();

                $this->session->getFlashBag()->add('success', 'Goed nieuws! We hebben je Facebook account succesvol aan je al bestaande account gekoppeld!');
            }
        }
        else {
            $user = new User();
            $user->setFirstName($credentials->getFirstName());
            $user->setLastName($credentials->getLastName());
            $user->setEmail($credentials->getField('email'));
            $user->setFbId($credentials->getId());
            $user->setRoles(['ROLE_USER']);

            $this->session->getFlashBag()->add('success', 'Goed nieuws! We hebben je account succesvol aangemaakt!');
        }
/*
        if(!$user->getPicture()) {
            //upload profile picture
            $folder = '/uploads/profile/';
//            $uploadPath = $this->param_upload_directory;
//            if(!is_dir($uploadPath.$folder))
//            {
//                mkdir($uploadPath.$folder, 0777, true);
//            }

            $fileName = (string)$credentials->getId().'-avatar.jpg';
            file_put_contents($uploadPath.$folder.$fileName, fopen($credentials->getPicture()->getUrl(), 'r'));

            $user->setPicture($folder.$fileName);
        }*/

        //persist user
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

    public function getLoginUrl()
    {
        $fb = $this->getFacebook();
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email']; // Optional permissions
        return $helper->getLoginUrl($this->param_facebook_oauth_redirect, $permissions);
    }

    /**
     * @return mixed
     */
    public function getParamFacebookOauthRedirect()
    {
        return $this->param_facebook_oauth_redirect;
    }

    public function resetFacebookUserSession() {
        $this->session->clear('fb_access_token');
    }

    private function getFacebook() {

        $fb = new Facebook([
            'app_id' => $this->param_facebook_app_id,
            'app_secret' => $this->param_facebook_app_secret,
            'default_graph_version' => 'v2.2',
        ]);

        return $fb;
    }
}
