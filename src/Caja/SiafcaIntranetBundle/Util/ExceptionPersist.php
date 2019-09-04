<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Caja\SiafcaIntranetBundle\Util;

use Exception;
use Caja\SiafcaIntranetBundle\Entity\Excepcion;

/**
 * Description of ExceptionPersist
 *
 * @author cnass
 */
class ExceptionPersist {
    private $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function persistir(Exception $ex){
        $em = $this->container->get('doctrine')->getEntityManager();
        $usr =  $this->container->get('security.token_storage')->getToken()->getUser();
//        var_dump($usr); die();
        $usuario = array();
        $usuario['id'] = $usr->getId();
        $usuario['nombre'] = $usr->getUsername();
//        var_dump($usr->getUsuarioOrganismos()); die();
        $ruta = $this->container->get('request')->get('_route');
        $controlador = $this->container->get('request')->get('_controller');
        $parametros = json_encode($this->container->get('request')->get('_route_params'));
        $fecha = date("Y-m-d H:i:s");
        
        $e = new Excepcion();
        $e->setCodigo((string)$ex->getCode());
        $e->setArchivo($ex->getFile());
        $e->setLinea((string)$ex->getLine());
        $e->setMensaje($ex->getMessage());
        $e->setUsuario(json_encode($usuario));
        $e->setRuta($ruta);
        $e->setControlador($controlador);
        $e->setParametros($parametros);
        $e->setFecha($fecha);
        
//        var_dump($e); die();
        
        $em->persist($e);
        $em->flush($e);
        return null;
    }
}
