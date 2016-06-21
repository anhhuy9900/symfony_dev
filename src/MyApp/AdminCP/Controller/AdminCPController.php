<?php
namespace MyApp\AdminCP\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyApp\AdminCP\Validation\AdminLoginValidation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MyApp\AdminCP\Entity\AdminLoginEntity;


class AdminCPController extends Controller
{
    

    /**
     * @Route("/", name="myapp_admincp")
     */
    public function indexAction(Request $request)
    {
        $data = array();
        return $this->render('@admin/admin.html.twig', $data);
    }

    /**
     * @Route("/login", name="admincp_page")
     */
    public function loginAction(Request $request)
    {


        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            //->setAction($this->generateUrl('product_form_submit'))
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            //->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);


        $form_errors = array();
        if ($form->isSubmitted() && $form->isValid()) {
            $validation = new AdminLoginValidation();

            $data = $form->getData();
            $validation->username = $data['username'];
            $validation->password = $data['password'];

            $validator = $this->get('validator');
            $errors = $validator->validate($validation);
            if(count($errors) > 0){
                $form_errors = $errors;
            }
        }

        $data = array(
            'form' => $form->createView(),
            'form_errors' => $form_errors
        );
        return $this->render('@admin/login.html.twig', $data);
    }

    /**
     * @Route("/test", name="admincp_test_page")
     */
    public function testAction(Request $request)
    {   
        $entity = new AdminLoginEntity();
        dump($entity);
        //dump($entity->checkValidPassword('66565656'));
        die;

        $admincp_service = $this->container->get('app.admincp_service');
        $password = 123456;
        dump($admincp_service->valid_password_not_match($password));
        die();
    }
}