<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Caja\SiafcaIntranetBundle\Entity\Concepto;
use Caja\SiafcaIntranetBundle\Form\ConceptoType;

/**
 * Concepto controller.
 *
 */
class ConceptoController extends Controller
{
    /**
     * Lists all Concepto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $conceptos = $em->getRepository('SiafcaIntranetBundle:Concepto')->findAll();

        return $this->render('concepto/index.html.twig', array(
            'conceptos' => $conceptos,
        ));
    }

    /**
     * Creates a new Concepto entity.
     *
     */
    public function newAction(Request $request)
    {
        $concepto = new Concepto();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\ConceptoType', $concepto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($concepto);
            $em->flush();

            return $this->redirectToRoute('concepto_show', array('id' => $concepto->getId()));
        }

        return $this->render('concepto/new.html.twig', array(
            'concepto' => $concepto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Concepto entity.
     *
     */
    public function showAction(Concepto $concepto)
    {
        $deleteForm = $this->createDeleteForm($concepto);

        return $this->render('concepto/show.html.twig', array(
            'concepto' => $concepto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Concepto entity.
     *
     */
    public function editAction(Request $request, Concepto $concepto)
    {
        $deleteForm = $this->createDeleteForm($concepto);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\ConceptoType', $concepto);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($concepto);
            $em->flush();

            return $this->redirectToRoute('concepto_edit', array('id' => $concepto->getId()));
        }

        return $this->render('concepto/edit.html.twig', array(
            'concepto' => $concepto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Concepto entity.
     *
     */
    public function deleteAction(Request $request, Concepto $concepto)
    {
        $form = $this->createDeleteForm($concepto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($concepto);
            $em->flush();
        }

        return $this->redirectToRoute('concepto_index');
    }

    /**
     * Creates a form to delete a Concepto entity.
     *
     * @param Concepto $concepto The Concepto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Concepto $concepto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('concepto_delete', array('id' => $concepto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
