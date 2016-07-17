<?php
namespace MyApp\News\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use MyApp\News\Entity\NewsEntity;


/**
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="AdminNewsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AdminNewsRepository extends EntityRepository
{

    public function _create_record_DB($data)
    {
        $entity = new NewsEntity();
        $entity->setCategoryID($data['category_id']);
        $entity->setTitle($data['title']);
        $entity->setImage($data['image']);
        $entity->setDescription($data['description']);
        $entity->setContent($data['content']);
        $entity->setStatus($data['status']);
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
        $entity = $em->getRepository('NewsBundle:NewsEntity')->find($data['id']);

        $entity->setCategoryID($data['category_id']);
        $entity->setTitle($data['title']);
        $entity->setImage($data['image']);
        $entity->setDescription($data['description']);
        $entity->setContent($data['content']);
        $entity->setStatus($data['status']);
        $entity->setUpdated_Date(time());

        $em->flush();

        return $entity->getID();
    }

    public function _delete_record_DB($id){
        $em = $this->getEntityManager();
        $entity = $em->getRepository('NewsBundle:NewsEntity')->findOneBy(array('id'=>$id));
        $em->remove($entity);
        $em->flush();
    }

    public function _getListRecords($offset, $limit, $where = array(), $order = array('field'=>'id', 'by'=>'DESC')){
        $repository = $this->getEntityManager()->getRepository('NewsBundle:NewsEntity');
        $query = $repository->createQueryBuilder('pk');
        $query->select("pk");
        $query->where('pk.id > 0');
        if(!empty($where)){
            if($where['key']){
                $query->andWhere('pk.title LIKE :key')->setParameter('key', '%'.$where['key'].'%');
            }
            //dump($where['date_range']);die();
            if($where['date_range']){
                $query->andWhere('pk.updated_date >= :date_from')->setParameter('date_from', $where['date_range']['from']);
                $query->andWhere('pk.updated_date <= :date_to')->setParameter('date_to', $where['date_range']['to']);
            }
        }
        $query->orderBy("pk.".$order['field'], $order['by']);
        $query->setMaxResults($offset);
        $query->setFirstResult($limit);
        $result = $query->getQuery();

        return $result->getResult();
    }

    public function _getTotalRecords($key = ''){
        $repository = $this->getEntityManager()->getRepository('NewsBundle:NewsEntity');
        $query = $repository->createQueryBuilder('pk');
        $query->select('COUNT(pk.id)');
        if($key){
            $query->where('pk.title LIKE :key')->setParameter('key', '%'.$key.'%');
        }
        $total = $query->getQuery()->getSingleScalarResult();

        return $total;
    }


}