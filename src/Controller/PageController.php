<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/08/2018
 * Time: 14:25
 */

namespace App\Controller;


use App\Entity\ContactRequest;
use App\Form\ContactForm;
use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageUrl;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends Controller
{
    /**
     * @Route("/{_locale}",name="homepage",requirements={"_locale"="[a-z]{2}"},defaults={"_locale"="fr"})
     */
    public function homeAction(Request $request){

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

        $params = array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale());
        return $this->render('page/home.html.twig',$params);
    }

    /**
     * @Route("{_locale}/contact",name="contact",requirements={"slug"="^(?!admin|produit|product)[a-z-_0-9/]+$","_locale"="[a-z]{2}"})
     */
    public function contactAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        // Récupération d'une page dont le slug est : Contact

        $page = $em->getRepository(Page::class)->findOneBy(array(
            'slug' => 'contact'
        ));

        // Si il n'y a pas de page de contact
        if($page === null || $page->getActive() === false){
            return $this->redirectToRoute('homepage');
        }

        // Création du formulaire de contact
        $object = new ContactRequest();
        $form = $this->createForm(ContactForm::class , $object,['action'=>$this->generateUrl('contact',array('_locale'=> $request->getLocale()))]);


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
                    if($value['success'] === true) {


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

        return $this->render('page/contact.html.twig',$params);
    }


    /**
     * @Route("/{_locale}/{slug}",name="page",requirements={"slug"="^(?!admin)[a-z-_0-9/]+$","_locale"="[a-z]{2}"})
     */
    public function pageAction(Request $request,$slug){
        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository(PageUrl::class)->findOneBy(array(
            'url' => $slug
        ));

        if($url === null)
            return $this->redirectToRoute('homepage');

        if($url->getLang() !== $request->getLocale()){
            $url = $em->getRepository(PageUrl::class)->findOneBy(
                array(
                    'lang'  => $request->getLocale(),
                    'page'  => $url->getPage()
                )
            );
            if($url === null){
                return $this->redirectToRoute('homepage');
            }
            return $this->redirectToRoute('page',array('_locale'=>$request->getLocale(),'slug' => $url->getUrl()));
        }


        $page = $url->getPage();
        if($page->getActive() === false){
            return $this->redirectToRoute('homepage');
        }

        if($page->getType()->getName() == 'contact'){
            return $this->redirectToRoute('contact');
        }

        $pages = $em->getRepository(Page::class)->findBy(array(
            'parent'    =>  null,
            'remove'    =>  false,
            ),
            ['prio'=>'ASC']
        );


        $infos = $em->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository(Partner::class)->findAll();

        $params = array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale());



        if(file_exists($this->getParameter('kernel.project_dir').'/templates/page/'.$page->getType()->getName().'.html.twig')){
            return $this->render('page/'.$page->getType()->getName().'.html.twig',$params);
        }

        return $this->render('page/page.html.twig',$params);
    }


}