<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Form\OrganismoType; // Se utiliza pasando el nombre completo de la clase
use Caja\SiafcaIntranetBundle\Entity\Firmante;
use Symfony\Component\Form\Form;
use Caja\SiafcaIntranetBundle\Entity\Domicilio;
use Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo;
use Caja\SiafcaIntranetBundle\Entity\EntidadResponsable;

/**
 * Organismo controller.
 * AYUDA:
 * IntraOrgIndex - indexAction
 * IntraOrgNew - newAction
 * IntraOrgShow - showAction
 * IntraOrgSdShow - showAction
 * IntraOrgBlShow - showAction
 * IntraOrgEdit - editAction
 * IntraOrgFilt - filterAction
 */
class OrganismoController extends Controller
{
    /**
     * Retorna 10 organismos filtrados por nombre.
     * Para ser utilizado en autocompletado de campos y dar sugerencias a los usuarios.
     * @param Request $request
     * @return JsonResponse
     */
    public function AutocompleteAction(Request $request)
    {
        $input = $request->get('filter');

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                'SELECT DISTINCT o.nombre ' .
                'FROM SiafcaIntranetBundle:Organismo o ' .
                'WHERE UPPER(o.nombre) LIKE :filter ' .
                    'AND o INSTANCE OF SiafcaIntranetBundle:Organismo ' .
                'ORDER BY o.nombre ASC'
            )
            ->setParameter('filter', '%'.strtoupper($input).'%')
            ->setMaxResults(10);
        $results = $query->getArrayResult();

        $matchResults = array();
        foreach ($results as $result) {
            $matchResults[] = $result['nombre'];
        }

        $response = new JsonResponse();
        $response->setData(array('matchResults' => $matchResults));
        return $response;
    }

    /**
     * Lists all Organismo entities.
     */
    public function indexAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOrgIndex'));
        
        $formFilter = $this->get('form.filter');
        $route = $this->generateUrl('organismo_index');
        $filterForm = $formFilter->createFilterForm($route ,$this->getFormFilterAttrs(), 'filterForm') ;
        $searchForm = $formFilter->createFilterForm($route ,$this->getSearchFormFilterAttrs(), 'searchForm');

        $usuario = $this->getUser();
        $uOrganismos = $usuario->getUsuarioOrganismos();
        $orgAdm = $usuario->orgAdm();
        $dql= "SELECT "
                . "partial o.{id, nombre, codigo, cuit}, "
                . "partial t.{id, codigo, nombre}, "
                . "partial d.{id}, "
                . "partial l.{id, nombre} "
            . "FROM "
                . "SiafcaIntranetBundle:Organismo o "
                . "JOIN o.tipoOrganismo t "
                . "LEFT JOIN o.domicilios d "
                . "LEFT JOIN d.localidad l "
                . "JOIN o.estado e "
            . "WHERE o INSTANCE OF Caja\SiafcaIntranetBundle\Entity\Organismo";
        $searchForm->handleRequest($request);
        $filterForm->handleRequest($request);
        $filters = $this->getFilterResults($filterForm, $searchForm);
        $dql .= $filters['dql'];
        $dql .= " ORDER BY o.id DESC";
        $query = $em->createQuery($dql);
        
        $paginator  = $this->get('knp_paginator');
        $organismos = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
            //array('defaultSortFieldName' => 'o.nombre', 'defaultSortDirection' => 'asc')
        );

        $subtitulo = $filters['subtitulo'];
        
        return $this->render('organismo/index.html.twig', array(
            'organismos' => $organismos,
            'subtitulo' => $subtitulo,
            'filterForm' => $filterForm->createView(),
            'searchForm' => $searchForm->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Creates a new Organismo entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOrgNew'));


        $organismo = new Organismo();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\OrganismoType', $organismo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nombre = strtoupper($organismo->getNombre());
            $organismo->setNombre($nombre);
            $estado = $em->getRepository('SiafcaIntranetBundle:Estado')->findOneBy(array(
                'clase' => 'ORG',
                'estado' => '0'
            ));
            $organismo->organismoRecienCreado($estado);

            if ($organismo->getTipoOrganismo()->getCodigo() == '1'){     // Administración Central
                
                $prefijo = '1'.$organismo->getJuri()->getCodigo().'%';
                $sql = "select to_char(to_number(nvl(max(substr(codigo, 4, 5)), '00000'))+1, 'FM00000') from organismo where codigo like '".$prefijo."'";
                $nextValue = $this->getSequenceValue($sql, "TO_CHAR(TO_NUMBER(NVL(MAX(SUBSTR(CODIGO,4,5)),'00000'))+1,'FM00000')");
            } elseif ($organismo->getTipoOrganismo()->getCodigo() == '4'){     // Descentralizado
                
                $prefijo = '4'.$organismo->getJuri()->getCodigo().'%';
                $sql = "select to_char(to_number(nvl(max(substr(codigo, 4, 5)), '00000'))+1, 'FM00000') from organismo where codigo like '".$prefijo."'";
                $nextValue = $this->getSequenceValue($sql, "TO_CHAR(TO_NUMBER(NVL(MAX(SUBSTR(CODIGO,4,5)),'00000'))+1,'FM00000')");
                
            } elseif ($organismo->getTipoOrganismo()->getCodigo() == '5'){     // Organismos Adheridos
                $nextValue = $this->getSequenceValue("Select TO_CHAR(ORG_AD_SEQ.NEXTVAL, 'FM00000') from dual","TO_CHAR(ORG_AD_SEQ.NEXTVAL,'FM00000')");
            } elseif ($organismo->getTipoOrganismo()->getCodigo() == '2'){     // Municipalidad o Comuna
                $nextValue = $this->getSequenceValue("Select TO_CHAR(MUN_COM_SEQ.NEXTVAL, 'FM000') from dual","TO_CHAR(MUN_COM_SEQ.NEXTVAL,'FM000')");
            } else {
                $nextValue = null;
            }
            $organismo->generarCodigo($nextValue);
                 
            $em->persist($organismo);
            $em->flush();
            return $this->redirectToRoute('organismo_show', array('id' => $organismo->getId()));
        }

        return $this->render('organismo/edit.html.twig', array(
            'organismo' => $organismo,
            'form' => $form->createView(),
            'ayuda' => $ayuda,
        ));
    }

    public function habilitarAction(Request $request){
        $idOrg = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($idOrg);
        $hab = $organismo->habilitar();
        if($hab == 1){
            $em->persist($organismo);
            $em->flush();   
            return new JsonResponse(array(
                'success' => true,
                'mensaje' => 'Se habilito el organismo'
            ));
        }
//        elseif($hab == 2) {
//            $em->persist($organismo);
//            $em->flush();
//            return new JsonResponse(array(
//                'success' => true,
//                'mensaje' => 'Se habilito el organismo',
//            ));
        elseif($hab == 3){
            return new JsonResponse(array(
                'success' =>false,
                'mensaje' => 'No se pudo habilitar el organismo. Intente más tarde',
            )
                    );
        }
    }

    /**
     * Finds and displays a Organismo entity.
     *
     */
    public function showAction(Organismo $organismo)
    {
        $em = $this->getDoctrine()->getManager();
        $this->chequearDatos($organismo);

        $bloquearForm = $this->createBloquearForm($organismo);
        $limit = 5;
        $liquidaciones = $this->getDoctrine()->getManager()->getRepository('SiafcaIntranetBundle:Liquidacion')->getTheLastPresentadas($organismo->getId(), $limit);
        
        $estadoSinFirmante = $em->getRepository('SiafcaIntranetBundle:estado')->find(5);
        $estadoCreado = $em->getRepository('SiafcaIntranetBundle:estado')->find(2);
        
        $estado2 =  $organismo->getEstado();
       
        if($estado2 == 'Habilitado')
        {
            $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOrgShow'));
        }
        else if ($estado2 == 'Sin Firmante' || $estado2 == 'Creado')
        {
            $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOrgSdShow')); 
        }
        else if ($estado2 == 'Bloqueado')
        {
            $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOrgBlShow'));
        }
        
        
        $query = $em->createQuery(
                'SELECT count(f) FROM SiafcaIntranetBundle:Firmante f WHERE f.organismo = '.$organismo->getId() . ' AND f.estado = 35 '
            );
        $cant_activos = $query->getSingleScalarResult();
        $usuarioRes = $organismo->getUsuarioResponsable();
        
        if ($usuarioRes == Null){
            $organismo->setEstado($estadoCreado);
            $em->persist($organismo);
            $em->flush();
        }elseif ($usuarioRes != Null and $cant_activos == 0){
            $organismo->setEstado($estadoSinFirmante);
            $em->persist($organismo);
            $em->flush();
        }
        $domicilio = new Domicilio();
        $domicilio->setOrganismo($organismo);
        // El submit se atiende en el controlador de domicilio:
        $domicilioForm = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\DomicilioType',
            $domicilio,
            array('action' => $this->generateUrl(
                    'domicilio_new', 
                    array('id_entity' => $organismo->getId(), 
                        //'persona' => false
                        )
                    ),
                //'isOrganismo' => true
                )
            );

        $usuarioResponsable = new UsuarioOrganismo();
        
        //$type = [$this->getUser()->orgAdm()['roles'] , $this->getUser()->orgAdm()['adm'] ];

        $adm = $this->getUser()->orgAdm()['adm'];
        $roles = $this->getUser()->orgAdm()['roles'];
        
        
        $usuarioResponsableForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
                $usuarioResponsable,
                array('action' => $this->generateUrl(
                        'usuario_organismo.new_usuario',
                        array('id' => $organismo->getId())),
                    'discr' => $organismo->getDiscr(),
                    'type' => $adm,
                    'roles'=> $roles
                    )
        
              );  
//        if ($usuarioResponsableForm->isSubmitted() && $usuarioResponsableForm->isValid()) {
//        var_dump($usuarioResponsableForm->getData());
//        die;}
//        $usuarioResponsableForm = $this->createForm(
//                'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
//                $usuarioResponsable,
//                array('action' => $this->generateUrl(
//                        'usuario_organismo.new_usuario',
//                        array('id' => $organismo->getId())),
//                    'type' => $organismo->getDiscr(),
//                    'attr' => $this->getUser()->orgAdm(),
//
//
//                    )
//                );        
        //cargo el nombre de usuario
//        $usuarioResponsableForm->get('username')->setData($organismo->getCodigo());
        
        $editUsuarioResponsableForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
                $usuarioResponsable,
                array('action'=> $this->generateUrl(
                        'usuario_organismo.edit',
                        array('id' => $organismo->getId())
                        ),
                    'discr' => $organismo->getDiscr(),
                    'type' => $adm,
                    'roles'=> $roles
                    )
                );

        $editDomicilioForm = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\DomicilioType',
            $domicilio,
            array('action' => $this->generateUrl('domicilio_edit_ajax'),
                //'isOrganismo' => true
                )
        );
        
        $firmante = new Firmante(); // Se utiliza el mismo para la edicion
        $firmante->setOrganismo($organismo);
        // El submit de la nueva alta y de la edicion se atienden en el controlador de firmante:
        // (Se especifica el atributo action del formulario para que no apunte a este controlador)
        $firmanteForm = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\FirmanteType',
            $firmante,
            array('action' => $this->generateUrl(
                'firmante_new_ajax', 
                array('id' => $organismo->getId()
                    )
                )
            )
        );
        $editFirmanteForm = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\FirmanteType',
            $firmante,
            array('action' => $this->generateUrl('firmante_edit_ajax'))
        );
        
        

        $entidadResponsable = new EntidadResponsable();
        $entidadResponsableForm = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\EntidadResponsableType',
            $entidadResponsable,
            array('action' => $this->generateUrl('entidadresponsable_new_ajax', array('id' => $organismo->getId())))
        );
        $editEntidadResponsableForm = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\EntidadResponsableType',
            $entidadResponsable,
            array('action' => $this->generateUrl('entidadresponsable_new_ajax', array('id' => $organismo->getId())))
        );
        $faltantesForm = $this->createFormBuilder()
                ->add('anio', 'Symfony\Component\Form\Extension\Core\Type\NumberType',
                        array(
                            'label' => 'Año a consultar',
                            ),
                        array(
                            'id' => 'anio')
                        )
                ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array('label' => 'Consultar'))
                ->getForm();

return $this->render('organismo/show.html.twig', array(
            'organismo' => $organismo,
            'bloquear_form' => $bloquearForm->createView(),
            'liquidaciones' => $liquidaciones,
            'domicilioForm' => $domicilioForm->createView(),
            'editDomicilioForm' =>$editDomicilioForm->createView(),
            'firmanteForm' => $firmanteForm->createView(),
            'editFirmanteForm' => $editFirmanteForm->createView(),
            'entidadResponsableForm' => $entidadResponsableForm->createView(),
            'editEntidadResponsableForm' => $editEntidadResponsableForm->createView(),
            'usuarioResponsableForm' => $usuarioResponsableForm->createView(),
            'editUsuarioResponsableForm' => $editUsuarioResponsableForm->createView(),
            'ayuda' => $ayuda,
            'faltantesForm' => $faltantesForm->createView()
        ));
    }

    
     public function idCiudadanaOrganismoAjaxAction(Request $request)
    {
        $cuil = $request->query->get('cuil');
     
//        $cuil = $request->request->get('idCiudadana');
        
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
            
            return new JsonResponse(array(
            'success' => true,
            'resp' => $resp));
      
            
        }
    }

    public function faltantesAction(Request $request){
        $organismo = $request->request->get('organismo');
        $anio = $request->request->get('anio');
        $em = $this->getDoctrine()->getEntityManager();
        $faltantes = $em->getRepository('SiafcaIntranetBundle:Organismo')
                ->faltantesXanioYorg($anio,$organismo);


        return new JsonResponse(array(
            'success' => true,
            'faltantes' => $faltantes));
        


    }
    /**
     * Displays a form to edit an existing Organismo entity.
     *
     */
    public function editAction(Request $request, Organismo $organismo)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOrgEdit'));

        $deleteForm = $this->createDeleteForm($organismo);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\OrganismoType', $organismo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organismo);
            $em->flush();

            return $this->redirectToRoute('organismo_show', array('id' => $organismo->getId()));
        }

        return $this->render('organismo/edit.html.twig', array(
            'organismo' => $organismo,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Deletes a Organismo entity.
     *
     */
    public function deleteAction(Request $request, Organismo $organismo)
    {
        $form = $this->createDeleteForm($organismo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($organismo);
            $em->flush();
        }

        return $this->redirectToRoute('organismo_index');
    }

    /**
     * Creates a form to delete a Organismo entity.
     *
     * @param Organismo $organismo The Organismo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Organismo $organismo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('organismo_delete', array('id' => $organismo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Creates a form to delete a Firmante entity.
     *
     * @param Firmante $firmante The Firmante entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteFormFirmante(Firmante $firmante)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('firmante_bloquear', array('id' => $firmante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    private function getSequenceValue($query,$subquery)
    {
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($query); 
        $stmt->execute();
        $arrayResult = $stmt->fetchAll();
//        var_dump($arrayResult);die;
        $nextValue = $arrayResult[0][$subquery];
        return $nextValue;
    }
    
    private function chequearDatos($organismo)
    {
          
        $em = $this->getDoctrine()->getManager();
        $succes = false;
       
        if ($organismo->debeHabilitarse()) {    //Chequea si se debe habilitar un Organismo 
        // Si tiene firmante y usuario responsable
        
//            if ($organismo->getTipoOrganismo()->getCodigo() == '5'){     // Organismos Adheridos
//                $nextValue = $this->getSequenceValue("Select TO_CHAR(ORG_AD_SEQ.NEXTVAL, 'FM00000') from dual","TO_CHAR(ORG_AD_SEQ.NEXTVAL,'FM00000')");
//            } elseif ($organismo->getTipoOrganismo()->getCodigo() == '2'){     // Municipalidad o Comuna
//                $nextValue = $this->getSequenceValue("Select TO_CHAR(MUN_COM_SEQ.NEXTVAL, 'FM000') from dual","TO_CHAR(MUN_COM_SEQ.NEXTVAL,'FM000')");
//            } else {
//                $nextValue = null;
//            }
            $succes = $organismo->habilitar($nextValue);
            
        }elseif ($organismo->estadoCreado()){
            $succes = $organismo->isCreado(); 
        
        }elseif ($organismo->debeSerConUsuarioResponsable()) { // Chequea si tiene usuario responsable para cambiar de estado creado a sin firmante
            $succes = $organismo->estadoConUsuarioResponsable();
           
        } elseif ($organismo->debeSerSinFirmante()) { //Chequea si no tiene firmante, para cambiar su estado
            $succes = $organismo->estadoSinFirmante();
        } elseif ($organismo->debeSerConFirmante()) {
            $succes = $organismo->estadoConFirmante();
        }
        if ($succes) { 
            $em->persist($organismo);
            $em->flush(); 
        }
    }
    
    public function bloquearAction(Request $request, Organismo $organismo)
    {
        $form = $this->createBloquearForm($organismo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $organismo->bloquear();
            $em->persist($organismo);
            $em->flush();
        }

        return $this->redirectToRoute('organismo_index');
    }
    
    private function createBloquearForm(Organismo $organismo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('organismo_bloquear', array('id' => $organismo->getId())))
            ->setMethod('POST')
            ->getForm()
        ;
    }
    
    public function searchAction(Request $request)
    {
    }

    /**
     * 
     * @return array
     */
    private function getFormFilterAttrs()
    {
        
        $formAttrs = array(
            array(
                'type' => 'SubmitType',
                'name' => 'todos',
                'class' => 'btn-default',
                'label' => 'Todos',
                'dql' => '',
                'subtitulo' => 'Todos'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'escPrivadas',
                'class' => 'btn-primary-bt',
                'label' => 'Esc. Privadas',
                'dql' => " AND t.codigo = '0'",
                'subtitulo' => 'Escuelas Privadas'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'adheridos',
                'class' => 'btn-success',
                'label' => 'Adheridos',
                'dql' => " AND t.codigo = '5'",
                'subtitulo' => 'Adheridos'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'adminCentral',
                'class' => 'btn-info',
                'label' => 'Admin. Central',
                'dql' => " AND t.codigo = '1'",
                'subtitulo' => 'Administración Central'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'desentralizados',
                'class' => 'btn-primary',
                'label' => 'Descentralizados',
                'dql' => " AND t.codigo = '4'",
                'subtitulo' => 'Desentralizados'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'muniComuna',
                'class' => 'btn-grey',
                'label' => 'Municipalidad/Comuna',
                'dql' => " AND t.codigo = '2'",
                'subtitulo' => 'Municipalidades y Comunas'
            )
        );
        
        
        return $formAttrs;
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
                'placeholder' => 'Código Organismo',
                'label' => false,
                'dql' => " AND o.codigo LIKE '",
                'subtitulo' => 'Organismos con código '
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
     * @param \Symfony\Component\Form\Form $filterForm
     * @return array
     */
    private function getFilterResults(Form $filterForm, Form $searchForm)
    {
        $formFilter = $this->get('form.filter');
        $defaultSubtitulo = 'Todos';
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
        
        $filters = $formFilter->getFilterResults($form, $isButtonForm, $formAttrs, $defaultSubtitulo);
        
        return $filters;
    }

    /**
     * Retorna los datos de un aportante en JSON. Para utilizar via AJAX.
     * @param Request $request
     * @return Response Respuesta codificada en JSON
     */
    public function getSingleAportanteAction(Request $request)
    {
        $idOrganismo = $request->request->get('idOrganismo');
        $cuilPersona = $request->request->get('cuilPersona');
        $idExpte = $request->request->get('idExpte');
        $em = $this->get('doctrine')->getManager();
        $aportante = $em->getRepository('SiafcaIntranetBundle:Aportante')->getAportanteByCuil($idOrganismo, $cuilPersona, $idExpte);

        return new Response(json_encode($aportante));
    }

    /**
     * Manejador para vista de busqueda de organismos
     * @param Request $request
     * @return type
     */
    public function filterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOrgFilt'));


        $usuario = $this->getUser();
//        dump($usuario);
//        die;
        $orgAdm = $usuario->orgAdm();
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') || $orgAdm['adm'] == 'oficina'){
        $dql= "SELECT "
                . "partial o.{id, nombre, codigo, cuit}, "
                . "partial t.{id, codigo, nombre}, "
                . "partial d.{id}, "
                . "partial l.{id, nombre} "
            . "FROM "
                . "SiafcaIntranetBundle:Organismo o "
                . "JOIN o.tipoOrganismo t "
                . "LEFT JOIN o.domicilios d "
                . "LEFT JOIN d.localidad l "
                . "JOIN o.estado e "
            . "WHERE o INSTANCE OF Caja\SiafcaIntranetBundle\Entity\Organismo";
                
        }
        else{
            foreach($orgAdm['id'] as $id){
               $idOrg[] = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($id);
            }
        $dql= "SELECT "
                . "partial o.{id, nombre, codigo, cuit}, "
                . "partial t.{id, codigo, nombre}, "
                . "partial d.{id}, "
                . "partial l.{id, nombre} "
            . "FROM "
                . "SiafcaIntranetBundle:Organismo o "
                . "JOIN o.tipoOrganismo t "
                . "LEFT JOIN o.domicilios d "
                . "LEFT JOIN d.localidad l "
                . "JOIN o.estado e "
            . "WHERE o INSTANCE OF Caja\SiafcaIntranetBundle\Entity\Organismo AND o.id IN (:orgIDS)";

        $dql .= " ORDER BY o.id DESC";
        }
        
        
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\SearchOrganismoType', null, array(
            'method' => 'GET',
        ));
        $form->handleRequest($request);

        // Añade condiciones a la consulta dependiendo de los campos llenados en el formulario
        if ($form->isSubmitted() && $form->isValid()) {
            $submit = $form->getData();
            if ($submit['nombre']) { $dql .= " AND UPPER(o.nombre) like '%".strtoupper($submit['nombre'])."%'"; }
            if ($submit['cuit']) { $dql .= " AND o.cuit LIKE '".$submit['cuit']."%'"; }
            if ($submit['tipoOrganismo']) { $dql .= " AND t.id = ".$submit['tipoOrganismo']->getId(); }
            if ($submit['fechaInicio']) { $dql .= " AND o.fechaAlta >= '".$submit['fechaInicio']->format('Y-m-d')." 00:00:00'"; }
            if ($submit['fechaAprobacion']) { $dql .= " AND o.fechaAprobacion >= '".$submit['fechaAprobacion']->format('Y-m-d')." 00:00:00' AND o.fechaAprobacion <= '".$submit['fechaAprobacion']->format('Y-m-d')." 23.59.59'"; }
            if ($submit['fechaFinal']) { $dql .= " AND o.fechaBaja <= '".$submit['fechaFinal']->format('Y-m-d')." 23.59.59'"; }
            if ($submit['expediente']) { $dql .= " AND o.expediente LIKE '".$submit['expediente']."%'"; }
            if ($submit['codigo']) { $dql .= " AND o.codigo LIKE '".$submit['codigo']."%'"; }
            if ($submit['resolucion']) { $dql .= " AND o.resolucion LIKE '".$submit['resolucion']."%'"; }
            if ($submit['zona']) { $dql .= " AND z.id = ".$submit['zona']->getId(); }
            if ($submit['estado']) { $dql .= " AND e.id = ".$submit['estado']->getId(); }
        }
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') || $orgAdm['adm'] == 'oficina'){
        $query = $em->createQuery($dql);
        }else{
                    $query = $em->createQuery($dql)->setParameter('orgIDS',$idOrg);
        }

        $paginator = $this->get('knp_paginator');
        $filtrados = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20,
            array('defaultSortFieldName' => 'o.nombre', 'defaultSortDirection' => 'asc')
        );
        
        return $this->render('organismo/search.html.twig', array(
            'form' => $form->createView(),
            'organismos' => $filtrados,
            'ayuda' => $ayuda
        ));
    }
    public function testWSAction(){
        $em = $this->getDoctrine()->getManager();

        $organismo= $em->getRepository('SiafcaIntranetBundle:Organismo')->find("313");
        
        
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array(
            'cuil'=> "27247842034"));
       
        
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->ordenarAportantes($organismo);
        
        foreach ($aportantes as $item ){
            var_dump($item->getPersona()->getapellidoPat()."<br>");
        //\Symfony\Component\VarDumper\VarDumper::dump($item->getId());
        }
        die;
        
      // return $this->redirectToRoute('organismo_index');
        
    }
}
