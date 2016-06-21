<?php
namespace MyApp\AdminCP\Validation;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class AdminLoginValidation  {

    public $username;
    public $password;

    protected $em;



  public function __construct(\Doctrine\ORM\EntityManager $em)
  {
      $this->em = $em;
  }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

        $metadata->addPropertyConstraint('username', new Assert\NotBlank(
            array(
                'message' => 'Username should not be blank',
            )
        ));
        $metadata->addPropertyConstraint('password', new Assert\NotBlank(
            array(
                'message' => 'Password should not be blank',
            )
        ));
        $metadata->addPropertyConstraint('password', new Assert\Length(array(
            'min'        => 6,
            'max'        => 50,
            'minMessage' => 'Password must be at least {{ limit }} characters long',
            'maxMessage' => 'Password cannot be longer than {{ limit }} characters',
        )));

         $class = new AdminLoginValidation();
        /*$metadata->addConstraint($class->valid_password_not_match($class->password));
         dump($class->valid_password_not_match($class->password));*/
        $metadata->addPropertyConstraint('password', $class->valid_password_not_match() );
    }

    public function valid_password_not_match()
    {
        $value = md5($this->password);



        $query = $this->em->createQuery(
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