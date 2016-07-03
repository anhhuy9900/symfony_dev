<?php
namespace MyApp\AdminCP\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyApp\AdminCP\Entity\AdminLoginEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MyApp\MyHelper\GlobalHelper;

class AdminCPController extends Controller
{
    private $admincp_service;
    private $data;
    /**
     * Used as constructor
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->admincp_service = $this->container->get('app.admincp_service');
        $this->admincp_service->admin_CheckValidLogin();
        $this->data = array(
            'title' => 'Admin DasnhBoard'
        );
    }

    /**
     * @Route("/", name="admincp_page")
     */
    public function indexAction(Request $request)
    {

        $this->data['title'] = 'Admin DasnhBoard';
        return $this->render('@admin/admin.html.twig', $this->data);
    }



    /**
     * @Route("/test", name="admincp_test_page")
     */
    public function testAction(Request $request)
    {   
        /*$entity = new AdminLoginEntity();
        dump($entity);
        //dump($entity->checkValidPassword('66565656'));
        die;*/

        $password = 'anhhuy@#';
        dump($this->admincp_service->checkValidPassword($password));
        die();
    }

}