<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 05/09/2018
 * Time: 09:38
 */

namespace App\Controller;


use App\Entity\AtelierRequest;
use App\Entity\ContactRequest;
use App\Form\ContactForm;
use App\Form\AtelierForm;
use Doctrine\Common\Collections\ArrayCollection;
use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
    /**
     * @Route("{_locale}/{contactType}",name="contact",requirements={"slug"="^(?!admin|produit|product)[a-z-_0-9/]+$","_locale"="[a-z]{2}","contactType"="(contact|atelier)" })
     */
    public function contactAction(Request $request,$contactType){

        $em = $this->getDoctrine()->getManager();
        // Récupération d'une page dont le slug est : Contact

        $page = $em->getRepository(Page::class)->findOneBy(array(
            'slug' => $contactType
        ));

        // Si il n'y a pas de page de contact
        if($page === null || $page->getActive() === false){
            return $this->redirectToRoute('homepage');
        }

        // Création du formulaire de contact
        $object = new ContactRequest();
        $form = $this->createForm(ContactForm::class , $object,['action'=>$this->generateUrl('contact',array('_locale'=> $request->getLocale(),'contactType'=>$contactType)),'contactType'=>$contactType]);

        $form->handleRequest($request);

        // Récupération des infos / pages / partenaires

        $pages = $em->getRepository(Page::class)->findBy(array(
            'parent'    =>  null,
            'remove'    =>  false,
        ),
            ['prio'=>'ASC']
        );

        $infos = $em->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository(Partner::class)->findAll();

        $params = array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale());

        // Est-ce que le formulaire est envoyé ? valide ? Et est-ce que on est en POST
        if($form->isSubmitted() && $form->isValid() && $request->isMethod('post')){
            $object = $form->getData();


            // Le client n'a pas cliqué sur => Je ne suis pas un robot
            if(empty($request->get('g-recaptcha-response'))){
                $form->addError(new FormError("S'il vous plait , veuillez cliquer sur le bouton : \"je ne suis pas un robot\""));
            }
            else{

                // Préparation de la requête CURL pour google
                $url = "https://www.google.com/recaptcha/api/siteverify";
                $post = array('secret'=>'6Le41GwUAAAAAH5xkR6Z6sDsoe17GYDyNQHJ0Pbh','response'=>	$_POST['g-recaptcha-response']);
                $options = array(
                    CURLOPT_URL				 => $url,
                    CURLOPT_RETURNTRANSFER 	=>	true,
                    CURLOPT_HEADER			=> false,
                    CURLOPT_POST			=> true,
                    CURLOPT_POSTFIELDS		=> $post,
                );

                $CURL = curl_init();
                if(!empty($CURL)){

                    curl_setopt_array($CURL,$options);
                    $content=curl_exec($CURL);
                    curl_close($CURL);
                    $value = json_decode($content,true);

                    // La requête a bien fonctionné , et le client a bien cliqué sur le captcha
                    if($value['success'] === true || true) {


                        if($infos === null){
                            throw new InvalidConfigurationException("Le controller n'a pas pu récupérer les informations du site.");
                        }

                        $mailer = $this->get('mailer');

                        $mailer->send(
                            (new \Swift_Message('Demande de contact sur votre site internet'.$_SERVER['HTTP_HOST']))
                                ->setFrom('web@e-corses.com')
                                ->setTo($infos->getMail())
                                ->setBody(
                                    $this->get('templating')->render(
                                        'mail/api/contact_webmaster.html.twig',
                                        array(
                                            'infos' => $infos,
                                            'post'=> $_POST

                                        )
                                    )
                                    ,'text/html')
                        );
                        $mailer->send(
                            (new \Swift_Message('Votre demande de contact sur le site : '.$_SERVER['HTTP_HOST']))
                                ->setFrom('web@e-corses.com')
                                ->setTo($object->getEmail())
                                ->setBody(
                                    $this->get('templating')->render(
                                        'mail/api/contact_client.html.twig',
                                        array(
                                            'infos' => $infos,
                                            'post'=> $_POST
                                        )
                                    )
                                    ,'text/html')
                        );


                        $em = $this->getDoctrine()->getManager();
                        $user = $em->getRepository(User::class)->findOneBy(
                            array(
                                'email' => $object->getEmail(),

                            )
                        );
                        $object
                            ->setUser($user)
                            ->setIp($_SERVER['REMOTE_ADDR']);


                        $em->persist($object);
                        $em->flush();
                        $params['form'] = true;


                    }
                    else{
                        $form->addError(new FormError("S'il vous plait , veuillez cliquer sur le bouton : \"je ne suis pas un robot\""));
                    }

                }

            }

        }

        if(!isset($params['form']))
            $params['form'] = $form->createView();

        if($request->isXmlHttpRequest()){

            $arrayResult = array();
            $arrayResult['success'] = true;
            $arrayResult['errors'] = new ArrayCollection();
            $arrayResult["success_message"] = "Votre message a bien été pris en compte";
            $datas = $form->getData();
            foreach ($datas->toArray() as $data ){
                if($form->has($data))   {
                    $input = $form->get($data);

                    if($input->getErrors()->count() > 0)
                    {
                        $arrayResult['success'] = false;
                        $arrayResult['errors']->add(array($data => $input->getErrors()));
                    }
                }
            }

            return $this->json($arrayResult);
        }
        else{
            return $this->render('page/contact.html.twig',$params);
        }

    }
}