<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\ExpteAmparo;
use Caja\SiafcaIntranetBundle\Entity\ExpteAmpItemConcepto;
use Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem;
use Caja\SiafcaIntranetBundle\Entity\Cargo;
use Caja\SiafcaIntranetBundle\Form\ExpteAmparoType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Caja\SiafcaIntranetBundle\Util\Util;

/**
 * Cargo controller.
 *
 */
class AmparoController extends Controller
{
    /**
    
     * IntraAmpNew : newAction
     * IntraAmpInd : indexAction     
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $formFilter = $this->get('form.filter');
        $organismo=$em->find('SiafcaIntranetBundle:Organismo', $id);
        $route = $this->generateUrl('amparo_index', array('id' => $organismo->getId()));
        $dql   = "SELECT ea FROM SiafcaIntranetBundle:ExpteAmparo ea JOIN ea.organismo o WHERE o.id = ". $id;
        $searchForm = $formFilter->createFilterForm($route ,$this->getSearchFormFilterAttrs(), 'searchForm') ;
        $searchForm->handleRequest($request);
        $filters = $this->getFilterResults( $searchForm, $organismo);
        $subtitulo = $filters['subtitulo'];
        $dql .= $filters['dql'];
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $amparos = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        $concepto = new ExpteAmpItemConcepto();
        $conceptoForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\ExpteAmpItemConceptoType', $concepto);
        $conceptoForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraAmpIndex'));
        
        //Atiende el llamado ajax
        if ($conceptoForm->isSubmitted() && $conceptoForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($concepto);
            $em->flush();
            if (!is_null($concepto->getId())) {
                $response = array(
                    'success' => true,
                    'concepto' => array(
                        'id' => $concepto->getId(),
                        'nombre' => $concepto->getConcepto()->getNombre(),
                        'finicio' => $concepto->getFechaInicio()->format('d/m/Y'),
                        'ffin' => $concepto->getFechaFin()->format('d/m/Y'),
                        'porcentaje' => $concepto->getPorcentaje(),
                    )
                );
            } else {
                $response = array(
                    'success' => false,
                    'msg' => 'Error al dar de alta el concepto, intente más tarde'
                );
            }
            die(json_encode($response));
        } else if ($conceptoForm->isSubmitted() && !$conceptoForm->isValid()) {
            $Utils = new Util();
            $errors = $Utils->getErrorMessages($conceptoForm);
            $response = array(
                'success' => false,
                'errors' => $errors,
                'msg' => 'Verifique los campos e intente nuevamente'
            );
            die(json_encode($response));
        }

        return $this->render('amparo/index.html.twig', array(
            'idOrganismo' => $id,
            'amparos' => $amparos,
            'titulo' => 'Amparos',
            'subtitulo' => $subtitulo,
            'searchForm' => $searchForm->createView(),
            'concepto' => $concepto,
            'conceptoForm' => $conceptoForm->createView(),
            'ayuda'=> $ayuda,
        ));
    }

    /**
     * Creates a new ExpteAmparo entity.
     *
     */
    
    public function newAction(Request $request, $orgId)
    {
        $expteAmparo = new ExpteAmparo();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\ExpteAmparoType', $expteAmparo);
        $form->handleRequest($request);
        
       $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraAmpNew'));


        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
                'name' => 'numero',
                'placeholder' => 'Numero Expediente',
                'label' => false,
                'dql' => " AND ea.numero LIKE '",
                'subtitulo' => 'Expedientes con Numero '
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
     * @param string $organismo
     * @return array
     */
    private function getFilterResults(Form $searchForm, $organismo)
    {
        $formFilter = $this->get('form.filter');
        $defaultSubtitulo = $organismo->__toString();
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
}
