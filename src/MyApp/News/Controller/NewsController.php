<?php
namespace MyApp\News\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    /**
     * @Route("/news", name="newspage")
     */
    public function indexAction(Request $request)
    {
        dump(1111);die;
        return $this->render();
    }
}
