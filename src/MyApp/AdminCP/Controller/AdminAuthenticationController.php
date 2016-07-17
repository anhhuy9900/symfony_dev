<?php
namespace MyApp\AdminCP\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyApp\AdminCP\Validation\AdminLoginValidation;
use MyApp\AdminCP\Entity\AdminAuthenticationEntity;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MyApp\MyHelper\GlobalHelper;

class AdminAuthenticationController extends Controller
{
    private $admincp_service;
    /**
     * Used as constructor
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->admincp_service = $this->container->get('app.admincp_service');

    }

    /**
     * @Route("/admind/login", name="admincp_login_page")
     */
    public function loginAction(Request $request)
    {

        if($this->admincp_service->admin_UserSessionLogin()){
            header('Location: ' . $this->generateUrl('admincp_page'));
            exit();
        }

        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            //->setAction($this->generateUrl('login_form_submit'))
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            //->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);


        $form_errors = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $validation = new AdminLoginValidation();

            $data = $form->getData();
            $validation->username = $data['username'];
            $validation->password = md5($data['password']);

            $validator = $this->get('validator');
            $errors = $validator->validate($validation);

            $form_errors = GlobalHelper::getErrorMessages($errors);
            if(!$form_errors){
                $this->admincp_service->admin_onAuthentication($data);

                $url = $this->generateUrl('admincp_page');
                return $this->redirect($url, 301);
                exit();
            }
        }



        $data = array(
            'form' => $form->createView(),
            'form_errors' => $form_errors
        );
        return $this->render('@admin/login.html.twig', $data);
    }

    /**
     * @Route("/admind/logout", name="admincp_logout_page")
     */
    public function logoutAction(Request $request)
    {  
        $session = $request->getSession();
        if(!empty($session->get('_security_secured_userad'))){
            $session->remove('_security_secured_userad');
            header('Location: ' . $this->generateUrl('admincp_login_page'));
            exit();
        }

        return FALSE;
    }
    
}