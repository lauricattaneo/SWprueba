<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * 
 * IntraApoInd - indexAction
 *
 */

class AportanteController extends Controller
{
    //TO-DO CONTINUAR MODULARIZANDO EL METODO, QUEDO MUYY LARGO
    public function indexAction(Request $request, Organismo $organismoId)
    {
        //obtengo el id del organismo 
        $idOrg = $organismoId->getId();
        
        $ayuda = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('SiafcaIntranetBundle:Ayuda')
                    ->findOneBy(array('codigo' => 'IntraApoIndex'));

        //Creo el formulario
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        $form->handleRequest($request);
        
        //defino bandera como true para que se muestre el boton <buscar>
        //si bandera es false, se muestra boton <volver>
        $bandera = true;
        
        //obtengo todos los aportantes
        $aportantes = $this->obtenerTodosAportantesAction($idOrg, $request);
        
        if($form->isValid() && $form->isSubmitted())
        {
            $dato = $form->get('busqueda')->getData();
            //segun el dato que viene del select de opciones
            //se busca por apellido or cuil or documento
            if($dato == 'apellido')
            {
                //
                $aportantes = 
                        $this->
                        buscarApellidoNombreAction
                            ($idOrg,
                                $this->formateoTexto($form->get('campoApellido')->getData()),
                                $this->formateoTexto($form->get('campoNombre')->getData()),
                                $request
                            );
            }
            else if($dato == 'documento')
            {
                //obtengo los aportantes
                $aportantes = 
                        $this->
                        buscarDocumentoAction($idOrg,
                                              trim($form->get('campoDni')->getData()), 
                                              $request);
            }
            else if($dato == 'cuil')//funciona
            {
                //obtengo los aportantes
                $aportantes = $this->
                              buscarCuilAction($idOrg,
                                               trim($form->get('campoCuil')->getData()),
                                               $request);
            }
            //cambio bandera y ayuda
            $bandera = false;
            $ayuda   = null;
        }
        
        //retorno
        return $this->render('aportante/index.html.twig', 
                array
                (
                    'aportantes' =>  $aportantes,
                    'subtitle'   =>  $organismoId->getNombre(),
                    'form'       =>  $form->createView(),
                    'bandera'    =>  $bandera,
                    'subtitulo'  =>  $organismoId->getNombre(),
                    'titulo'     =>  'Aportantes',
                    'orgId'      =>  $organismoId->getId(),
                    'actualOnly' =>  ((is_null($request->query->get('actualOnly')))? false : true),
                    'ayuda'      =>  $ayuda
                ));
    }
    
    /*
     * metodo para obtener todos los aportantes
     */
    private function obtenerTodosAportantesAction($idOrg,$request)
    {
        //uso el repositorio para obtener los aportantes asociados al organismo
        $resultado = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('SiafcaIntranetBundle:Organismo')
                    ->obtenerAportantes($idOrg,$request);
        
        return $this->obtenerPaginacion($request, $resultado);
    }
        
   /**
    * metodo para buscar los aportantes por cuil
    */
    private function buscarCuilAction($idOrg,$cuil,$request)
    {
        //obtengo los aportantes asociados al organismo
        $resultado =  
                $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('SiafcaIntranetBundle:Organismo')
                    ->obtenerAportantesCuil($idOrg,$cuil)
                ;
        
       return $this->obtenerPaginacion($request, $resultado);
    }
    
    /*
     * metodo para buscar por dni los aportantes asociados a un organismo
     * @param idOrg id del organismo
     * @param documento
     * @param request lo usa el paginador
     */
    private function buscarDocumentoAction($idOrg,$documento,$request)
    {
        //obtengo los aportantes asociados al organismo
        $resultado =
                $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('SiafcaIntranetBundle:Organismo')
                    ->obtenerAportantesDocumento($idOrg,$documento)
                ;
        return $this->obtenerPaginacion($request, $resultado);
    }
    
    private function buscarApellidoNombreAction($idOrg,$apellido,$nombre,$request)
    {
         //obtengo los aportantes asociados al organismo
        $resultado =
                $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('SiafcaIntranetBundle:Organismo')
                    ->obtenerAportantesApellidoNombre($idOrg,$apellido,$nombre)
                ;
        
        return $this->obtenerPaginacion($request, $resultado);
    }

    /*
     * metodo para obtener la paginacion
     */
    private function obtenerPaginacion($request,$resultado)
    {
        $paginator = $this->get('knp_paginator');
        $aportantes = $paginator->paginate
            (
                $resultado,
                $request->query->getInt('page', 1),
                20
                ,
                array('defaultSortFieldName' => 'p.apellidoPat', 'defaultSortDirection' => 'asc')
            );
            
        return $aportantes;
    }
    
    /*
     * metodo para eliminar espacios en blanco y si viene vacio se asigna null
     */
    public function formateoTexto($ingreso)
    {
        $aux = trim($ingreso);
        
        //si el dato viene vacio 
        //se le asigna un valor para que se 
        //pueda generar la ruta, sino tira un throw exepction
                
        $salida = $aux == '' ? 'null' : $aux;
        return $salida;
    }
}
