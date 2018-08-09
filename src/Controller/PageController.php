<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/08/2018
 * Time: 14:25
 */

namespace App\Controller;


use ScyLabs\NeptuneBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends Controller
{
    /**
     * @Route("/",name="homepage")
     */
    public function homeAction(){

        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository(Page::class)->findBy(array(
            'parent' => null,
            'remove' => false
            ),
            ['prio'=>'ASC']
        );
        $page = $pages[0];

        return $this->render('page/page.html.twig',array('pages'=>$pages,'page'=>$page));
    }
    public function pageAction(){

    }
}