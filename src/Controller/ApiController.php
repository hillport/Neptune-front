<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/08/2018
 * Time: 13:49
 */

namespace App\Controller;


use ScyLabs\NeptuneBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends Controller
{
    /**
     * @Route("/downloads/{name}{id}.{ext}",name="api_download",requirements={"id"="\d+","name"=".*"})
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

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $name.$id.'.'.$ext);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));

        return new Response(readfile($path));
    }

}