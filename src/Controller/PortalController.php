<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/09/2018
 * Time: 11:25
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class PortalController extends Controller
{
    /**
     @Route("/{_locale}",defaults={"_locale"="fr"},name="portal")
     */
    public function portalAction(){
        $session = new Session();
        if($session->get('18old')){
            // return $this->redirectToRoute('toto')
        }
        return new Response('Je suis un portail');
    }

    /**
     * @Route("/valid_old",name="valid_old")
     */
    public function oldAction(){
        $session = new Session();
        $session->set('18old',true);
        return $this->redirectToRoute('page',array(
            
        ));
    }
}