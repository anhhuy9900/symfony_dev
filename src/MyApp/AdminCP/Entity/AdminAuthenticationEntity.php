<?php
namespace MyApp\AdminCP\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;


/**
 * @ORM\Entity
 * @ORM\Table(name="system_users")
 * @ORM\Entity(repositoryClass="MyApp\AdminCP\Repository\AdminCPRepository")
 */
class AdminAuthenticationEntity {
    /**
     * @ORM\Column(type="integer")
     * @ORM\id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=3)
     */
    private $role_id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $user_token;

    /**
     * @ORM\Column(type="smallint", length=1)
     */
    private $permission_limit;

    /**
     * @ORM\Column(type="smallint", length=1)
     */
    private $status;

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

    public function getId() {
        return $this->id;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setPassword($password) {
    $this->password = $password;
}

    public function getPassword() {
        return $this->password;
    }

    public function setUser_Token($user_token) {
        $this->user_token = $user_token;
    }

    public function getUser_Token() {
        return $this->user_token;
    }

    public function setPermission_limit($permission_limit) {
        $this->permission_limit = $permission_limit;
    }

    public function getPermission_limit() {
        return $this->permission_limit;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
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