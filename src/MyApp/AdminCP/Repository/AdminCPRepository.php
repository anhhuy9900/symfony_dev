<?php
namespace MyApp\AdminCP\Repository;

use Doctrine\ORM\EntityRepository;

class AdminCPRepository extends EntityRepository
{
    /*public function countForCategory($id)
    {
        $result= $this->createQueryBuilder('a')
            ->join('a.category', 'c')
            ->where('c.category_id = :id')
            ->setParameter('id', $id)
            ->select('count(a')
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }*/

    public function checkValidPassword($value){

        //$doctrime = $this->_em->getDoctrine()->getManager();

        $query = $this->createQuery(
            "SELECT password
            FROM  system_users pk
            WHERE pk.password > :password"
        )->setParameter('password', $value);

        $result = $query->getResult();
        if(empty($result)){
           return TRUE;
        }

        return FALSE;
    }
}