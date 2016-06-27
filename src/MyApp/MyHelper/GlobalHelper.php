<?php
namespace MyApp\MyHelper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use MyApp\AdminCP\Entity\AdminLoginEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class GlobalHelper{

    public static function pr($data, $type = 0) {
        print '<pre>';
        print_r($data);
        print '</pre>';
        if ($type != 0) {
            exit();
        }
    }

    public static function getErrorMessages($errors) {
        $error_message = '';

        if(count($errors) > 0){
            foreach ($errors as $key => $error) {
                $error_message = $error->getMessage();
                break;
            }
        }

        return $error_message;
    }

}