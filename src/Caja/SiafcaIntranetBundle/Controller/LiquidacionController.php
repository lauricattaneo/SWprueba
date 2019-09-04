<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Caja\SiafcaIntranetBundle\Entity\Liquidacion;
use Caja\SiafcaIntranetBundle\Util\Excepcion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\InformePDF;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Liquidacion controller.
 * AYUDA:
 * IntraLiqIndex - indexAction
 * IntraLiqNew - newAction
 * IntraLiqShow - showAction
 * IntraLiqEdit - editAction
 * IntraLiqFilt - filterAction
 * IntraApoShow - aportantesAction
 * IntraApoCuiShow - buscarAportanteCuilAction
 * IntraApoDniShow - buscarAportanteDniAction
 * 
 */
class LiquidacionController extends Controller {

    public function aportantesAction(Request $request, $liquidacionId) {

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraApoShow'));
  
        //busco los aportantes de la liq
        $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($liquidacionId);

        //obtengo el año y mes si se obtuvieron datos      
        $anio = '';
        $mes = '';
//        $subtitulo = false;
        
        //accedo a la primer liquidacion, si es que esta definida
        if(!empty($liquidacion))
        {
            $anio = $liquidacion->getAnio();
            $mes = $liquidacion->getNombreMes();
//            $subtitulo = true;
        }
        
        //Creo el formulario
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        $form->handleRequest($request);
        
        if($form->isValid() && $form->isSubmitted())
        {
            $dato = $form->get('busqueda')->getData();
            if($dato == 'apellido')
            {
                $nombre = trim($form->get('campoNombre')->getData());
                $apellido = trim($form->get('campoApellido')->getData());
                
                //si el nombre viene vacio (o el apellido)
                //se le asigna un valor para que se 
                //pueda generar la ruta, sino tira un throw exepction
                
                $Nombre   = $nombre   == '' ? 'null' : $nombre;
                $Apellido = $apellido == '' ? 'null' : $apellido;
                
                //
                return $this->redirectToRoute
                        ('liquidacion_aportante_buscar_apellido',
                            array(
                                'idliq'=> $liquidacionId,
                                'apellido' => $Apellido,
                                'nombre' => $Nombre,
                                'mes' =>  $mes,
                                'anio' => $anio
                                ));
            }
            else if($dato == 'documento')
            {
                $documento = trim($form->get('campoDni')->getData());
                //
                return $this->redirectToRoute(
                        'liquidacion_aportante_buscar_dni',
                            array(
                                'idliq'=> $liquidacionId,
                                'dni' => $documento,
                                'mes' =>  $mes,
                                'anio' => $anio
                                ));
            }
            else if($dato == 'cuil')
            {
                $cuil = trim($form->get('campoCuil')->getData());
                //
                return $this->redirectToRoute
                        ('liquidacion_aportante_buscar_cuil', 
                        array('idliq'=> $liquidacionId,
                              'cuil' => $cuil,
                              'mes' =>  $mes,
                              'anio' => $anio,
                            )
                        );
            }
        }
        
        
        //consulto
        $query = $em->createQuery(
                                'SELECT partial i.{id}'
                                . 'FROM SiafcaIntranetBundle:Item i'
                                . ' JOIN i.liquidacion l'
                                . ' JOIN i.aportante a'
                                . ' JOIN a.persona p '
                                . ' WHERE l.id = :idLiq'
                                . ' ORDER BY p.apellidoPat,p.nombre ASC')
                    ->setParameter('idLiq', $liquidacionId);
        $paginator = $this->get('knp_paginator');
        $aportantes = $paginator->paginate(
                $query, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 20/* limit per page */
        );

        return $this->render('liquidacion/aportantes.html.twig',
                array(
                    'aportantes' => $aportantes, /* consulta paginada */
                    'liquidacion' => $liquidacion,
                    'idLiq'=> $liquidacion->getid(),
                    'ayuda' => $ayuda,
//                    'subtitulo' => $subtitulo,
                    'anio' => $anio,
                    'mes' => $mes,
                    'bandera' => true,
                    'form' => $form->createView()
                ));
    }
    
    //funciona
    public function buscarAportanteCuilAction(Request $request,$idliq,$cuil,$mes,$anio)
    {   
        $em = $this->getDoctrine()->getManager();
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraApoCuiShow'));
        //creo el form de busq 
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        $form->handleRequest($request);
        
        $resultado = $this
                          ->getDoctrine()
                          ->getManager()
                          ->getRepository('SiafcaIntranetBundle:Liquidacion')
                          ->obtenerAportantesCuil($idliq,$cuil);
        
        //si falla muestro error
        if(empty($resultado))
        {
            $form->handleRequest($request);
            $this->addFlash(
                    'error', // error, info, success
                    'No se encontró una persona asociada al cuil ingresado.'
            );
            return $this->redirectToRoute('liquidacion_aportantes',
                    array('liquidacionId' => $idliq));
        }
        //paginacion
        $paginator = $this->get('knp_paginator');
        $aportantes = $paginator->paginate(
                $resultado, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 20/* limit per page */
        );
        
        return $this->render('liquidacion/aportantes.html.twig',
                array(
                    'aportantes' => $aportantes, /* consulta paginada */
                    'liquidacion' => 0,
                    'idLiq'=> $idliq,
                    'ayuda' => $ayuda,
//                    'subtitulo' => true,
                    'anio' => $anio,
                    'mes' => $mes,
                    'bandera' => FALSE,
                    'form' => $form->createView()
                ));
    }

    public function buscarAportanteApellidoAction(Request $request,$idliq,$apellido,$nombre,$mes,$anio)
    {   
        $em = $this->getDoctrine()->getManager();
        //creo el form de busq 
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        $form->handleRequest($request);
        
        $resultado = $this
                          ->getDoctrine()
                          ->getManager()
                          ->getRepository('SiafcaIntranetBundle:Liquidacion')
                          ->obtenerAportantesApellidoNombre($idliq,$apellido,$nombre);
      
        //si falla muestro error
        if(empty($resultado))
        {
            $form->handleRequest($request);
            $this->addFlash(
                    'error', // error, info, success
                    'No se encontró una persona asociada al apellido y nombre ingresado.'
            );
            return $this->redirectToRoute('liquidacion_aportantes',
                    array('liquidacionId' => $idliq));
        }
        
        //paginacion
        $paginator = $this->get('knp_paginator');
        $aportantes = $paginator->paginate(
                $resultado, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 20/* limit per page */
        );
        
        return $this->render('liquidacion/aportantes.html.twig',
                array(
                    'aportantes' => $aportantes, /* consulta paginada */
                    'liquidacion' => 0,
                    'idLiq'=> $idliq,
                    'ayuda' => 0,
//                    'subtitulo' => true,
                    'anio' => $anio,
                    'mes' => $mes,
                    'bandera' => FALSE,
                    'form' => $form->createView()
                ));
    }
            
    public function buscarAportanteDniAction(Request $request,$idliq,$dni,$mes,$anio)
    {   
        $em = $this->getDoctrine()->getManager();
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraApoDniShow'));
  
        //creo el form de busq 
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\BusquedaPersonaType');
        $form->handleRequest($request);
        
        $resultado = $this
                          ->getDoctrine()
                          ->getManager()
                          ->getRepository('SiafcaIntranetBundle:Liquidacion')
                          ->obtenerAportantesDni($idliq,$dni);
       
        //si falla muestro error
        if(empty($resultado))
        {
            $form->handleRequest($request);
            $this->addFlash(
                    'error', // error, info, success
                    'No se encontró una persona asociada al documento ingresado.'
            );
            return $this->redirectToRoute('liquidacion_aportantes',
                    array('liquidacionId' => $idliq));
        }
        
        //paginacion
        $paginator = $this->get('knp_paginator');
        $aportantes = $paginator->paginate(
                $resultado, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 20/* limit per page */
        );
        
        return $this->render('liquidacion/aportantes.html.twig',
                array(
                    'aportantes' => $aportantes, /* consulta paginada */
                    'liquidacion' => 0,
                    'idLiq'=> $idliq,
                    'ayuda' => $ayuda,
//                    'subtitulo' => true,
                    'anio' => $anio,
                    'mes' => $mes,
                    'bandera' => FALSE,
                    'form' => $form->createView()
                ));
    }

    public function downloadJubidatAction(Request $request) {
        $id = $request->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $response = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->jubiDatExists($id);


        return new JsonResponse($response);
    }

    public function transicionAction(Request $request) {
        $id = $request->request->get('id');
        $trans = $request->request->get('trans');
        $em = $this->getDoctrine()->getManager();
        $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($id);

        $response = array(
            'success' => false,
            'mensaje' => '',
            'file' => '',
        );
        $estadoTrans = false;

        if ($trans == 'T11') {
            $response = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->jubiDatExists($id);


            if ($response['success'] && $response['jubidat']) {
                //$estadoTrans = true;
                $estadoTrans = $liquidacion->aplicarTransicion($trans);
            }
        } else {
            $estadoTrans = $liquidacion->aplicarTransicion($trans);
        }

        if ($estadoTrans) {
            $em->persist($liquidacion);
            $em->flush();
            $estado = $liquidacion->getEstado();
            $response['success'] = true;
            $response['mensaje'] = $estado->getNombre() . " con exito";
        } else {
            $response['success'] = false;
            $response['mensaje'] = "No se pudo aplicar la transición";
        }
        return new JsonResponse($response);
    }

    public function informePDFAction($id) {
        $em = $this->getDoctrine()->getManager();
        $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($id);


        $parametros = array(
            'id' => $liquidacion->getId(),
            'nombre' => $liquidacion->getOrganismo()->getNombre(),
            'codigo' => $liquidacion->getOrganismo()->getCodigo(),
            'fuente' => $liquidacion->getFuenteLiq(),
            'tipo' => $liquidacion->getTipoLiq(),
            'presentado' => $liquidacion->getPresentacion()->getFechaPresentacion()->format('d/m/y'),
            'ctdadAport' => $liquidacion->getInforme()->getCantidadAportantes(),
            'periodo' => $liquidacion->getMes() . "/" . $liquidacion->getAnio(),
            'resumen' => $em->getRepository('SiafcaIntranetBundle:InformeItem')->getInformeItemSorted($liquidacion->getInforme()->getId()),
        );

        $pdf = new InformePDF($parametros);
        $pdf->render();

        $nombreArch = 'INFORME' . str_pad((string) $liquidacion->getId(), 12, '0', STR_PAD_LEFT) . '.pdf';

        $pdf->Output($nombreArch, 'I');
    }

    /**
     * Lists all Liquidacion entities.
     *
     */
    public function indexAction(Request $request, Organismo $organismo = null) {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraLiqIndex'));

        $formFilter = $this->get('form.filter');
        $route = $this->generateUrl('liquidacion_index', array('organismo' => (is_null($organismo) ? null : $organismo->getId())));
        $filterForm = $formFilter->createFilterForm($route, $this->getFormFilterAttrs(), 'filterForm');

        $dql = 'SELECT '
                . 'partial l.{id, mes, anio}, '
                . 'partial e.{id, nombre, estado}, '
                . 'partial tl.{id, nombre, codigo}, '
                . 'partial fl.{id, nombre, codigo}, '
                . 'partial o.{id, nombre, codigo}, '
                . 'partial cc.{id}, '
                . 'partial i.{id}, '
                . 'partial p.{id}, '
                . 'partial bd.{id} '
                . 'FROM SiafcaIntranetBundle:Liquidacion l'
                . ' JOIN l.estado e'
                . ' JOIN l.organismo o'
                . ' JOIN l.presentacion p'
                . ' JOIN l.fuenteLiq fl'
                . ' JOIN l.tipoLiq tl'
                . ' LEFT JOIN l.conjuntoControl cc'
                . ' LEFT JOIN l.informe i'
                . ' LEFT JOIN l.boletaDeposito bd';

        $filterForm->handleRequest($request);
        $searchForm = $formFilter->createFilterForm($route, $this->getSearchFormFilterAttrs(), 'searchForm');
        $searchForm->handleRequest($request);
        $filters = $this->getFilterResults($filterForm, $searchForm);

        $subtitulo = $filters['subtitulo'];
        $dql .= $filters['dql'];
        if (isset($organismo)) {
            $dql .= ' AND o.id = ' . $organismo->getId() . ' ';
        }
        $dql .= ' ORDER BY o.codigo DESC, l.anio DESC, l.mes DESC, tl.codigo ASC';

        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $liquidaciones = $paginator->paginate(
                $query, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 20/* limit per page */
                //array('defaultSortFieldName' => 'p.fechaPresentacion', 'defaultSortDirection' => 'desc')
        );

        return $this->render('liquidacion/index.html.twig', array(
                    'liquidaciones' => $liquidaciones,
                    'subtitulo' => (is_null($organismo) ? '' : $organismo->getNombre() . ' - ') . $subtitulo,
                    'filterForm' => $filterForm->createView(),
                    'searchForm' => $searchForm->createView(),
                    'ayuda' => $ayuda
        ));
    }

    /**
     * Creates a new Liquidacion entity.
     *
     */
    public function newAction(Request $request) {
        $liquidacion = new Liquidacion();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\LiquidacionType', $liquidacion);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraLiqNew'));


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($liquidacion);
            $em->flush();

            return $this->redirectToRoute('liquidacion_show', array('id' => $liquidacion->getId()));
        }

        return $this->render('liquidacion/new.html.twig', array(
                    'liquidacion' => $liquidacion,
                    'form' => $form->createView(),
                    'ayuda' => $ayuda
        ));
    }

    /**
     * Finds and displays a Liquidacion entity.
     *
     */
    public function showAction(Liquidacion $liquidacion) {
        $em = $this->getDoctrine()->getManager();
        $estado =  $liquidacion->getEstado();
        
        $ayuda = '';
        if($estado == 'Aplicado')
        {
            $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraLiqShowApl')); 
        }
        else if($estado == 'Presentado')
        {
            $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraLiqShowPre'));
        }
        else if($estado == 'Rechazado')
        {
            $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraLiqShowRec'));
        }
        
        return $this->render('liquidacion/show.html.twig', array(
                    'liquidacion' => $liquidacion,
                    'ayuda' => $ayuda
        ));
    }

    /**
     * Displays a form to edit an existing Liquidacion entity.
     *
     */
    public function editAction(Request $request, Liquidacion $liquidacion) {
        $deleteForm = $this->createDeleteForm($liquidacion);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\LiquidacionType', $liquidacion);
        $editForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraLiqEdit'));

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($liquidacion);
            $em->flush();

            return $this->redirectToRoute('liquidacion_edit', array('id' => $liquidacion->getId()));
        }

        return $this->render('liquidacion/edit.html.twig', array(
                    'liquidacion' => $liquidacion,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'ayuda'=> $ayuda
        ));
    }

    /**
     * Deletes a Liquidacion entity.
     *
     */
    public function deleteAction(Request $request, Liquidacion $liquidacion) {
        $form = $this->createDeleteForm($liquidacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($liquidacion);
            $em->flush();
        }

        return $this->redirectToRoute('liquidacion_index');
    }

    /**
     * Creates a form to delete a Liquidacion entity.
     *
     * @param Liquidacion $liquidacion The Liquidacion entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Liquidacion $liquidacion) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('liquidacion_delete', array('id' => $liquidacion->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     *
     * @return array
     */
    private function getFormFilterAttrs() {

        $formAttrs = array(
            array(
                'type' => 'SubmitType',
                'name' => 'todos',
                'class' => 'btn-default',
                'label' => 'Todos',
                'dql' => " WHERE e.estado IN ('08', '09', '10', '11', '13') AND e.clase = 'LIQ'",
                'subtitulo' => 'Todas'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'presentadas',
                'class' => 'btn-primary-bt',
                'label' => 'Presentadas',
                'dql' => "  WHERE e.estado IN ('08') AND e.clase = 'LIQ'",
                'subtitulo' => 'Presentadas'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'aceptadas',
                'class' => 'btn-success',
                'label' => 'Aceptadas',
                'dql' => "  WHERE e.estado IN ('09', '10', '11') AND e.clase = 'LIQ'",
                'subtitulo' => 'Aceptadas'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'canceladas',
                'class' => 'btn-danger',
                'label' => 'Canceladas',
                'dql' => "  WHERE e.estado IN ('13') AND e.clase = 'LIQ'",
                'subtitulo' => 'Canceladas'
            )
        );


        return $formAttrs;
    }

    /**
     *
     * @return array
     */
    private function getSearchFormFilterAttrs() {
        $formAttrs = array(
            array(
                'type' => 'TextType',
                'name' => 'codigo',
                'placeholder' => 'Código Organismo',
                'label' => false,
                'dql' => " WHERE o.codigo LIKE '",
                'subtitulo' => 'Liquidaciones del Organismo '
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'buscar',
                'class' => 'btn-primary',
                'label' => 'Buscar',
                'dql' => "",
                'subtitulo' => ''
            )
        );


        return $formAttrs;
    }

    /**
     * 
     * @return array
     */
    private function getSearchFormFilterAttrs_1() {
        $formAttrs = array(
            array(
                'type' => 'TextType',
                'name' => 'cuil',
                'placeholder' => 'C.U.I.L.',
                'label' => false,
                'dql' => " and p.cuil LIKE '",
                'subtitulo' => 'Aportantes con C.U.I.L. ',
                'maxlength' => '10'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'buscar',
                'class' => 'btn-primary',
                'label' => 'Buscar',
                'dql' => "",
                'subtitulo' => ''
            )
        );
        // 'dql' => " WHERE p.cuil LIKE '",
        return $formAttrs;
    }

    /**
     *
     * @param Form $filterForm
     * @return array
     */
    private function getFilterResults(Form $filterForm, Form $searchForm) {
        $formFilter = $this->get('form.filter');
        $defaultSubtitulo = 'Todas';
        $defaultDQL = " WHERE e.estado IN ('08', '09', '10', '11', '13') AND e.clase = 'LIQ'";
        $form = null;
        $isButtonForm = true;
        $formAttrs = null;

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $form = $filterForm;
            $isButtonForm = true;
            $formAttrs = $this->getFormFilterAttrs();
        } elseif ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $form = $searchForm;
            $isButtonForm = false;
            $formAttrs = $this->getSearchFormFilterAttrs();
        }

        $filters = $formFilter->getFilterResults($form, $isButtonForm, $formAttrs, $defaultSubtitulo, $defaultDQL);

        return $filters;
    }

    /**
     * 
     * @param \Symfony\Component\Form\Form $searchForm
     * @return array
     */
    private function getFilterResults_1(Form $searchForm) {
        $formFilter = $this->get('form.filter');
        $defaultSubtitulo = 'Todos';
        $form = null;
        $isButtonForm = true;
        $formAttrs = null;
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $form = $searchForm;
            $isButtonForm = false;
            $formAttrs = $this->getSearchFormFilterAttrs_1();
        }
        $filters = $formFilter->getFilterResults($form, $isButtonForm, $formAttrs, $defaultSubtitulo);
        return $filters;
    }

    /**
     * Manejador para vista de busqueda de liquidaciones
     * @param Request $request
     * @return type
     */
    public function filterAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraLiqFilt'));


        $em = $this->getDoctrine()->getManager();

        // Consulta base:
        $dql = 'SELECT '
                . 'partial l.{id, mes, anio}, '
                . 'partial e.{id, nombre, estado}, '
                . 'partial tl.{id, nombre, codigo}, '
                . 'partial fl.{id, nombre, codigo}, '
                . 'partial o.{id, nombre, codigo}, '
                . 'partial cc.{id}, '
                . 'partial i.{id}, '
                . 'partial p.{id}, '
                . 'partial bd.{id} '
                . 'FROM SiafcaIntranetBundle:Liquidacion l'
                . ' JOIN l.estado e'
                . ' JOIN l.organismo o'
                . ' JOIN l.presentacion p'
                . ' JOIN l.fuenteLiq fl'
                . ' JOIN l.tipoLiq tl'
                . ' LEFT JOIN l.conjuntoControl cc'
                . ' LEFT JOIN l.informe i'
                . ' LEFT JOIN l.boletaDeposito bd';

        $liquidacion = new Liquidacion();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\SearchLiquidacionType', $liquidacion, array(
            'method' => 'GET',
        ));
        $form->handleRequest($request);

        // Añade condiciones a la consulta dependiendo de los campos llenados en el formulario
        if ($form->isSubmitted() && $form->isValid()) {
            $condition = '';
            if ($liquidacion->getTitulo()) {
                $condition .= " AND UPPER(l.titulo) LIKE '%" . strtoupper($liquidacion->getTitulo()) . "%'";
            }
            if ($liquidacion->getAnio()) {
                $condition .= " AND l.anio = '" . $liquidacion->getAnio() . "'";
            }
            if ($liquidacion->getMes()) {
                $condition .= " AND l.mes = '" . $liquidacion->getMes() . "'";
            }
            if ($liquidacion->getFuenteLiq()) {
                $condition .= " AND fl.id = " . $liquidacion->getFuenteLiq()->getId();
            }
            if ($liquidacion->getTipoLiq()) {
                $condition .= " AND tl.id = " . $liquidacion->getTipoLiq()->getId();
            }
            if ($liquidacion->getEstado()) {
                $condition .= " AND e.id = " . $liquidacion->getEstado()->getId();
            }
            if (trim($request->query->get('search_liquidacion')['orgName']) != '') {
                $condition .= " AND UPPER(o.nombre) LIKE '%" . strtoupper(trim($request->query->get('search_liquidacion')['orgName'])) . "%'";
            }
            if (trim($condition) !== '') {
                $dql .= ' WHERE' . substr($condition, 4);
            }
        }

        $dql .= ' ORDER BY l.anio DESC, l.mes DESC, tl.codigo ASC';

        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $filtrados = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 20
        );

        return $this->render('liquidacion/search.html.twig', array(
                    'form' => $form->createView(),
                    'liquidaciones' => $filtrados,
                    'ayuda' => $ayuda
        ));
    }

    }
