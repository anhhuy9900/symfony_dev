<?php

namespace MyApp\AdminCP\Entity\AdminCp;

use Doctrine\ORM\Mapping as ORM;

/**
 * Index
 *
 * @ORM\Table(name="admin_cp\index")
 * @ORM\Entity(repositoryClass="MyApp\AdminCP\Repository\AdminCp\IndexRepository")
 */
class Index
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
