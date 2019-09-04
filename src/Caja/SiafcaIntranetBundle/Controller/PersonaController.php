<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Caja\SiafcaIntranetBundle\Entity\Persona;
use Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType;
use Symfony\Component\Form\Form;
use Caja\SiafcaIntranetBundle\Entity\Domicilio;
use Caja\SiafcaIntranetBundle\Entity\Familiar;

/**
 * Persona controller.
 * AYUDA:
 * IntraPerIndex - indexAction
 * IntraPerNew - newAction
 * IntraPerShow - showAction
 * IntraPerEdit - editAction
 * IntraPerApoH - historicoAportanteAction
 * IntraPerFirH - historicoFirmanteAction
 *
 */
class PersonaController extends Controller
{
    /**
     * Lists all Persona entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraPerIndex'));

        //Creo el formulario
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        $form->handleRequest($request);
        
        if($form->isValid() && $form->isSubmitted())
        {
            $dato = $form->get('busqueda')->getData();
            //segun el dato que viene del select de opciones
            //se busca por apellido or cuil or 
            if($dato == 'apellido')
            {
                $apellido = trim($form->get('campoApellido')->getData());
                $nombre = trim($form->get('campoNombre')->getData());
                
                if(empty($apellido) && empty($nombre)) {
                    $this->addFlash(
                            'error', // error, info, success
                            'Debe ingresar el apellido y/o nombre de la persona que desea buscar.'
                    );
                return $this->redirectToRoute('persona_index');
                }
                
                //si el nombre viene vacio (o el apellido)
                //se le asigna un valor para que se 
                //pueda generar la ruta, sino tira un throw exepction
                
                $Nombre   = $nombre   == '' ? 'null' : $nombre;
                $Apellido = $apellido == '' ? 'null' : $apellido;
                
                //
                return $this->
                        redirectToRoute
                            ('persona_buscarApellido',
                             array('apellido' => $Apellido,
                                   'nombre'   => $Nombre   
                            ));
            }
            else if($dato == 'documento')
            {
                $documento = trim($form->get('campoDni')->getData());
                // si el documento viene vacio le pido que ingrese
                if(empty($documento)) {
                    $this->addFlash(
                            'error', // error, info, success
                            'Debe ingresar el Nº de documento que desea buscar.'
                    );
                return $this->redirectToRoute('persona_index');
                }
                //
                return $this->redirectToRoute('persona_buscarDni', array('documento' => $documento));
            }
            else if($dato == 'cuil')
            {
                $cuil = trim($form->get('campoCuil')->getData());
                // si el cuil viene vacio le pido que lo ingrese
                if(empty($documento)) {
                    $this->addFlash(
                            'error', // error, info, success
                            'Debe ingresar el Nº de documento que desea buscar.'
                    );
                return $this->redirectToRoute('persona_index');
                }
                //
                return $this->redirectToRoute('persona_buscarCuil', array('cuil' => $cuil));
            }
        }
        
        // obtengo del repositorio el listado
        $resultado = $this->getDoctrine()
                          ->getManager()
                          ->getRepository('SiafcaIntranetBundle:Persona')
                          ->obtenerIndex();

        
        $personas = 
            $this->get('paginacion')->obtenerPaginacion
                (
                    $request,
                    $resultado/*page number*/,
                    20, /*limit per page*/
                    array('defaultSortFieldName' => 'ord_nombre', 'defaultSortDirection' => 'asc')
                );
        return $this->render('persona/index.html.twig', 
                array(
                        'personas' => $personas,
                        'form'     => $form->createView(),
                        'ayuda'    => $ayuda,
                        'bandera'  => true
                    ));
    }
       
    public function buscarPersonaDocumentoAction(Request $request,$documento)
    {
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        
        $resultado = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('SiafcaIntranetBundle:Persona')
                        ->findBy(array('documento'=>$documento));
        
        $form->handleRequest($request);
        if(!$resultado)
        {
            $this->addFlash(
                    'error', // error, info, success
                    'No se encontró una persona asociada al documento ingresado.'
            );
            return $this->redirectToRoute('persona_index');
        }
        
        // armo la paginacion
        $personas = 
            $this->get('paginacion')->obtenerPaginacion
                (
                    $request,
                    $resultado/*page number*/,
                    20, /*limit per page*/
                    array('defaultSortFieldName' => 'ord_nombre', 'defaultSortDirection' => 'asc')
                );
        
        return $this->render('persona/index.html.twig', array(
                'personas' => $personas,
                'form' => $form->createView(),
                'bandera'=> false
                ));
    }
    
    public function buscarPersonaApellidoAction(Request $request,$apellido,$nombre)
    {
        if($apellido == 'null' && $nombre == 'null')
        {
            $resultado = null;
        }
        else
        {
            //obtengo el resultado usando el repositorio
            $resultado = $this->getDoctrine()
                              ->getManager()
                              ->getRepository('SiafcaIntranetBundle:Persona')
                              ->buscarApellido($apellido,$nombre);
        }
        
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        $form->handleRequest($request);
        //si falla la busqueda o no encuentra nada retorna false
        if($resultado == false)
        {
            //muestro un mensaje flash con el error y vuelvo al index
            $this->addFlash
                (
                    'error', // error, info, success
                    'No se encontró una persona asociada al nombre y apellido.'
                );
            return $this->redirectToRoute('persona_index');
        }

        // armo la paginacion
        $personas = 
            $this->get('paginacion')->obtenerPaginacion
                (
                    $request,
                    $resultado/*page number*/,
                    20, /*limit per page*/
                    array('defaultSortFieldName' => 'ord_nombre', 'defaultSortDirection' => 'asc')
                );
        
        //muesto los resultados
        return $this->render('persona/index.html.twig', 
                array
                (
                    'personas' => $personas,
                    'form' => $form->createView(),
                    'bandera'=> false
                ));
    }

    public function buscarPersonaCuilAction(Request $request,$cuil)
    {
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        
        
       //obtengo el resultado usando el repositorio
        $resultado = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('SiafcaIntranetBundle:Persona')
                            ->findBy(array('cuil'=>$cuil));
        
        //
        if(!$resultado)
        {
            $form->handleRequest($request);
            $this->addFlash(
                    'error', // error, info, success
                    'No se encontró una persona asociada al cuil ingresado.'
            );
            return $this->redirectToRoute('persona_index');
        }
        
        // armo la paginacion
        $personas = 
            $this->get('paginacion')->obtenerPaginacion
                (
                    $request,
                    $resultado/*page number*/,
                    20, /*limit per page*/
                    array('defaultSortFieldName' => 'ord_nombre', 'defaultSortDirection' => 'asc')
                );
                
        return $this->render('persona/index.html.twig', array(
                'personas' => $personas,
                'form' => $form->createView(),
                'bandera'=> false
                ));
    }
   
    /**
     * Creates a new Persona entity.
     *
     */
    public function newAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraPerNew'));
        $persona = new Persona();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\PersonaType', $persona);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nombre = strtoupper ($persona->getNombre());
            $apellido = strtoupper ($persona->getApellidoPat());
            $persona->setNombre($nombre); 
            $persona->setApellidoPat($apellido); 
            $em->persist($persona);
            $em->flush();
            return $this->redirectToRoute('persona_show', array(
                'id' => $persona->getId(),
            ));
        }

        return $this->render('persona/edit.html.twig', array(
            'persona' => $persona,
            'form' => $form->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Obtiene los datos de la persona a mostrar.
     * Ya que es una vista donde se muestran todos los datos de la persona, se filtran sus
     * apariciones como firmante y aportante de organismo de forma que se muestren solo
     * los vigentes en la actualidad. (Puede ser una lista demasiado larga)
     * El usuario podra ver detalles de estos listados ingresando al listado correspondiente de cada uno.
     * Finds and displays a Persona entity.
     */
    public function showAction($id)
    {
       
                $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraPerShow'));
        
        //obtengo la info de la persona
        $persona = $this->getDoctrine()
                          ->getManager()
                          ->getRepository('SiafcaIntranetBundle:Persona')
                          ->obtenerPersona($id);
        
        $deleteForm = $this->createDeleteForm($persona);
        
    
        $domicilio = new Domicilio();
        $domicilio->setPersona($persona);
        // El submit se atiende en el controlador de domicilio:
        $domicilioForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\DomicilioType',
                $domicilio,
                array('action' => $this->generateUrl(
                        'domicilio_new', 
                        array('id_entity' => $persona->getId(), 'persona' => true)
                        )
                    )
                );
        
        $editDomicilioForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\DomicilioType',
                $domicilio,
                array('action' => $this->generateUrl(
                        'domicilio_edit_ajax')
                )
                );
        
        $familiar = new Familiar();
        
        $familiar->setTitular($persona);
        
        $familiarForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\FamiliarType',
                $familiar,
                array('action' => $this->generateUrl(
                        'familiar_new_ajax', 
                        array('id_persona' => $persona->getId())
                        )
                    )
                );
        
        $editFamiliarForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\FamiliarType',
                $familiar,
                array('action' => $this->generateUrl(
                        'familiar_edit_ajax')
                    )
                );
        
        
        return $this->render('persona/show.html.twig', array(
            'persona' => $persona,
            'delete_form' => $deleteForm->createView(),
            'domicilioForm' => $domicilioForm->createView(),
            'familiarForm' =>$familiarForm->createView(),
            'editFamiliarForm' =>$editFamiliarForm->createView(),
            'editDomicilioForm' =>$editDomicilioForm->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Displays a form to edit an existing Persona entity.
     *
     */
    public function editAction(Request $request, Persona $persona)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraPerEdit'));
        $deleteForm = $this->createDeleteForm($persona);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\PersonaType', $persona);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($persona);
            $em->flush();

            return $this->redirectToRoute('persona_show', array('id' => $persona->getId()));
        }

        return $this->render('persona/edit.html.twig', array(
            'persona' => $persona,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Deletes a Persona entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->find($id);
        $form = $this->createDeleteForm($persona);
        $form->handleRequest($request);
        //variable para mostrar mensaje 
        $rel = (!$persona->getAportantes() && !$persona->getFirmantes());
        //Si la persona no existe (segun el id) o la persona es aportante
        //o firmante, no se elimina y vuelve al show de ese id
        if ($persona){
            $em = $this->getDoctrine()->getManager();
            $em->remove($persona);
            $em->flush();
            return $this->redirectToRoute('persona_index');
        } else {
            return $this->redirectToRoute('persona_show', array(
                        'id' => $id,
            ));
        }
    }

    /**
     * Creates a form to delete a Persona entity.
     *
     * @param Persona $persona The Persona entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Persona $persona)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('persona_delete', array('id' => $persona->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function ajaxCuilAction(Request $request) {
        if (! $request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }
        
        $cuil = $request->query->get('cuil');
        $repoPersona = $this->getDoctrine()->getManager()->getRepository('SiafcaIntranetBundle:Persona');
        $personas = $repoPersona->findBy(array(
                'cuil' => $cuil
        ));
        $result = array();
        foreach ($personas as $persona) {
            $singlePerson = array();
            $singlePerson['id'] = $persona->getId();
            $singlePerson['cuil'] = $persona->getCuil();
            $singlePerson['nombre'] = $persona->getNombre();
            $singlePerson['apellidoPat'] = $persona->getApellidoPat();
            $singlePerson['apellidoMat'] = $persona->getApellidoMat();
            $singlePerson['documento'] = $persona->getDocumento();
            $singlePerson['tipoDocumento'] = $persona->getTipoDocumento()->getId();
            $singlePerson['fechaNac'] = $persona->getFechaNac()->format('d/m/Y');
            $singlePerson['nacionalidad'] = $persona->getNacionalidad()->getId();
            $singlePerson['sexo'] = $persona->getSexo()->getId();
            $singlePerson['estadoCivil'] = $persona->getEstadoCivil()->getId();
            $result[] = $singlePerson;
        }
        return new JsonResponse($result);
    }
    
    /**
     * Retorna los datos necesarios para la vista de historicos de aportante-persona
     * @param Request $request
     * @param Persona $persona
     * @return type
     */
    public function historicoAportanteAction(Request $request, Persona $persona) {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraPerApoH'));

        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\DateFiltersType', null, array());
        $form->handleRequest($request);
        
        $dql = "SELECT ".
                   "partial e.{id, nroLiq, fechaAlta, fechaBaja}, ".
                   "partial o.{id, nombre}, ".
                   "partial c.{id, nombre}, ".
                   "partial r.{id, nombre}, ".
                   "partial es.{id, nombre} ".
               "FROM SiafcaIntranetBundle:Aportante e ".
                   "JOIN e.organismo o ".
                   "JOIN e.cargo c ".
                   "JOIN e.revista r ".
                   "JOIN e.estado es ".
               "WHERE e.persona = :personaId";
        
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $request->request->get('date_filters')['startDate'];
            $end = $request->request->get('date_filters')['endDate'];
            if (!is_null($start) && trim($start) !== '') {
                $matches = array();
                preg_match('/(\d\d)\/(\d\d)\/(\d{4})/', $start, $matches);
                if (count($matches) == 4) {
                    list($day, $month, $year) = array($matches[1], $matches[2], $matches[3]);
                    $start = (new \DateTime())->setDate($year, $month, $day);
                }
                $dql .= " AND e.fechaAlta >= '".$start->format("Y-m-d")." 00:00:00'";
            }
            if (!is_null($end) && trim($end) !== '') {
                $matches = array();
                preg_match('/(\d\d)\/(\d\d)\/(\d{4})/', $end, $matches);
                if (count($matches) == 4) {
                    list($day, $month, $year) = array($matches[1], $matches[2], $matches[3]);
                    $end = (new \DateTime())->setDate($year, $month, $day);
                }
                $dql .= " AND (e.fechaBaja <= '".$end->format("Y-m-d")." 23.59.59'";
                $dql .= " OR (e.fechaAlta <= '".$end->format("Y-m-d")." 23.59.59'";
                $dql .= " AND e.fechaBaja IS NULL))";
            }
        }

        $query = $em->createQuery($dql)->setParameter('personaId', $persona->getId());

        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /* page number */
            20, /* limit per page */
            array('defaultSortFieldName' => 'e.fechaAlta', 'defaultSortDirection' => 'desc')
        );

        return $this->render('partials/history.html.twig', array(
            'entities' => $entities,
            'titulo' => $persona->getNombreCompleto(),
            'subtitulo' => 'Historico de rol aportante',
            'form' => $form->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Retorna los datos necesarios para la vista de historicos de firmante-persona
     * @param Request $request
     * @param Persona $persona
     * @return type
     */
    public function historicoFirmanteAction(Request $request, Persona $persona) {


        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\DateFiltersType', null, array());
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraPerFirH'));

        $dql = "SELECT ".
                   "partial e.{id, fechaDesde, fechaHasta, comentario}, ".
                   "partial o.{id, nombre} ".
               "FROM SiafcaIntranetBundle:Firmante e ".
                   "JOIN e.organismo o ".
               "WHERE e.persona = :personaId";

        if ($form->isSubmitted() && $form->isValid()) {
            $start = $request->request->get('date_filters')['startDate'];
            $end = $request->request->get('date_filters')['endDate'];
            if (!is_null($start) && trim($start) !== '') {
                $matches = array();
                preg_match('/(\d\d)\/(\d\d)\/(\d{4})/', $start, $matches);
                if (count($matches) == 4) {
                    list($day, $month, $year) = array($matches[1], $matches[2], $matches[3]);
                    $start = (new \DateTime())->setDate($year, $month, $day);
                }
                $dql .= " AND e.fechaDesde >= '".$start->format("Y-m-d")." 00:00:00'";
            }
            if (!is_null($end) && trim($end) !== '') {
                $matches = array();
                preg_match('/(\d\d)\/(\d\d)\/(\d{4})/', $end, $matches);
                if (count($matches) == 4) {
                    list($day, $month, $year) = array($matches[1], $matches[2], $matches[3]);
                    $end = (new \DateTime())->setDate($year, $month, $day);
                }
                $dql .= " AND (e.fechaHasta <= '".$end->format("Y-m-d")." 23.59.59'";
                $dql .= " OR (e.fechaDesde <= '".$end->format("Y-m-d")." 23.59.59'";
                $dql .= " AND e.fechaHasta IS NULL))";
            }
        }

        $query = $em->createQuery($dql)->setParameter('personaId', $persona->getId());

        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /* page number */
            20, /* limit per page */
            array('defaultSortFieldName' => 'e.fechaDesde', 'defaultSortDirection' => 'desc')
        );

        return $this->render('partials/history.html.twig', array(
            'entities' => $entities,
            'titulo' => $persona->getNombreCompleto(),
            'subtitulo' => 'Historico de rol firmante',
            'form' => $form->createView(),
            'ayuda' => $ayuda
        ));
    }
}
