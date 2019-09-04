<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\ExpteAmparo;
use Caja\SiafcaIntranetBundle\Form\ExpteAmparoType;
use Symfony\Component\Form\Form;

/**
 * Cargo controller.
 *
 */
class AmparoItemController extends Controller
{
    /**
     * Lists all ExpteAmparoItem entities.
     *
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $expteAmparo = $em->getRepository('SiafcaIntranetBundle:ExpteAmparo')->find($id);
        
        $expteAmparoItems = $em->getRepository('SiafcaIntranetBundle:ExpteAmpItem')->findByexpteAmparo($id);
        
        return $this->render('amparoItem/index.html.twig', array(
            'expteAmparoItems' => $expteAmparoItems,
            'expteAmapro' => $expteAmparo,
        ));
    }

    /**
     * Creates a new ExpteAmparo entity.
     *
     */
    
    public function newAction(Request $request, $orgId)
    {
       // die('acaaaa');

        $expteAmparo = new ExpteAmparo();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\ExpteAmparoType', $expteAmparo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($expteAmparo);
            $em->flush();

            return $this->redirectToRoute('amparo_show', array('id' => $expteAmparo->getId()));
        }
        return $this->render('amparo/new.html.twig', array(
            'idOrganismo' => $orgId,
            'expteAmparo' => $expteAmparo,
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
    
}