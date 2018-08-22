<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/08/2018
 * Time: 14:25
 */

namespace App\Controller;


use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\Partner;
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

        $infos = $em->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository(Partner::class)->findAll();

        return $this->render('page/home.html.twig',array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners));
    }
    public function pageAction(){

    }
}