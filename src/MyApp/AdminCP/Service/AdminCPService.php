<?php
namespace MyApp\AdminCP\Service;

class AdminCPService {


    function __construct()
    {
        
    }

    function valid_password_not_match($password)
    {
        /*$query = $this->connDefault->createQuery(
            "SELECT password
            FROM  system_users pk
            WHERE pk.password > :password"
        )->setParameter('password', $password);

        $result = $em->getResult();
        if(empty($result)){
            return FALSE;
        }*/

        return TRUE;
    }

}