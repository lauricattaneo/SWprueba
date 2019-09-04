<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\Cargo;
use Caja\SiafcaIntranetBundle\Form\CargoType;
use Symfony\Component\Form\Form;

/**
 * Cargo controller.
 *
 */
class CargoController extends Controller
{
    /**
     * Lists all Cargo entities.
     * IntraCarInd - indexAction
     * IntraCarShow - showAction
     * IntraCarEdit - editAction
     *
     */
    public function indexAction(Request $request,Organismo $organismo)
    {
        $em = $this->getDoctrine()->getManager();
        $formFilter = $this->get('form.filter');
        $route = $this->generateUrl('cargo_index', array('id' => $organismo->getId()));
        $dql   = "SELECT c FROM SiafcaIntranetBundle:Cargo c JOIN c.organismo o JOIN c.sectorPasivo s WHERE o.id = ". $organismo->getId();
        $searchForm = $formFilter->createFilterForm($route ,$this->getSearchFormFilterAttrs(), 'searchForm') ;
        $searchForm->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraCarIndex'));

        
        $filters = $this->getFilterResults( $searchForm, $organismo->__toString());
        $subtitulo = $filters['subtitulo'];
        $dql .= $filters['dql'];
        $query = $em->createQuery($dql);
        
        $paginator  = $this->get('knp_paginator');
        $cargos = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
        $sub = 'Cargos de '.$organismo;
        return $this->render('cargo/index.html.twig', array(
            'cargos' => $cargos,
            'orgId'=> $organismo->getId(),
            'titulo' => 'Cargos',
            'sub' => $sub,
            'subtitulo' => $subtitulo,
            'searchForm' => $searchForm->createView(),
            'ayuda' => $ayuda,
        ));
    }

    /**
     * Creates a new Cargo entity.
     *
     */
    public function newAction(Request $request)
    {
        $cargo = new Cargo();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\CargoType', $cargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cargo);
            $em->flush();

            return $this->redirectToRoute('cargo_show', array('id' => $cargo->getId()));
        }

        return $this->render('cargo/edit.html.twig', array(
            'cargo' => $cargo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Cargo entity.
     *
     */
    public function showAction(Cargo $cargo)
    {
        $deleteForm = $this->createDeleteForm($cargo);
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraCarShow'));

        
        return $this->render('cargo/show.html.twig', array(
            'cargo' => $cargo,
            'delete_form' => $deleteForm->createView(),
            'ayuda' => $ayuda,
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
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraCarEdit'));

        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cargo);
            $em->flush();

            return $this->redirectToRoute('cargo_show', array('id' => $cargo->getId()));
        }

        return $this->render('cargo/edit.html.twig', array(
            'cargo' => $cargo,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ayuda' => $ayuda,
        ));
    }

    /**
     * Deletes a Cargo entity.
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

        return $this->redirectToRoute('cargo_index', array('id' => $request->request->get('organismo')));
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
                'placeholder' => 'Código Cargo',
                'label' => false,
                'dql' => " AND c.codigo LIKE '",
                'subtitulo' => 'Cargos con Código '
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
        $defaultSubtitulo = 'Cargos de '. $organismo;
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
}
