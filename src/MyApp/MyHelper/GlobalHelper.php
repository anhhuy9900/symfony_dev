<?php
namespace MyApp\MyHelper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use MyApp\AdminCP\Entity\AdminLoginEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class GlobalHelper{

    public static function pr($data, $type = 0) {
        print '<pre>';
        print_r($data);
        print '</pre>';
        if ($type != 0) {
            exit();
        }
    }

    public static function getErrorMessages($errors) {
        $error_message = '';

        if(count($errors) > 0){
            foreach ($errors as $key => $error) {
                $error_message = $error->getMessage();
                break;
            }
        }

        return $error_message;
    }

    public static function __pagination($totalRows, $pageNum = 1, $pageSize, $limit = 3, $current_url = '') {
        settype($totalRows, "int");
        settype($pageSize, "int");
        if ($totalRows <= 0)
            return "";
        $totalPages = ceil($totalRows / $pageSize);
        if ($totalPages <= 1)
            return "";
        $currentPage = $pageNum;
        if ($currentPage <= 0 || $currentPage > $totalPages)
            $currentPage = 1;

        //From to
        $form = $currentPage - $limit;
        $to = $currentPage + $limit;

        //Tinh toan From to
        if ($form <= 0) {
            $form = 1;
            $to = $limit * 2;
        };
        if ($to > $totalPages)
            $to = $totalPages;

        //Tinh toan nut first prev next last
        $first = '';
        $prev = '';
        $next = '';
        $last = '';
        $link = '';

        //Link URL
        $linkUrl = $current_url;

        $get = '';
        $querystring = '';
        if ($_GET) {
            foreach ($_GET as $k => $v) {
                if ($k != 'p')
                    $querystring = $querystring . "&{$k}={$v}";
            }
            $querystring = substr($querystring, 1);
            $get.='?' . $querystring;
        }
        $sep = (!empty($querystring)) ? '&' : '';
        $linkUrl = $linkUrl . '?' . $querystring . $sep . 'p=';

        if ($currentPage > $limit + 2) {
            /** first */
            //$first= "<a href='$linkUrl' class='first'>...</a>&nbsp;";
        }

        /** **** prev ** */
        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            $prev = "<li class='paginate_button previous'><a href='$linkUrl$prevPage' class='prev'> Previous </a></li>";
        }

        /** *Next** */
        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            $next = "<li class='paginate_button next'><a href='$linkUrl$nextPage' class='next'> Next </a></li>";
        }

        /** *Last** */
        if ($currentPage < $totalPages - 4) {
            $lastPage = $totalPages;
            //$last= "<a href='$linkUrl$lastPage' class='last'>...</a>";
        }

        /* * *Link** */
        for ($i = $form; $i <= $to; $i++) {
            if ($currentPage == $i)
                $link.= "<li class='paginate_button active'><a href='javascript:;'>$i</a></li>";
            else
                $link.= "<li class='paginate_button'><a href='$linkUrl$i'>$i</a></li>";
        }

        $pagination = '<div class="dataTables_paginate paging_simple_numbers" id="dynamic-table_paginate"><ul class="pagination">' . $first . $prev . $link . $next . $last . '</ul></div>';

        return $pagination;
    }

}