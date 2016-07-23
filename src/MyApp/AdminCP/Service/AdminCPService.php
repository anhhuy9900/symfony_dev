<?php
namespace MyApp\AdminCP\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use MyApp\AdminCP\Entity\AdminAuthenticationEntity;

class AdminCPService extends Controller{

    private $global_helper_service;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->global_helper_service = $this->container->get('app.global_helper_service');
    }

    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    function admin_checkValidUser($username, $password)
    {
        $password = $this->encodePassword('MyPass', $password);
        $repository = $this->em->getRepository('AdminCPBundle:AdminAuthenticationEntity');
        $query = $repository->createQueryBuilder('pk')
            ->where('pk.username = :username')
            ->andWhere('pk.password = :password')
            ->andwhere('pk.status = 1')
            ->setParameters(array('username' => $username, 'password'=> $password))
            ->setMaxResults(1)
            ->getQuery();

        $result = $query->getArrayResult();
        if(!empty($result)){
            return $this->global_helper_service->__convert_result_to_object($result);
        }

        return FALSE;
    }

    function admin_getUserByToken($user_token)
    {

        $repository = $this->em->getRepository('AdminCPBundle:AdminAuthenticationEntity');
        $query = $repository->createQueryBuilder('pk')
            ->where('pk.user_token LIKE :user_token')
            ->andwhere('pk.status = 1')
            ->setParameters(array('user_token' => $user_token))
            ->getQuery();

        $result = $query->getArrayResult();
        if(!empty($result)){
            return $this->global_helper_service->__convert_result_to_object($result);
        }

        return FALSE;
    }

    function admin_get_current_user_login(){
        $session = new Session(new PhpBridgeSessionStorage());
        if(!empty($session->get('_userad_authentication'))) {
            $session_user = $session->get('_userad_authentication');

            //get current user
            $get_user = $this->admin_getUserByToken($session_user['token']);
            return $get_user;
        }
        return NULL;
    }


    function admin_onAuthentication($user_data){
        $session = new Session(new PhpBridgeSessionStorage());
        $session->start();

        $firewall = 'secured_userad';
        $token = new UsernamePasswordToken('admin', null, $firewall, array('ROLE_ADMIN'));

        $user = array(
            'username' => $user_data->username,
            'token' => $user_data->user_token,
            'ad_token' => $token,
        );

        $session->set('_security_'.$firewall, serialize($token));
        $session->set('_userad_authentication', $user);
        $session->save();
    }

    function admin_UserSessionLogin(){
        $session = new Session(new PhpBridgeSessionStorage());
        $valid = FALSE;
        if(!empty($session->get('_security_secured_userad'))){
            $valid = TRUE;
        }

        return $valid;
    }

    function admin_CheckValidLogin(){
        if(!$this->admin_UserSessionLogin()){
            header('Location: ' . $this->generateUrl('admincp_login_page'));
            exit();
        }

    }

    function admin_UserAdminInfo(){
        $session = new Session();
        $user = $session->get('_userad_authentication');
        return $user;
    }

    public function encodePassword($raw, $salt)
    {
        return hash('sha256', $salt . $raw); // Custom function for encrypt
    }

    public function _lists_modules_left_theme($parent_id){
        $current_user = $this->admin_get_current_user_login();
        $repository = $this->em->getRepository('AdminCPBundle:AdminSystemModulesEntity');
        $query = $repository->createQueryBuilder('pk');
        $query->select("pk.id, pk.module_name, pk.module_alias");
        $query->where('pk.module_status = 1');
        $query->andWhere('pk.parent_id = :parent_id')->setParameter('parent_id', $parent_id);
        //Just only super admin enough permission access for all modules
        if($current_user->permission_limit == 0){
            $query->andWhere('pk.module_permission = 0');
        }
        $query->orderBy("pk.module_order", 'ASC');
        $results = $query->getQuery()->getResult();

        $html = '';
        if(!empty($results)){
            $html .= '<ul class="submenu">';
            foreach($results as $value){
                $html_menu = $this->_lists_modules_left_theme($value['id']);
                $url_redirect = $value['module_alias'] ? $this->generateUrl($value['module_alias']) : '#';
                $html .='<li class="">';
                    $html .='<a href="' .$url_redirect. '"' .($html_menu ? 'class="dropdown-toggle"' : ''). '>';
                        $html .='<i class="menu-icon fa fa-caret-right"></i>';
                        $html .= $value['module_name'];
                    $html .='</a>';
                    $html .='<b class="arrow ' .($html_menu ? 'fa fa-angle-down' : ''). '"></b>';

                    $html .= $html_menu;

                $html .='</li>';

            }
            $html .= '</ul>';
        }

        return $html;
    }

    public function admin_check_roles_user($module_id, $role_type){
        $valid = FALSE;
        $get_user = $this->admin_get_current_user_login();
        if($get_user){
            $repository = $this->em->getRepository('AdminCPBundle:AdminSystemUsersEntity');
            $query = $repository->createQueryBuilder('pk');
            $query->select("fk.role_type");
            $query->leftJoin("AdminCPBundle:AdminSystemRolesEntity", "fk", "WITH", "pk.role_id=fk.id");
            $query->where('pk.id = :id')->setParameter('id', $get_user->id);
            $query->andwhere('pk.status = 1');
            $result = $query->getQuery()->getArrayResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);

            $result_role_type = unserialize($result[0]['role_type']);
            if(!empty($result_role_type[$module_id])){
                switch($role_type){
                    case 'view':
                        if($result_role_type[$module_id][$role_type]){
                            $valid = TRUE;
                        }
                        break;
                    case 'add':
                        if($result_role_type[$module_id][$role_type]){
                            $valid = TRUE;
                        }
                        break;
                    case 'edit':
                        if($result_role_type[$module_id][$role_type]){
                            $valid = TRUE;
                        }
                        break;
                    case 'delete':
                        if($result_role_type[$module_id][$role_type]){
                            $valid = TRUE;
                        }
                        break;
                    default:
                        break;
                }
            }

            if($get_user->permission_limit == 1){
                $valid = TRUE;
            }
        }

        return $valid;

    }

    public static function __admin_random_token($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes'))
        {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false)
            {
                // throw exception that unable to create random token
            }

            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
        }

        return ;
    }

    public function admin_get_current_module($module_alias, $field = ''){
        $repository = $this->em->getRepository('AdminCPBundle:AdminSystemModulesEntity');
        $query = $repository->createQueryBuilder('pk');
        $query->select("pk");
        $query->where('pk.module_alias LIKE :module_alias')->setParameter('module_alias', $module_alias);
        $result = $query->getQuery()->getArrayResult();
        if(!empty($result)){
            $data =  $this->global_helper_service->__convert_result_to_object($result);
            return !$field ? $data : $data->$field;
        }
        return 0;
    }

}