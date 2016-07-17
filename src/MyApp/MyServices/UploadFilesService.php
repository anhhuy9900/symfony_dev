<?php
namespace MyApp\MyServices;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;


class UploadFilesService extends Controller{

    /**
     * Used as constructor
     */

    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __get_path_folder_upload(){
        return $this->get('request')->getBasePath() . '/uploads/';
    }

    public function __upload_file_request($file, $type_name){

        $extension = $file->guessExtension(); // getting image extension
        $fileName = $type_name.'_'.rand(11111,99999).time().'.jpg';
        $var_path = $this->__creat_folder_upload($type_name);

        $file->move( $var_path['folder_path'], $fileName);
        $new_file = $var_path['path_url'].$fileName;

        return $new_file;
    }


    public function __creat_folder_upload($folder_name = 'images') {
        //dump($this->container);die();
        $uploadDir = $this->getParameter('upload_dir');
        $path_url = $folder_name.'/'.date('Y').'/'.date('m').'/'.date('d').'/';
        $folder_path = $uploadDir .'/'.$path_url;
        $folder = self::__Newfolder($folder_path);

        $data  = array(
            'folder_path' => $folder_path,
            'path_url' => $path_url,
        );
        return $data;
    }


    public static function __Newfolder($folder) {
        $fs = new Filesystem();
        $arr_folder = explode('/', $folder);
        $fol = '';
        foreach ($arr_folder as $row) {
            if (!empty($row)) {
                $fol.=$row . '/';
                if (!file_exists($fol)) {
                    $fs->mkdir($fol, 0777);
                } else {
                    if ($row != 'static') {
                        $mod = substr(sprintf('%o', fileperms($fol)), -4);
                        if ($mod != 0777) {
                            $fs->mkdir($fol, 0777);
                        }
                    }
                }
            }
        }
    }

    static function __random_file_name($length = 10) {
        $allowed_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $allowed_chars_len = strlen($allowed_chars);

        if($allowed_chars_len == 1) {
            return str_pad('', $length, $allowed_chars);
        } else {
            $result = '';

            while(strlen($result) < $length) {
                $result .= substr($allowed_chars, rand(0, $allowed_chars_len), 1);
            } // while

            return $result;
        }
    }
}