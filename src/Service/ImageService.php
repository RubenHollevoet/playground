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

class ImageService
{
    private $rootFolder;

    /**
     * FacebookUserProvider constructor.
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->rootFolder = $parameterBag->get('kernel.project_dir') . '/public';

//        die($rootFolder);
    }

    function base64ToJpeg($base64String, $dir, $fileName) {


        // open the output file for writing
        $ifp = fopen( $this->rootFolder.$dir.$fileName, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64String );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp );

        return $dir.$fileName;
    }
}
