<?php
namespace MyApp\AdminCP\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use MyApp\AdminCP\Entity\AdminLoginEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class AdminCPService extends Controller{


    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    function admin_checkValidUser($username, $password)
    {

        $repository = $this->em->getRepository('AdminCPBundle:AdminLoginEntity');
        $query = $repository->createQueryBuilder('pk')
        ->where('pk.username = :username')
        ->andWhere('pk.password = :password')
        ->setParameters(array('username' => $username, 'password'=> $password))
        ->getQuery();    

        $result = $query->getResult();
        if(!empty($result)){
            return TRUE;
        }

        return FALSE;
    }


    function admin_onAuthentication($data){
        $session = new Session(new PhpBridgeSessionStorage());
        $session->start();

        $firewall = 'secured_userad';
        $token = new UsernamePasswordToken('admin', null, $firewall, array('ROLE_ADMIN'));

        /*$user = array(
            'username' => $data['username'],
            'ad_token' => $token,
        );*/

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
    }

    function admin_UserSessionLogin(){
        $session = new Session();

        $valid = FALSE;
        if(!empty($session->get('_security_secured_userad'))){
            $valid = TRUE;
        }

        return $valid;
    }

    function admin_CheckValidLogin(){
        if(!$this->admin_UserSessionLogin()){
            header('Location: ' . $this->generateUrl('admincp_login_page'));
            exit();
        }

    }
}