<?php
namespace MyApp\Twig;

class AppExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('__strpos', array($this, '__strposFilter')),
            new \Twig_SimpleFilter('__getLinkOrder', array($this, '__getLinkOrderFilter')),
            new \Twig_SimpleFilter('__vardump', array($this, '__vardumpFilter')),
        );
    }

    public function __strposFilter($value, $type='')
    {
        return strpos($value, $type);
    }

    public function __vardumpFilter($value, $type = 0)
    {
        print '<pre>';
        print_r($value);
        print '</pre>';
        if($type){
            die();
        }
    }

    public function __getLinkOrderFilter($url, $field_type = 'id|DESC')
    {

        $filter_order = $field_type;
        if($this->__strposFilter($url, $field_type)){
            if($this->__strposFilter($url, 'order=')){
                $replace = $this->__strposFilter($url, '?order=') ? '?order='.$field_type : '&order='.$field_type;
                $url = str_replace($replace, '', $url);
            }
            $ex_field = explode('|', $field_type);

            if(!empty($filter_order)){
                if($ex_field[1] == 'DESC'){
                    $filter_order = $ex_field[0] . '|' .'ASC';
                }else{
                    $filter_order = $ex_field[0] . '|' .'DESC';
                }
            }

        }
        $filter_url = $this->__strposFilter($url, '?') ? $url.'&order=' : $url.'?order=';
        $handle_url = $filter_url . $filter_order;

        return $handle_url;
    }

    public function getName()
    {
        return 'app_extension';
    }
}