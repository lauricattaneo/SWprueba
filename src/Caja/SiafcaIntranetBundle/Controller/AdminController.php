<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * IntraIdCiudad - idCiudadanaAction
 * IntraIdCiShow - idCiudadanaValidarAction
 * 
 * Admin controller.
 *
 */
class AdminController extends Controller
{
    /**
     * Lista de las Excepciones
     *
     */
    public function indexExcepcionesAction(Request $request)
    {
       // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'No tiene los permisos necesarios');
        
        $em = $this->getDoctrine()->getManager();
        // obtengo la ayuda
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraExcIndex'));
        
        $formFilter = $this->get('form.filter');
        
        // creo el formulario de busqueda por codigo
        $searchForm = $formFilter->createFilterForm
                ($this->generateUrl('excepciones_index'),$this->getSearchFormFilterAttrs(),'searchForm') ;
        
        $searchForm->handleRequest($request);
        $filters = $this->getFilterResults( $searchForm, 'Excepciones sin resolver');

        // obtengo el index
        // $filters[dql] nos brinda la condicion where para la consulta
        // ejemplo el usuario busca codigo 1 entonces $filters[dql] es where codigo like 1
        $query = $this->obtenerIndex($filters['dql']);
        // pagino los resultados
        $excepciones = 
            $this->get('paginacion')->obtenerPaginacion
                (
                    $request,
                    $query/*page number*/,
                    10, /*limit per page*/
                    array('defaultSortFieldName' => 'ex.fecha', 'defaultSortDirection' => 'desc')
                );
        
        return $this->render('admin/indexExcepciones.html.twig', array(
            'excepciones' => $excepciones,
            'titulo'      => 'Log de Excepciones',
            'subtitulo'   => $filters['subtitulo'],
            'searchForm'  => $searchForm->createView(),
            'ayuda'       => $ayuda,
        ));
    }
    
    // funcion auxiliar para obtener el index
    private function obtenerIndex($condicionWhere)
    {
        $dql = "SELECT ex FROM SiafcaIntranetBundle:Excepcion ex " . $condicionWhere;
        
        return $this->getDoctrine()->getManager()
               ->createQuery($dql)
                ;
    }

    /**
     * Creates a new ExpteAmparo entity.
     *
     */
    public function newAction(Request $request, $orgId)
    {

        $em = $this->getDoctrine()->getManager();
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraExpAmpNew'));
        
        $expteAmparo = new ExpteAmparo();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\ExpteAmparoType', $expteAmparo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->persist($expteAmparo);
            $Organismo = $em
                ->getRepository('SiafcaIntranetBundle:Organismo')
                ->find($orgId);
            $expteAmparo->setOrganismo($Organismo);
            $em->flush();

            return $this->redirectToRoute('amparo_index', array('id' => $orgId));
        }

        return $this->render('amparo/new.html.twig', array(
            'idOrganismo' => $orgId,
            'expteAmparo' => $expteAmparo,
            'form' => $form->createView(),
            'ayuda' => $ayuda,
        ));
    }

    /**
     * Finds and displays a Cargo entity.
     *
     */
    public function showAction(Cargo $cargo)
    {
        $deleteForm = $this->createDeleteForm($cargo);

        return $this->render('cargo/show.html.twig', array(
            'cargo' => $cargo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Cargo entity.
     *
     */
    public function editAction(Request $request, Cargo $cargo)
    {
        $deleteForm = $this->createDeleteForm($cargo);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\CargoType', $cargo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cargo);
            $em->flush();

            return $this->redirectToRoute('cargo_edit', array('id' => $cargo->getId()));
        }

        return $this->render('cargo/edit.html.twig', array(
            'cargo' => $cargo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Cargo entity.
     *
     */
    public function deleteAction(Request $request, Cargo $cargo)
    {
        $form = $this->createDeleteForm($cargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cargo);
            $em->flush();
        }

        return $this->redirectToRoute('cargo_index');
    }

    /**
     * Creates a form to delete a Cargo entity.
     *
     * @param Cargo $cargo The Cargo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cargo $cargo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cargo_delete', array('id' => $cargo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * 
     * @return array
     */
    private function getSearchFormFilterAttrs()
    {
        $formAttrs = array(
            array(
                'type' => 'TextType',
                'name' => 'codigo',
                'placeholder' => 'Código de Error',
                'label' => false,
                'dql' => " WHERE ex.codigo LIKE '",
                'subtitulo' => 'Excepciones con el Código '
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
    private function getAyudasSearchFormFilterAttrs()
    {
        $formAttrs = array(
            array(
                'type' => 'TextType',
                'name' => 'codigo',
                'placeholder' => 'Código de Ayuda',
                'label' => false,
                'dql' => " WHERE a.codigo LIKE '",
                'subtitulo' => 'Ayudas con el Código '
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
     * @param \Symfony\Component\Form\Form $searchForm
     * @return array
     */
    private function getFilterResults(Form $searchForm, $subtitulo)
    {
        $formFilter = $this->get('form.filter');
        $defaultSubtitulo = $subtitulo;
        $form = null;
        $isButtonForm = true;
        $formAttrs = null;
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $form = $searchForm;
            $isButtonForm = false;
            $formAttrs = $this->getSearchFormFilterAttrs();
        }
        
        $filters = $formFilter->getFilterResults($form, $isButtonForm, $formAttrs, $defaultSubtitulo);
        
        return $filters;
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\Form $searchForm
     * @return array
     */
    private function getAyudasFilterResults(Form $searchForm, $subtitulo)
    {
        $formFilter = $this->get('form.filter');
        $defaultSubtitulo = $subtitulo;
        $form = null;
        $isButtonForm = true;
        $formAttrs = null;
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $form = $searchForm;
            $isButtonForm = false;
            $formAttrs = $this->getAyudasSearchFormFilterAttrs();
        }
        
        $filters = $formFilter->getFilterResults($form, $isButtonForm, $formAttrs, $defaultSubtitulo);
        
        return $filters;
    }

    /**
     * Elimina items relacionados a un amparo. Para utilizar via AJAX.
     * @param Request $request
     * @return Response True o False indicando si se pudo realizar la acción
     */
    public function deleteAmparoItemsAction(Request $request)
    {
        $targetId = $request->request->get('targetId');
        $entityType = $request->request->get('entityType');
        $em = $this->get('doctrine')->getManager();

        //Dependiendo la entidad borrada hay que eliminar las posibles asociaciones:
        $instance = $instanceAI = $instanceEAIC = null;
        switch ($entityType) {
            case 'expediente':
                $instance = $em->find('SiafcaIntranetBundle:ExpteAmparo', $targetId);
                $instanceAI = $em->getRepository('SiafcaIntranetBundle:ExpteAmpItem')->findBy(array('expteAmparo' => $targetId));
                $instanceAIId = array();
                foreach ($instanceAI as $AI) { $instanceAIId[] = $AI->getId(); }
                $instanceEAIC = $em->getRepository('SiafcaIntranetBundle:ExpteAmpItemConcepto')->findBy(array('amparoItem' => $instanceAIId));
                break;
            case 'amparado':
                $instance = $em->find('SiafcaIntranetBundle:ExpteAmpItem', $targetId);
                $instanceEAIC = $em->getRepository('SiafcaIntranetBundle:ExpteAmpItemConcepto')->findBy(array('amparoItem' => $targetId));
                break;
            case 'concepto':
                $instance = $em->find('SiafcaIntranetBundle:ExpteAmpItemConcepto', $targetId);
                break;
            default:
                //Si entityType no es valido se lanza un error
                throw new \Exception('El tipo de entidad no es válido');
        }
        
        if(is_null($instance)) {
            throw new \Exception('No se encontró una entidad con el id especificado');
        } else {
            if (!is_null($instanceEAIC)) {
                foreach ($instanceEAIC as $concepto) {
                    $em->remove($concepto);
                }
            }
            if (!is_null($instanceAI)) {
                foreach ($instanceAI as $itemExpte) {
                    $em->remove($itemExpte);
                }
            }
            $em->remove($instance);
            $em->flush();
        }

        //Si llega a este punto el flush se completo correctamente (si hay un error lanza excepcion)
        $response = array('success' => true);
        return new Response(json_encode($response));
    }

    /**
     * Inserta items relacionados a un amparo. Para utilizar via AJAX.
     * @param Request $request
     * @return json Response True o False indicando si se pudo realizar la acción. Adicionalmente retorna el id del nuevo elemento.
     */
    public function newAmparoItemsAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        // Respuesta por defecto:
        $response = array('success' => false);
        // Lectura de parametros que envia el frontend:
        $idExpte = $request->request->get('idExpte');
        $idAportante = $request->request->get('idAportante');
        // Búsqueda de entidades necesarias para creacion de un nuevo ExpteAmpItem:
        $objAportante = $em->find('SiafcaIntranetBundle:Aportante', $idAportante);
        $objExpte = $em->find('SiafcaIntranetBundle:ExpteAmparo', $idExpte);
        // Si se encontraron las entidades necesarias para establecer las relaciones se crea nueva instancia:
        if(!is_null($objAportante) && !is_null($objExpte)) {
            $newAmparoItem = new ExpteAmpItem();
            $newAmparoItem->setAportante($objAportante);
            $newAmparoItem->setExpteAmparo($objExpte);
            $em->persist($newAmparoItem);
            $em->flush();
            // Si se insertó correctamente el id de la nueva instancia deja de ser null: se cambia la respuesta
            if(!is_null($newAmparoItem->getId())) {
                $response = array(
                    'success' => true,
                    'amparo' => array(
                        'id' => $newAmparoItem->getId(),
                        'nombre' => trim($objAportante->getPersona()->getNombre()).' '.trim($objAportante->getPersona()->getApellidoPat()),
                        'cargo' => $objAportante->getCargo()->getNombre(),
                        'revista' => $objAportante->getRevista()->getDescripcion(),
                        'liq' => $objAportante->getNroLiq(),
                    )
                );
            }
        }
        return new Response(json_encode($response));
    }
    
     /**
     * Importar la información contenida en archivos .JOS
     * Estos son archivo multi JUBI.DAT
     * Contienen todas las DDJJ que los organismos hicieron en un día concreto
     * Hay dos archivos .JOS por día. Uno con DDJJ de Adm. Ctral. y  el otro con las DDJJ del resto de los Organismos
     */
    public function importarJosAction(Request $request)
 {
        
        $em = $this->getDoctrine()->getManager();
        $defaultData = array('message' => 'Seleccione la fecha del .JOS');
        // obtengo el formulario
        // se movio el formulario al archivo Form/ImportaJosType
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\ImportaJosType');
        
        $form->handleRequest($request);

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraImportJos'));

        if ($form->isValid() && $form->isSubmitted()) {

            $data = $form->get('fecha')->getData();
            $dia = substr($data, 0, 2);
            $mes = substr($data, 3, 2);
            $anio = substr($data, 6, 4);

            if ($form->getClickedButton() && 'cancelar' === $form->getClickedButton()->getName()) {
                return $this->redirectToRoute('importar_jos');
            }
            
            if ($form->getClickedButton() && 'importarJos' === $form->getClickedButton()->getName()) {
                //var_dump('Importar !!!'); die;

                $this->getDoctrine()->getConnection()->executeQuery(""
                        . "BEGIN "
                        . "SP_PROCESAR_JOS(:dia, :mes, :anio, :sobreescribir); "
                        . "END;"
                        , array('dia' => $dia, 'mes' => $mes, 'anio' => $anio, 'sobreescribir' => 'S')
                ); 
                
                return $this->redirectToRoute('reporte_jos', array('fechaStr' => $dia.$mes.$anio));
            }
            
            if ($form->getClickedButton() && 'reporteJos' === $form->getClickedButton()->getName()) {
                return $this->redirectToRoute('reporte_jos', array('fechaStr' => $dia.$mes.$anio));
            }
            
        }
        
        return $this->render('admin/importarJos.html.twig', array(
                    'form' => $form->createView(),
                    'ayuda' => $ayuda
        ));
    }
    
    public function reporteJosAction($fechaStr)
    {
        
        $em = $this->getDoctrine()->getManager();
            $dia = substr($fechaStr, 0, 2);
            $mes = substr($fechaStr, 2, 2);
            $anio = substr($fechaStr, 6, 2);

            $tituloJu = 'ju' . $dia . '-' . $mes . '-' . $anio . ".JOS";
            $tituloJuAdm = 'juadm' . $dia . '-' . $mes . '-' . $anio . ".JOS";

            // Tabla REPORTE JU 

            $moduloJu = 'SP_IMPORTA_JOS-' . $tituloJu;

            $query = $em->createQuery(
                            'SELECT r.codigo, r.tipo , r.descripcion, r.valor'
                            . ' FROM SiafcaIntranetBundle:Reporte r'
                            . ' WHERE r.modulo = :modulo'
                            . ' ORDER BY r.codigo ASC')
                    ->setParameter('modulo', $moduloJu);

            $resultReporteJu = $query->getResult();


            //Tabla REPORTE JUADM

            $moduloJuAdm = 'SP_IMPORTA_JOS-' . $tituloJuAdm;

            $query = $em->createQuery(
                            'SELECT r.codigo, r.tipo , r.descripcion, r.valor'
                            . ' FROM SiafcaIntranetBundle:Reporte r'
                            . ' WHERE r.modulo = :modulo'
                            . ' ORDER BY r.codigo ASC')
                    ->setParameter('modulo', $moduloJuAdm);
            
             $ayuda1 = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraRepoJos'));

            $resultReporteJuAdm = $query->getResult();

            return $this->render('admin/reporteJos.html.twig', array(
                        'tituloJu' => $tituloJu,
                        'tituloJuAdm' => $tituloJuAdm,
                        'moduloJu' => $moduloJu,
                        'moduloJuAdm' => $moduloJuAdm,
                        'reporteJu' => $resultReporteJu,
                        'reporteJuAdm' => $resultReporteJuAdm,
                        'ayuda' => $ayuda1,
            ));
    }

    public function informeJosAction (Request $request, $titulo) 
    {
        $em = $this->getDoctrine()->getManager();

        // Tabla LIQUIDACION  -  paginada va con reporte 

        $query = $em->createQuery(
                        'SELECT l.id, o.codigo, trim(o.nombre) as nombre, l.anio, l.mes, l.nroLiq, tl.codigo as codigoLiq, tl.nombre as nombreLiq'
                        . ' FROM SiafcaIntranetBundle:Liquidacion l'
                        . ' JOIN l.organismo o'
                        . ' JOIN l.tipoLiq tl'
                        . ' WHERE l.titulo = :titulo'
                        . ' ORDER BY o.nombre ASC, l.anio ASC, l.mes ASC, l.nroLiq ASC, tl.codigo')
                ->setParameter('titulo', $titulo);

        $result = $query->getResult();
        $cant = count($result);
        
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraInforJos'));

        if (!$result) {
            $liquidaciones = null;
        } else {
            $paginator = $this->get('knp_paginator');
            $liquidaciones = $paginator->paginate(
                    $result, $request->query->getInt('page', 1), 20
            );
        }

        return $this->render('admin/informeJos.html.twig', array(
                    'liquidaciones' => $liquidaciones,
                    'cant' => $cant,
                    'titulo' => $titulo,
                    'ayuda' => $ayuda,
        ));
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
    
    public function ayudasAction(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'No tiene los permisos necesarios');
        
        $formFilter = $this->get('form.filter');
        
        $route = $this->generateUrl('ayudas');
        $searchForm = $formFilter->createFilterForm($route ,$this->getAyudasSearchFormFilterAttrs(), 'searchForm') ;
        $searchForm->handleRequest($request);
        $filters = $this->getAyudasFilterResults( $searchForm, 'Ayudas encontradas');
        // obtengo todas las ayudas
        $query = $this->obtenerAyudas($filters['dql']);
        // pagino los resultados
        $ayudas = 
            $this->get('paginacion')->obtenerPaginacion
                (
                    $request,
                    $query/*page number*/,
                    10, /*limit per page*/
                    array('defaultSortFieldName' => 'a.codigo', 'defaultSortDirection' => 'asc')
                );

        return $this->render('ayuda/indexAyudas.html.twig', array(
            'ayudas'     => $ayudas,
            'titulo'     => 'Ayudas',
            'subtitulo'  => $filters['subtitulo'],
            'searchForm' => $searchForm->createView(),
            
        ));        
    }
    
    // obtengo el listado de las ayudas
    private function obtenerAyudas($condicionWhere)
    {
        $dql = "SELECT a FROM SiafcaIntranetBundle:Ayuda a " . $condicionWhere; 
        return $this->getDoctrine()->getManager()
                    ->createQuery($dql)
                ;
    }
    
    public function idCiudadanaAction(Request $request)
    {
        $defaultData = array('message' => 'Ingrese la IdCiudadana que desea validar.');
        $em = $this->getDoctrine()->getManager();
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraIdCiudad'));

        $form = $this->createFormBuilder($defaultData)
            ->add('cuil', 'text')
            ->add('validar', SubmitType::class, array('label' => 'Validar', 'attr'  => array('class' => 'btn btn-default pull-right active')))
            ->add('cancelar', SubmitType::class, array('label' => 'Cancelar', 'attr'  => array('class' => 'btn btn-primary pull-right active')))
            ->setMethod('POST')
            ->getForm();     

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data es un arreglo con las claves "message", "cuil"
            $data = $form->getData();
//            dump($data); die;
            
//            return $this->redirectToRoute('id_ciudadana_validar', array('cuil' => $data['cuil']));
//            $request->setMethod('POST');
          
            return $this->redirectToRoute('id_ciudadana_validar', $data);
        }

        return $this->render('admin/idCiudadana.html.twig', array(
            'form' => $form->createView(),
            'ayuda' => $ayuda,
        ));
    }
    
    public function idCiudadanaValidarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraIdCiShow'));
        $cuil = $request->query->all()['cuil'];
        $filter = "(cuil=$cuil)";
        $ldap_ds = $this->container->getParameter('ldap_ds');
        $ds = ldap_connect($ldap_ds);
        $ldap_dn = $this->container->getParameter('ldap_dn');
        $justThese = array( "cn", "mail", "cuil" );
        $sr = @ldap_search( $ds, $ldap_dn, $filter, $justThese );
        
        if( $sr !== false ) {
            $info = ldap_get_entries( $ds, $sr );
            $count = $info["count"];
            
            $resp = ["valido" => 'N', 
                   "cuil" => [$cuil]];
            
            if ($count == 1) {
                
                $resp = ["valido" => 'S', 
                   "nombre" => $info[0]['cn'],
                   "mail" => $info[0]['mail'], 
                   "cuil" => $info[0]['cuil'],
                   "dn" => $info[0]['dn']];
            }
//            dump($resp); die;
            return $this->render('admin/idCiudadanaShow.html.twig', array(
            'resp' => $resp,
            'ayuda' => $ayuda,    
        ));
            
        }
    }   

}
