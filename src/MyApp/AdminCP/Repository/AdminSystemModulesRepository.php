<?php
namespace MyApp\AdminCP\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use MyApp\AdminCP\Entity\AdminSystemModulesEntity;


/**
 * @ORM\Table(name="system_modules")
 * @ORM\Entity(repositoryClass="AdminSystemModulesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AdminSystemModulesRepository extends EntityRepository
{

    public function _create_record_DB($data)
    {
        $entity = new AdminSystemModulesEntity();
        $entity->setParentID($data['parent_id']);
        $entity->setModule_Name($data['module_name']);
        $entity->setModule_Alias($data['module_alias']);
        $entity->setModule_Status($data['module_status']);
        $entity->setModule_Order($data['module_order']);
        $entity->setUpdated_Date(time());
        $entity->setCreated_Date(time());

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity->getID();
    }

    public function _update_record_DB($data)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository('AdminCPBundle:AdminSystemModulesEntity')->find($data['id']);

        $entity->setParentID($data['parent_id']);
        $entity->setModule_Name($data['module_name']);
        $entity->setModule_Alias($data['module_alias']);
        $entity->setModule_Status($data['module_status']);
        $entity->setModule_Order((int)$data['module_order']);
        $entity->setUpdated_Date(time());

        $em->flush();
        return $entity->getID();
    }

    public function _delete_record_DB($id){
        $em = $this->getEntityManager();
        $entity = $em->getRepository('AdminCPBundle:AdminSystemModulesEntity')->findOneBy(array('id'=>$id));
        $em->remove($entity);
        $em->flush();
    }

    public function _getListRecords($offset, $limit){
        $repository = $this->getEntityManager()->getRepository('AdminCPBundle:AdminSystemModulesEntity');
        $result = $repository->createQueryBuilder('pk')
            ->select("pk")
            ->orderBy("pk.created_date", "DESC")
            ->setMaxResults($offset)
            ->setFirstResult($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function _getTotalRecords(){
        $repository = $this->getEntityManager()->getRepository('AdminCPBundle:AdminSystemModulesEntity');
        $total = $repository->createQueryBuilder('pk')
            ->select('COUNT(pk.id)')
            ->getQuery()->getSingleScalarResult();

        return $total;
    }

}