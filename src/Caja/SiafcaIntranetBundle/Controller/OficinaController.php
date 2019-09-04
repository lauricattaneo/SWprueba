<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Oficina;
use Caja\SiafcaIntranetBundle\Entity\Firmante;
use Caja\SiafcaIntranetBundle\Entity\Domicilio;
use Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Oficna controller.
 * AYUDA:
 * IntraOfiInd - indexAction
 * IntraOfiNew - newAction
 * IntraOfiShow - showAction
 * IntraOfiEdit - editAction
 * IntraOfiFilt - filterAction
 *
 */
class OficinaController extends Controller
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
                'FROM SiafcaIntranetBundle:Oficina o ' .
                'WHERE UPPER(o.nombre) LIKE :filter ' .
                    'AND o INSTANCE OF SiafcaIntranetBundle:Oficina ' .
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
     * Lists all Oficina entities.
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOfiIndex'));

        $dql= "SELECT "
                . "partial o.{id, nombre}, "
                . "partial d.{id}, "
                . "partial l.{id, nombre} "
            . "FROM "
                . "SiafcaIntranetBundle:Oficina o "
                . "LEFT JOIN o.domicilios d "
                . "LEFT JOIN d.localidad l "
                . "WHERE o INSTANCE OF :type";
        $query = $em->createQuery($dql);
        $query->setParameter('type', $em->getClassMetadata('Caja\SiafcaIntranetBundle\Entity\Oficina'));
        
        $paginator  = $this->get('knp_paginator');
        $oficinas = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1) /*page number*/,
            10, /*limit per page*/
            array('defaultSortFieldName' => 'o.nombre', 'defaultSortDirection' => 'asc')
        );
        
        return $this->render('oficina/index.html.twig', array(
            'oficinas' => $oficinas,
            'ayuda' => $ayuda
        ));
    }

    /**
     * Creates a new Oficina entity.
     */
    public function newAction(Request $request)
    {
        $oficina = new Oficina();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\OficinaType', $oficina);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOfiNew'));


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($oficina);
            $em->flush();
            return $this->redirectToRoute('oficina_show', array('id' => $oficina->getId()));
        }

        return $this->render('oficina/edit.html.twig', array(
            'oficina' => $oficina,
            'form' => $form->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Finds and displays a Oficina entity.
     */
    public function showAction(Request $request, Oficina $oficina)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOfiShow'));
                
        $user = $this->getUser();
        $orgAdm = $user->orgAdm();

   //     if(($orgAdm['roles'] == 'ROLE_CONTRALOR_ADMIN' || $orgAdm['roles'] == 'ROLE_CONTRALOR_USER') && $orgAdm['id'] != $oficina->getId())
//      if(in_array('ROLE_CONTRALOR_ADMIN',$orgAdm['roles']) ||  in_array('ROLE_CONTRALOR_USER',$orgAdm['roles']) && $orgAdm['id'] != $oficina->getId())
//       {
//         // return $this->redirectToRoute('oficina_show', array('id' => $oficina->getId()));
//       }
        if(($orgAdm['roles'] == 'ROLE_CONTRALOR_ADMIN' || $orgAdm['roles'] == 'ROLE_CONTRALOR_USER') && $orgAdm['id'] != $oficina->getId())
            return $this->redirectToRoute('oficina_show', array('id' => $orgAdm['id']));
       
       $domicilio = new Domicilio();
        $domicilio->setOrganismo($oficina);
                // El submit se atiende en el controlador de domicilio:
                $domicilioForm = $this->createForm(
                    'Caja\SiafcaIntranetBundle\Form\DomicilioType',
                    $domicilio,
                    array(
                        'action' => $this->generateUrl('domicilio_new',
                                array(
                                    'id_entity' => $oficina->getId(),
                                    'persona' => false)
                                ),)
        
                );
        $editDomicilioForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\DomicilioType',
                $domicilio,
                array(
                    'action' => $this->generateUrl('domicilio_edit_ajax',
                            array(
                                'id_entity' => $oficina->getId(),
                                'persona'=> false)
                            ),)
                 
                );
        
        $usuarioResponsable = new UsuarioOrganismo();

        $adm = $this->getUser()->orgAdm()['adm'];
        $roles = $this->getUser()->orgAdm()['roles'];
        
        $usuarioResponsableForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
                $usuarioResponsable,
                array('action' => $this->generateUrl(
                        'usuario_organismo.new_usuario',
                        array('id' => $oficina->getId())),
                    'discr' => $oficina->getDiscr(),
                    'type' => $adm,
                    'roles'=> $roles
                        
                    )
                );

        $editUsuarioResponsableForm = $this->createForm(
                'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
                $usuarioResponsable,
                array('action'=> $this->generateUrl(
                        'usuario_organismo.edit',
                        array('id' => $oficina->getId())),
                        'discr' => $oficina->getDiscr(),
                        'type' => $adm,
                        'roles'=> $roles        
                    )
                );


        return $this->render('oficina/show.html.twig', array(
            'oficina' => $oficina,
            'domicilioForm' => $domicilioForm->createView(),
            'usuarioResponsableForm' => $usuarioResponsableForm->createView(),
            'editUsuarioResponsableForm' => $editUsuarioResponsableForm->createView(),
            'editDomicilioForm' => $editDomicilioForm->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Displays a form to edit an existing Oficina entity.
     */
    public function editAction(Request $request, Oficina $oficina)
    {
//        $this->denyAccessUnlessGranted('edit', $oficina);
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOfiEdit'));

        $deleteForm = $this->createDeleteForm($oficina);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\OficinaType', $oficina);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($oficina);
            $em->flush();

            return $this->redirectToRoute('oficina_show', array('id' => $oficina->getId()));
        }

        return $this->render('oficina/edit.html.twig', array(
            'oficina' => $oficina,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ayuda' => $ayuda
        ));
    }

    /**
     * Deletes a Oficina entity.
     *
     */
    public function deleteAction(Request $request, Oficina $oficina)
    {
        $form = $this->createDeleteForm($oficina);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($oficina);
            $em->flush();
        }

        return $this->redirectToRoute('oficina_index');
    }

    /**
     * Creates a form to delete a Oficina entity.
     *
     * @param Oficina $oficina The Organismo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Oficina $oficina)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('oficina_delete', array('id' => $oficina->getId())))
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
        $nextValue = $arrayResult[0][$subquery];
        return $nextValue;
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
                'dql' => " WHERE t.codigo = '0'",
                'subtitulo' => 'Escuelas Privadas'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'adheridos',
                'class' => 'btn-success',
                'label' => 'Adheridos',
                'dql' => " WHERE t.codigo = '5'",
                'subtitulo' => 'Adheridos'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'adminCentral',
                'class' => 'btn-info',
                'label' => 'Admin. Central',
                'dql' => " WHERE t.codigo = '1'",
                'subtitulo' => 'Administración Central'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'desentralizados',
                'class' => 'btn-primary',
                'label' => 'Descentralizados',
                'dql' => " WHERE t.codigo = '4'",
                'subtitulo' => 'Desentralizados'
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'muniComuna',
                'class' => 'btn-grey',
                'label' => 'Municipalidad/Comuna',
                'dql' => " WHERE t.codigo = '2'",
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
                'dql' => " WHERE o.codigo LIKE '",
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
     * Manejador para vista de busqueda de organismos
     * @param Request $request
     * @return type
     */
    public function filterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraOfiFilt'));


        // Consulta base:
        $dql= "SELECT "
                . "partial o.{id, nombre}, "
                . "partial d.{id}, "
                . "partial l.{id, nombre} "
            . "FROM "
                . "SiafcaIntranetBundle:Oficina o "
                . "LEFT JOIN o.domicilios d "
                . "LEFT JOIN d.localidad l "
                . "LEFT JOIN o.zona z "
                . "WHERE o INSTANCE OF Caja\SiafcaIntranetBundle\Entity\Oficina ";

        $oficina = new Oficina();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\SearchOficinaType', $oficina, array(
            'method' => 'GET',
        ));
        $form->handleRequest($request);

        // Añade condiciones a la consulta dependiendo de los campos llenados en el formulario
        if ($form->isSubmitted() && $form->isValid()) {
            if ($oficina->getNombre()) { $dql .= " AND UPPER(o.nombre) like '%".strtoupper($oficina->getNombre())."%'"; }
            if ($oficina->getCuit()) { $dql .= " AND o.cuit LIKE '".$oficina->getCuit()."%'"; }
            if ($oficina->getZona()) { $dql .= " AND z.id = ".$oficina->getZona()->getId(); }
        }

        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $filtrados = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20,
            array('defaultSortFieldName' => 'o.nombre', 'defaultSortDirection' => 'asc')
        );

        return $this->render('oficina/search.html.twig', array(
            'form' => $form->createView(),
            'oficinas' => $filtrados,
            'ayuda' => $ayuda
        ));
    }
}
