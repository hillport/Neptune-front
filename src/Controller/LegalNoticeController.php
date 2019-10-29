<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29/10/2019
 * Time: 08:47
 */

namespace App\Controller;


use ScyLabs\NeptuneBundle\Controller\BaseController;
use ScyLabs\NeptuneBundle\Entity\Infos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LegalNoticeController extends BaseController
{
    /**
     * @Route("/legal-notice",name="mention")
     */
    public function legalNotice(Request $request){
        $em = $this->getDoctrine()->getManager();
        $infos = $this->getDoctrine()->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        $pages = $em->getRepository($this->getClass('page'))->findBy(array(
            'parent' => null,
            'remove' => false,
            'active' => true
        ),
            ['prio'=>'ASC']
        );
        $page = $pages[0];
        return $this->render('mention.html.twig',[
            'infos'     =>  $infos,
            'pages'     =>  $pages,
            'page'      =>  $page,
            'locale'    => $request->getLocale()
        ]);
    }
}