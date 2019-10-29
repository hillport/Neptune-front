<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29/10/2019
 * Time: 08:47
 */

namespace App\Controller;


use ScyLabs\NeptuneBundle\Entity\Infos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LegalNoticeController extends AbstractController
{
    /**
     * @Route("/legal-notice",name="mention")
     */
    public function legalNotice(){
        $infos = $this->getDoctrine()->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        return $this->render('mention.html.twig',[
           'infos'  =>  $infos
        ]);
    }
}