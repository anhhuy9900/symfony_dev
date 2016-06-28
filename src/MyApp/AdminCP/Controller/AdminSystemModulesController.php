<?php
namespace MyApp\AdminCP\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyApp\AdminCP\Entity\AdminSystemModulesEntity;
use MyApp\AdminCP\Validation\AdminSystemModulesValidation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MyApp\MyHelper\GlobalHelper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


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
        $limit = 10;
        $page_offset = $request->query->get('p') ? (int)$request->query->get('p') : 0;
        $offset = $page_offset > 0 ? ($page_offset - 1) * $limit : $page_offset * $limit;

    	$repository = $this->getDoctrine()->getRepository('AdminCPBundle:AdminSystemModulesEntity');
        $total = $repository->_getTotalRecords();
        $results = $repository->_getListRecords($limit, $offset);

        $pagination = GlobalHelper::__pagination($total, $page_offset, $limit, 3, $this->generateUrl('admincp_system_modules_page'));

        $data = array(
        	'results' => $results,
        	'pagination' => $pagination
        );
        return $this->render('@admin/system-modules/list.html.twig', $data);
    }

    /**
     * @Route("/system-modules/edit/{id}", name="admincp_system_modules_edit_page")
     */
    public function editAction($id, Request $request)
    {   

        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            //->setAction($this->generateUrl('admincp_system_modules_edit_page'))
            ->add('parent_id', ChoiceType::class, array(
                'label' => 'Parent',
                'choices' => array(0 => 'Unpblish', 1 => 'Publish')
            ))
            ->add('module_name', TextType::class, array(
                'label' => 'Module Name',
            ))
            ->add('module_alias', TextType::class, array(
                'label' => 'Module Alias',
            ))
            ->add('module_order', TextType::class, array(
                'label' => 'Module Order',
            ))
            ->add('module_status', ChoiceType::class, array(
                'label' => 'Module Status',
                'choices' => array( 0 => 'Unpblish', 1 => 'Publish')
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Submit',
            ))
            ->getForm();

        $form->handleRequest($request);

        $form_errors = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $validation = new AdminSystemModulesValidation();

            $data = $form->getData();
            $validation->module_name = $data['module_name'];;
            $validation->module_alias = $data['module_alias'];;
            $validation->module_order = (int)$data['module_order'];;

            $validator = $this->get('validator');
            $errors = $validator->validate($validation);

            $form_errors = GlobalHelper::getErrorMessages($errors);
            if(!$form_errors){
              // $entity = new AdminSystemModulesRepository();
               $em = $this->getDoctrine()->getEntityManager();
               $id = $em->getRepository('AdminCPBundle:AdminSystemModulesEntity')->create_record_DB($data);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'form_errors' => $form_errors
        );
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