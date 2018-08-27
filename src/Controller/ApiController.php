<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/08/2018
 * Time: 13:49
 */

namespace App\Controller;


use App\Entity\ContactRequest;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends Controller
{
    /**
     * @Route("api/contact",name="api_contact")
     */
    public function contactAction(Request $request){



        $object = new ContactRequest();
        $form = $this->createForm(ContactType::class , $object,['action'=>$this->generateUrl('api_contact')]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

        }

        return $this->render('api/contact.html.twig',array('form'=>$form->createView()));
    }
}