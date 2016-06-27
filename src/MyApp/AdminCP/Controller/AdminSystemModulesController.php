<?php
namespace MyApp\AdminCP\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyApp\AdminCP\Entity\AdminSystemModulesEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MyApp\MyHelper\GlobalHelper;

class AdminSystemModulesController extends Controller
{
    private $admincp_service;

    /**
     * Used as constructor
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->admincp_service = $this->container->get('app.admincp_service');
        $this->admincp_service->admin_CheckValidLogin();
    }

    /**
     * @Route("/system-modules", name="admincp_system_modules_page")
     */
    public function indexAction(Request $request)
    {   
    	$repository = $this->getDoctrine()->getRepository('AdminCPBundle:AdminSystemModulesEntity');
    	$results = $repository->findAll();

    	//dump($lists_modules);die();

        $data = array(
        	'results' => $results
        );
        return $this->render('@admin/system-modules/list.html.twig', $data);
    }

    /**
     * @Route("/system-modules/edit/{id}", name="admincp_system_modules_edit_page")
     */
    public function editAction($id)
    {   
        $data = array();
        return $this->render('@admin/system-modules/edit.html.twig', $data);
    }

    /**
     * @Route("/system-modules/delete/{id}", name="admincp_system_modules_delete_page")
     */
    public function deleteAction($id)
    {   
        $data = array();
        return $this->render();
    }
}    