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

    public function _getListRecords($offset, $limit, $key = ''){
        $repository = $this->getEntityManager()->getRepository('AdminCPBundle:AdminSystemModulesEntity');
        $query = $repository->createQueryBuilder('pk');
        $query->select("pk");
        if($key){
            $query->where('pk.module_name LIKE :key')->setParameter('key', '%'.$key.'%');
        }
        $query->orderBy("pk.created_date", "DESC");
        $query->setMaxResults($offset);
        $query->setFirstResult($limit);
        $result = $query->getQuery();

        return $result->getResult();;
    }

    public function _getTotalRecords($key = ''){
        $repository = $this->getEntityManager()->getRepository('AdminCPBundle:AdminSystemModulesEntity');
        $query = $repository->createQueryBuilder('pk');
        $query->select('COUNT(pk.id)');
        if($key){
            $query->where('pk.module_name LIKE :key')->setParameter('key', '%'.$key.'%');
        }
        $total = $query->getQuery()->getSingleScalarResult();

        return $total;
    }

}