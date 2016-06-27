<?php
namespace MyApp\AdminCP\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;


/**
 * @ORM\Entity
 * @ORM\Table(name="system_modules")
 * @ORM\Entity(repositoryClass="MyApp\AdminCP\Repository\AdminSystemModulesRepository")
 */
class AdminSystemModulesEntity {
    /**
     * @ORM\Column(type="integer", length=5)
     * @ORM\id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=5)
     */
    private $parent_id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $module_name;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $module_alias;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $module_type;

    /**
     * @ORM\Column(type="smallint", length=1)
     */
    private $module_status;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $module_order;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $updated_date;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $created_date;

    /*public function __construct(Doctrine $doctrine)
    {
        $this->em = $doctrine->getEntityManager();
    }*/

    public function getModuleID() {
        return $this->module_id;
    }

    public function setParentID($parent_id) {
        $this->parent_id = $parent_id;
    }

    public function getParentID() {
        return $this->parent_id;
    }

    public function setModuleName($module_name) {
        $this->module_name = $module_name;
    }

    public function getModuleName() {
        return $this->module_name;
    }

    public function setModuleAlias($module_alias) {
        $this->module_alias = $module_alias;
    }

    public function getModuleAlias() {
        return $this->module_alias;
    }

    public function setModuleType($module_type) {
        $this->module_type = $module_type;
    }

    public function getModuleType() {
        return $this->module_type;
    }

    public function setModuleStatus($module_status) {
        $this->module_status = $module_status;
    }

    public function getModuleStatus() {
        return $this->module_status;
    }

    public function setModuleOrder($module_order) {
        $this->module_order = $module_order;
    }

    public function getModuleOrder() {
        return $this->module_order;
    }

    public function setUpdated_date($updated_date) {
        $this->updated_date = $updated_date;
    }

    public function getUpdated_date() {
        return $this->updated_date;
    }

    public function setCreated_date($created_date) {
        $this->created_date = $created_date;
    }

    public function getCreated_date() {
        return $this->created_date;
    }

}