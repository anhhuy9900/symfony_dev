<?php
namespace MyApp\CategoriesNews\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoriesNewsController extends Controller
{
    /**
     * @Route("/categories", name="categories_news_page")
     */
    public function indexAction(Request $request)
    {
        dump(1111);die;
        return $this->render();
    }
}
