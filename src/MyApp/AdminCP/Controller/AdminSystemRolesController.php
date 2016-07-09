<?php
namespace MyApp\AdminCP\Controller;

use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use MyApp\AdminCP\Entity\AdminSystemRolesEntity;
use MyApp\AdminCP\Validation\AdminSystemRolesValidation;
use MyApp\MyHelper\GlobalHelper;


class AdminSystemRolesController extends Controller
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
            'title' => 'Manage System Roles'
        );
    }

    /**
     * @Route("/system-roles", name="admincp_system_roles_page")
     */
    public function indexAction(Request $request)
    {
        $key = $request->query->get('key') ? GlobalHelper::__xss_clean_string($request->query->get('key')) : '';

        $arr_order = $request->query->get('order') ? GlobalHelper::__handle_param_order_in_url($request->query->get('order')) : array('field'=>'id', 'by'=>'DESC');

        $date_range = $request->query->get('date_range') ? GlobalHelper::__handle_param_date_range_in_url($request->query->get('date_range')) : array();

        $limit = $request->query->get('lm') ? (int)$request->query->get('lm') : 10;
        $page_offset = $request->query->get('p') ? (int)$request->query->get('p') : 0;
        $offset = $page_offset > 0 ? ($page_offset - 1) * $limit : $page_offset * $limit;

    	$repository = $this->getDoctrine()->getRepository('AdminCPBundle:AdminSystemRolesEntity');
        $total = $repository->_getTotalRecords($key);
        $results = $repository->_getListRecords($limit, $offset, array('key' => $key, 'date_range' => $date_range), $arr_order);

        if($request->query->get('report')){
            $this->_report_data($results);
        }

        $pagination = GlobalHelper::__pagination($total, $page_offset, $limit, 3, $this->generateUrl('admincp_system_roles_page'));

        $this->data['results'] = $results;
        $this->data['pagination'] = $pagination;

        return $this->render('@admin/system-roles/list.html.twig', $this->data);
    }

    /**
     * @Route("/system-roles/create", name="admincp_system_roles_create_page")
     */
    public function createAction(Request $request)
    {
        $id = 0;
        $handle_data = self::handle_form_data($id, $request);

        if($handle_data['success']){
            $request->getSession()->getFlashBag()->add('message_data', 'Created record success!');
            $url = $this->generateUrl('admincp_system_roles_page');
            return $this->redirect($url, 301);
        }

        $this->data['form'] = $handle_data['form']->createView();
        $this->data['form_errors'] = $handle_data['form_errors'];
        $this->data['lists_modules'] = $handle_data['lists_modules'];

        return $this->render('@admin/system-roles/edit.html.twig', $this->data);

    }

    /**
     * @Route("/system-roles/edit/{id}", name="admincp_system_roles_edit_page")
     */
    public function editAction($id, Request $request)
    {
        $handle_data = self::handle_form_data($id, $request);

        if($handle_data['success']){
            $request->getSession()->getFlashBag()->add('message_data', 'Updated record success!');
            $url = $this->generateUrl('admincp_system_roles_page');
            return $this->redirect($url, 301);
        }

        $this->data['form'] = $handle_data['form']->createView();
        $this->data['form_errors'] = $handle_data['form_errors'];
        $this->data['lists_modules'] = $handle_data['lists_modules'];

        return $this->render('@admin/system-roles/edit.html.twig', $this->data);
    }

    /**
     * @Route("/system-roles/delete/{id}", name="admincp_system_roles_delete_page")
     */
    public function deleteAction($id , Request $request)
    {
        if($id > 0){
            $em = $this->getDoctrine()->getEntityManager();
            $check_exist_record = $em->getRepository('AdminCPBundle:AdminSystemRolesEntity')->find($id);
            if($check_exist_record){
                $em->getRepository('AdminCPBundle:AdminSystemRolesEntity')->_delete_record_DB($id);

                $request->getSession()->getFlashBag()->add('message_data', 'Deleted record success!');
            }

            $url = $this->generateUrl('admincp_system_roles_page');
            return $this->redirect($url, 301);
            exit();
        }

        return $this->render();
    }

    /**
     * This function Handle create vs update data including handle and handle record in database
     */
    private function handle_form_data($id, $request){
        $em = $this->getDoctrine()->getEntityManager();
        $result_data = $em->getRepository('AdminCPBundle:AdminSystemRolesEntity')->find($id);

        $fields_value = array(
            'id' => ( $id ? $id : 0 ),
            'role_name' => ( $result_data ? $result_data->getRole_Name() : '' ),
            'role_status' => ( $result_data ? $result_data->getRole_Status() : 0 )
        );

        $defaultData = array('message' => 'Type your message here');

        $form = $this->createFormBuilder($defaultData)
            //->setAction($this->generateUrl('admincp_system_roles_edit_page'))
            ->add('id', HiddenType::class, array(
                'data' => $fields_value['id'],
            ))
            ->add('role_name', TextType::class, array(
                'label' => 'Role Name',
                'data' => $fields_value['role_name']
            ))
            ->add('role_status', ChoiceType::class, array(
                'label' => 'Role Status',
                'data' => $fields_value['role_status'],
                'choices' => array( 0 => 'Unpblish', 1 => 'Publish')
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Submit',
            ))
            ->getForm();

        $form->handleRequest($request);

        $form_errors = '';
        $success = FALSE;
        if ($form->isSubmitted() && $form->isValid()) {
            $validation = new AdminSystemRolesValidation();

            $data = $form->getData();
            $validation->role_name = $data['role_name'];

            $validator = $this->get('validator');
            $errors = $validator->validate($validation);

            $form_errors = GlobalHelper::getErrorMessages($errors);
            if(!$form_errors){
                $em = $this->getDoctrine()->getEntityManager();
                $data['role_type'] = self::_filter_permission_role_type($request->request->get('role_type'));
                if($data['id'] > 0){
                    /* Update record */
                    $id = $em->getRepository('AdminCPBundle:AdminSystemRolesEntity')->_update_record_DB($data);
                } else {
                    /* Create new record */
                    $id = $em->getRepository('AdminCPBundle:AdminSystemRolesEntity')->_create_record_DB($data);
                }

                $success = TRUE;
            }
        }

        $lists_modules = array();
        $getListModules = $em->getRepository('AdminCPBundle:AdminSystemRolesEntity')->_getListModules();
        if(!empty($getListModules)){
            foreach ($getListModules as $key => $module) {
                $var = new \stdClass();
                $var->id = $module['id'];
                $var->module_name = $module['module_name'];
                $var->view = self::_check_role_system($id, $module['id'], 'view');
                $var->add = self::_check_role_system($id, $module['id'], 'add');
                $var->edit = self::_check_role_system($id, $module['id'], 'edit');
                $var->delete = self::_check_role_system($id, $module['id'], 'delete');
                $module = $var;
                $lists_modules[] = $module;
            }
        }

        $handle_data = array(
            'lists_modules' => $lists_modules,
            'form' => $form,
            'form_errors' => $form_errors,
            'success' => $success
        );

        return $handle_data;
    }

    /**
     * Report data into file excel
     */
    private function _report_data($arrData = array())
    {
        return new Response();
    }

    /**
     * This function use to filter permission for each modules
     */
    private function _filter_permission_role_type($role_type){
        if(!empty($role_type)){
            foreach ($role_type as $key => $value) {
                $arr_val= array();
                if(!empty($value['view'])){
                    $arr_val['view'] = 1;
                }else{
                    $arr_val['view'] = 0;
                }

                if(!empty($value['add'])){
                    $arr_val['add'] = 1;
                }else{
                    $arr_val['add'] = 0;
                }

                if(!empty($value['edit'])){
                    $arr_val['edit'] = 1;
                }else{
                    $arr_val['edit'] = 0;
                }

                if(!empty($value['delete'])){
                    $arr_val['delete'] = 1;
                }else{
                    $arr_val['delete'] = 0;
                }
                $role_type[$key] = $arr_val;
            }
        }
        return serialize($role_type);
    }

    /**
     * This function use to check exists record of the role in database
     */
    protected function _check_role_system($role_id, $module_id, $action = ''){
        $em = $this->getDoctrine()->getEntityManager();
        $result_role_active = $em->getRepository('AdminCPBundle:AdminSystemRolesEntity')->find($role_id);
        if(!empty($result_role_active)){
            if(!empty($result_role_active->getRole_Type())){
                $role_type = unserialize($result_role_active->getRole_Type());
                if(!empty($role_type[$module_id][$action])) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }
}    