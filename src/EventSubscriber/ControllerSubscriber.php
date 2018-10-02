<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/09/2018
 * Time: 10:43
 */

namespace App\EventSubscriber;


use App\Controller\ContactController;

use FOS\UserBundle\Mailer\MailerInterface;
use ScyLabs\NeptuneBundle\Controller\FileController;
use ScyLabs\NeptuneBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface ;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent ;
use Symfony\Component\HttpKernel\KernelEvents ;
use Symfony\Component\Routing\RouterInterface;


class ControllerSubscriber implements EventSubscriberInterface
{
    private $container;
    private $router;
    public function __construct(RouterInterface $router,ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    public static function getSubscribedEvents ()
    {
        // return the subscribed events, their methods and priorities
        return array (
            KernelEvents::CONTROLLER => array (
                array ( 'processController' , 10 ),
                array ( 'logController' , 0 ),
            )
        );
    }

    /*
     * Exemples to execute code before the Controllers
     */
    public function processController ( FilterControllerEvent $event )
    {
        /*

        if(! $event->isMasterRequest()){
            return;
        }
        $controller = $event->getController();
        if($controller[0] instanceof PageController || $controller[0] instanceof ContactController ){

            $session = new Session();
            if($session->get('18old') === null || true){

                $url = $this->router->generate('portal');

                $event->setController(function() use ($url){

                    return new RedirectResponse($url);
                });
            }


        }
        */
        return;
    }

    public function logController ( FilterControllerEvent $event )
    {

    }

}