<?php
namespace MyApp\News\Controller;

use MyApp\AdminCP\Controller\AdminCPController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use MyApp\News\Entity\NewsEntity;
use MyApp\News\Entity\FilesManagedEntity;
use MyApp\News\Entity\TagsEntity;
use MyApp\News\Validation\AdminNewsValidation;
use MyApp\MyHelper\GlobalHelper;

class AdminNewsController extends AdminCPController
{

    /**
     * Used as constructor
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->data['title'] = 'Admin Manage News';
    }

    /**
     * @Route("/admind/news", name="admincp_news_page")
     */
    public function indexAction(Request $request)
    {
        $key = $request->query->get('key') ? GlobalHelper::__xss_clean_string($request->query->get('key')) : '';

        $arr_order = $request->query->get('order') ? GlobalHelper::__handle_param_order_in_url($request->query->get('order')) : array('field'=>'id', 'by'=>'DESC');

        $date_range = $request->query->get('date_range') ? GlobalHelper::__handle_param_date_range_in_url($request->query->get('date_range')) : array();

        $limit = $request->query->get('lm') ? (int)$request->query->get('lm') : 10;
        $page_offset = $request->query->get('p') ? (int)$request->query->get('p') : 0;
        $offset = $page_offset > 0 ? ($page_offset - 1) * $limit : $page_offset * $limit;

        $repository = $this->getDoctrine()->getRepository('NewsBundle:NewsEntity');
        $total = $repository->_getTotalRecords($key);
        $results = $repository->_getListRecords($limit, $offset, array('key' => $key, 'date_range' => $date_range), $arr_order);

        if($request->query->get('report')){
            $this->_report_data($results);
        }

        $pagination = GlobalHelper::__pagination($total, $page_offset, $limit, 3, $this->generateUrl('admincp_news_page'));

        $this->data['results'] = $results;
        $this->data['pagination'] = $pagination;

        return $this->render('@admin/news/list.html.twig', $this->data);
    }

    /**
     * @Route("/admind/news/create", name="admincp_news_create_page")
     */
    public function createAction(Request $request)
    {
        $id = 0;
        $handle_data = self::handle_form_data($id, $request);

        if($handle_data['success']){
            $request->getSession()->getFlashBag()->add('message_data', 'Created record success!');
            $url = $this->generateUrl('admincp_news_page');
            return $this->redirect($url, 301);
        }

        $this->data['form'] = $handle_data['form']->createView();
        $this->data['form_errors'] = $handle_data['form_errors'];
        $this->data['fields_value'] = $handle_data['fields_value'];
        $this->data['list_galleries'] = $handle_data['list_galleries'];
        $this->data['list_tags'] = $handle_data['list_tags'];

        return $this->render('@admin/news/edit.html.twig', $this->data);

    }

    /**
     * @Route("/admind/news/edit/{id}", name="admincp_news_edit_page")
     */
    public function editAction($id, Request $request)
    {
        $handle_data = self::handle_form_data($id, $request);

        if($handle_data['success']){
            $request->getSession()->getFlashBag()->add('message_data', 'Updated record success!');
            $url = $this->generateUrl('admincp_news_page');
            return $this->redirect($url, 301);
        }

        $this->data['form'] = $handle_data['form']->createView();
        $this->data['form_errors'] = $handle_data['form_errors'];
        $this->data['fields_value'] = $handle_data['fields_value'];
        $this->data['list_galleries'] = $handle_data['list_galleries'];
        $this->data['list_tags'] = $handle_data['list_tags'];

        return $this->render('@admin/news/edit.html.twig', $this->data);
    }

    /**
     * @Route("/admind/news/delete/{id}", name="admincp_news_delete_page")
     */
    public function deleteAction($id , Request $request)
    {
        if($id > 0){
            $em = $this->getDoctrine()->getEntityManager();
            $check_exist_record = $em->getRepository('NewsBundle:NewsEntity')->find($id);
            if($check_exist_record){
                $em->getRepository('NewsBundle:NewsEntity')->_delete_record_DB($id);

                $request->getSession()->getFlashBag()->add('message_data', 'Deleted record success!');
            }

            $url = $this->generateUrl('admincp_news_page');
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
        $result_data = $em->getRepository('NewsBundle:NewsEntity')->find($id);

        $fields_value = array(
            'id' => ( $id ? $id : 0 ),
            'category_id' => ( $result_data ? $result_data->getCategoryID() : 0 ),
            'tag_id' => ( $result_data ? $result_data->getTagID() : 0 ),
            'title' => ( $result_data ? $result_data->getTitle() : '' ),
            'image' => ( $result_data ? $result_data->getImage() : '' ),
            'description' => ( $result_data ? $result_data->getDescription() : '' ),
            'content' => ( $result_data ? $result_data->getContent() : '' ),
            'status' => ( $result_data ? $result_data->getStatus() : 0 )
        );


        //Get list galleries
        $list_galleries = $this->global_service->__get_list_galleries($id, 'news');

        //Get list tags
        $list_tags = $em->getRepository('NewsBundle:NewsEntity')->_getListTagsNews($id, 'news');

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            //->setAction($this->generateUrl('admincp_news_edit_page'))
            ->add('id', HiddenType::class, array(
                'data' => $fields_value['id'],
            ))
            ->add('category_id', ChoiceType::class, array(
                'label' => 'Category ID',
                'choices' =>array(0 => 'Category Tag ID'),
                'data' => $fields_value['category_id']
            ))
            /*->add('tag_id', ChoiceType::class, array(
                'label' => 'Tag ID',
                'choices' =>array(0 => 'Select Tag ID'),
                'data' => $fields_value['tag_id']
            ))*/
            ->add('title', TextType::class, array(
                'label' => 'Title',
                'data' => $fields_value['title']
            ))
            ->add('image', FileType::class, array(
                'label' => 'Image',
                'required' => FALSE
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
                'data' => $fields_value['description']
            ))
            ->add('content', TextareaType::class, array(
                'label' => 'Content',
                'data' => $fields_value['content']
            ))
            ->add('status', ChoiceType::class, array(
                'label' => 'Status',
                'data' => $fields_value['status'],
                'choices' => array( 0 => 'Unpblish', 1 => 'Publish')
            ))
            ->add('tags', TextType::class, array(
                'label' => 'Tags input',
                'data' =>  '',
                'required' => FALSE
            ))
            ->add('lists_thumb', TextareaType::class, array(
                'data' => (!empty($list_galleries) ? json_encode($list_galleries) : ''),
                'required' => FALSE
            ))
            ->add('lists_del_file', TextareaType::class, array(
                'data' => '',
                'required' => FALSE
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Submit',
            ))
            ->getForm();

        $form->handleRequest($request);

        $form_errors = '';
        $success = FALSE;
        if ($form->isSubmitted() && $form->isValid()) {
            $validation = new AdminNewsValidation();

            $data = $form->getData();
            $validation->title = $data['title'];
            $validation->description = $data['description'];
            $validation->content = $data['content'];

            $validator = $this->get('validator');
            $errors = $validator->validate($validation);

            $form_errors = GlobalHelper::getErrorMessages($errors);
            if(!$form_errors){

                //Upload image
                $service = $this->container->get('app.upload_files_service');
                $data['image'] = $service->__upload_file_request($data['image'],'news');

                if($data['id'] > 0){
                    /* Update record */
                    $id = $em->getRepository('NewsBundle:NewsEntity')->_update_record_DB($data);
                } else {
                    /* Create new record */
                    $id = $em->getRepository('NewsBundle:NewsEntity')->_create_record_DB($data);
                }

                /* handle gallery images */
                //create new files
                if(!empty($data['lists_thumb'])){
                    $files_gallery = json_decode($data['lists_thumb']);
                    foreach ($files_gallery as $key => $value) {
                        $this->__save_files_data($id, 'news', $value->file);
                    }
                }

                //delete new files
                if(!empty($data['lists_del_file'])){
                    $lists_del_file = json_decode($data['lists_del_file']);
                    foreach ($lists_del_file as $key_del_file => $del_file) {
                        $this->__delete_files_data($id, 'news', $del_file->id);
                    }
                }
                /* End handle gallery images */

                /* handle tags */
                $this->_handle_tags_new($id, 'news', $data['tags']);
                /* End handle tags */


                $success = TRUE;
            }
        }

        $handle_data = array(
            'form' => $form,
            'form_errors' => $form_errors,
            'success' => $success,
            'fields_value' => $fields_value,
            'list_galleries' => $list_galleries,
            'list_tags' => (!empty($list_tags)) ? json_encode($list_tags): array()
        );

        return $handle_data;
    }

    /**
     * Report data into file excel
     */
    private function _report_data($arrData = array())
    {

        $file_name = 'List-Modules-' . date('Ymd') . '.xlsx';

        // Create excel file
        $header = array();
        $header[] = 'ID';
        $header[] = 'NTitle';
        $header[] = 'Description';
        $header[] = 'Content';
        $header[] = 'Status';
        $header[] = 'Created Date';

        $data['headers'] = $header;

        $rows = array();
        if(!empty($arrData)){
            foreach($arrData as $key => $value) {
                $tmp = array();
                $tmp[] = $value->getID();
                $tmp[] = $value->getTitle();
                $tmp[] = $value->getDescription();
                $tmp[] = $value->getContent();
                $tmp[] = $value->getStatus() == 1 ? 'Active' : 'UnActive';
                $tmp[] = date('Y-m-d H:i:s',$value->getCreated_Date());

                $rows[] = $tmp;
            }
            $data['rows'] = $rows;
            GlobalHelper::__export_to_excel($data,$file_name);
        }
    }

    /**
     * This function use save file to database
     */
    private function __save_files_data($type_id, $type = 'default', $file = '')
    {
        $service = $this->container->get('app.upload_files_service');
        if($file && file_exists($this->getParameter('upload_dir').'/'.$file)){
            $gallery_name = $type.'_gallery';

            $entity = $this->getDoctrine()->getEntityManager()->getRepository('NewsBundle:FilesManagedEntity');
            $query = $entity->createQueryBuilder('pk');
            $query->select("pk");
            $query->where('pk.type = :type');
            $query->andWhere('pk.type_id = :type_id');
            $query->andWhere('pk.file = :file');
            $query->setParameter('type', $type);
            $query->setParameter('type_id', $type_id);
            $query->setParameter('file', $file);
            $get_file_exists = $query->getQuery()->getResult();

            if(empty($get_file_exists)) {
                $file_gallery = $service->__creat_folder_upload($gallery_name);
                $file_gallery_name = $service::__random_file_name(15).'_'.rand(11111,99999).time().'.jpg';
                $newfile = $file_gallery['path_url'].$file_gallery_name;

                copy($this->getParameter('upload_dir').'/'.$file, $file_gallery['folder_path'].$file_gallery_name);
                unlink($this->getParameter('upload_dir').'/'.$file);

                //Create file in database
                $create = new FilesManagedEntity();
                $create->setTypeID($type_id);
                $create->setType($type);
                $create->setFile($newfile);
                $create->setStatus(1);
                $create->setCreated_Date(time());
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($create);
                $em->flush();

                return TRUE;

            }
        }

        return FALSE;
    }

    /**
     * This function use delete file to database
     */
    public function __delete_files_data($type_id, $type = 'default', $file_id = 0){

        if(!empty($file_id)){

            $entity = $this->getDoctrine()->getEntityManager()->getRepository('NewsBundle:FilesManagedEntity');
            $query = $entity->createQueryBuilder('pk');
            $query->select("pk");
            $query->where('pk.type = :type');
            $query->andWhere('pk.type_id = :type_id');
            $query->andWhere('pk.id = :id');
            $query->setParameter('type', $type);
            $query->setParameter('type_id', $type_id);
            $query->setParameter('id', $file_id);
            $get_file = $query->getQuery()->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

            if(!empty($get_file)) {
                $get_file = (object)$get_file;
                if(file_exists($this->getParameter('upload_dir').'/'.$get_file->file)){
                    unlink($this->getParameter('upload_dir').'/'.$get_file->file);
                }
                $entity_delete = $entity->findOneBy(array('id' => $file_id));
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($entity_delete);
                $em->flush();

                return TRUE;
            }
        }

        return FALSE;

    }

    /**
     * This function use create and update atgs for each news
     */
    public function _handle_tags_new($type_id, $type = 'default', $tags = ''){

        if($tags){
            $entity = $this->getDoctrine()->getRepository('NewsBundle:TagsEntity');
            $list_tags = explode(',', $tags);
            if(!empty($list_tags)){
                foreach($list_tags as $tag){
                    $query = $entity->createQueryBuilder('pk');
                    $query->select("pk");
                    $query->where('pk.type = :type');
                    $query->andWhere('pk.type_id = :type_id');
                    $query->andWhere('pk.tag_name = :tag_name');
                    $query->setParameter('type', $type);
                    $query->setParameter('type_id', $type_id);
                    $query->setParameter('tag_name', $tag);
                    $get_tag_exists = $query->getQuery()->getResult();

                    if(empty($get_tag_exists)) {

                        //Create tag in database
                        $create = new TagsEntity();
                        $create->setTypeID($type_id);
                        $create->setType($type);
                        $create->setTag_Name($tag);
                        $create->setStatus(1);
                        $create->setCreated_Date(time());
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($create);
                        $em->flush();

                    }
                }
            }

            //delete tag if it isn't exists in list atgs
            $query = $entity->createQueryBuilder('pk');
            $query->select("pk");
            $query->where('pk.type = :type');
            $query->andWhere('pk.type_id = :type_id');
            $query->andWhere($query->expr()->notIn('pk.tag_name', ':list_tags'));
            $query->setParameter('type', $type);
            $query->setParameter('type_id', $type_id);
            $query->setParameter('list_tags', $list_tags);
            $list_tags_delete = $query->getQuery()->getResult();
            if(!empty($list_tags_delete)) {
                foreach ($list_tags_delete as $tag) {
                    $entity_delete = $entity->findOneBy(array('id' => $tag->getID()));
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->remove($entity_delete);
                    $em->flush();
                }
            }

            return TRUE;
        }

        return FALSE;
    }
}
