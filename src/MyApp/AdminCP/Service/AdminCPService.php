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

    private function getErrorMessages($errors) {
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