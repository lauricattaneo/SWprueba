<?php

namespace Caja\SiafcaIntranetBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Description of ForzarCambioPasswordListener
 *
 * @author administrador
*/
class EventListener {
    private $securityContext;
    private $router;
    private $encoderFactory;
    
    public function __construct( SecurityContext $securityContext, Router $router, EncoderFactory $encoderFactory ) {
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->encoderFactory = $encoderFactory;
    }
    
    /**
     * Este evento se invoca en cada request de la aplicación
     *
    */
    public function onRequest(GetResponseEvent $event) {

        if ( ($this->securityContext->getToken() ) && ( $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY') ) ) {

            $route_name = $event->getRequest()->get('_route');

            $user = $this->securityContext->getToken()->getUser();

            $encoder = $this->encoderFactory->getEncoder('Caja\SiafcaIntranetBundle\Entity\Usuario');

            $pass_inicial = $encoder->encodePassword( $user->getPassword(), $user->getSalt());

            // ¿es password1 igual a password2?
            $bool = StringUtils::equals($user->getPassword(), $pass_inicial);

//            if ($bool) {
//                if ($route_name != 'changepass_usuario') {                                                                                                 
//                    $response = new RedirectResponse($this->router->generate('changepass_usuario',array('id' => $user->getId())));                    
//                    $event->setResponse($response);                                               
//                }
//            }

        } 
    }
}