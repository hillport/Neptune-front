<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/08/2018
 * Time: 13:49
 */

namespace App\Controller;


use ScyLabs\NeptuneBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/downloads/{id}/{name}",name="api_download",requirements={"id"="\d+","name"=".*"})
     */
    public function downloadAction(Request $request,$id,$name,$ext){

        $doc = $this->getDoctrine()->getRepository(Document::class)->find($id);

        if($request->headers->has('referer'))
            $redirect =  $this->redirect($request->headers->get('referer'));
        else
            $redirect =  $this->redirectToRoute('homepage');

        if($doc === null){
            return $redirect;
        }

        if(!file_exists($this->getParameter('uploads_directory').$doc->getPath())){
            return $redirect;
        }
        $path = $this->getParameter('uploads_directory').$doc->getPath();

        return $this->file($path,$name,ResponseHeaderBag::DISPOSITION_INLINE);

    }

}