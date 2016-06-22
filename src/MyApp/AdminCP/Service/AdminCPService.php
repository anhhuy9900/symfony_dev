<?php
namespace MyApp\AdminCP\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use MyApp\AdminCP\Entity\AdminLoginEntity;

class AdminCPService extends Controller{


    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    function admin_checkValidPassword($password)
    {
        $password = md5($password);

        $query = $this->em->createQuery(
            "SELECT pk.password
            FROM  AdminCPBundle:AdminLoginEntity pk
            WHERE pk.password = :password"
        )->setParameter('password', $password);

        $result = $query->getResult();
        if(!empty($result)){
            return TRUE;
        }

        return FALSE;
    }

    function admin_checkValidUsername($username)
    {

        $query = $this->em->createQuery(
            "SELECT pk.username
            FROM  AdminCPBundle:AdminLoginEntity pk
            WHERE pk.username LIKE :username"
        )->setParameter('username', $username);

        $result = $query->getResult();
        if(!empty($result)){
            return TRUE;
        }

        return FALSE;
    }

}