<?php
namespace MyApp\AdminCP\Validation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContext;
use MyApp\AdminCP\Entity\AdminLoginEntity;
use MyApp\AdminCP\Repository\AdminCPRepository;



class AdminLoginValidation extends Controller {

    public $username;
    public $password;

    public function __construct($em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        // some extra stuff
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
        //$metadata->addPropertyConstraint('password', $class->valid_password_not_match() );
         $metadata->addConstraint(new Assert\Callback('validate'));
    }

    /*public function valid_password_not_match()
    {
        $password = md5($this->password);

        $em = $this->container->getDoctrine()->getManager();

        $query = $em->createQuery(
            "SELECT password
            FROM  system_users pk
            WHERE pk.password > :password"
        )->setParameter('password', $password);

        $result = $em->getResult();
        if(empty($result)){
            return FALSE;
        }

        return TRUE;
    }*/

    public function validate()
    {   
        $entity = new AdminLoginEntity();
        $repository = new AdminCPRepository();
        //$articleRepo = $this->getDoctrine()->getRepository('AdminCPBundle:AdminLoginEntity');
        //dump($articleRepo->checkValidPassword('556655'));
        dump($repository);
        die;


        $admincp_service = $this->container->get('app.admincp_service');
        $password = md5($this->password);
        dump($admincp_service->valid_password_not_match($password));
    
        die();
    }
}