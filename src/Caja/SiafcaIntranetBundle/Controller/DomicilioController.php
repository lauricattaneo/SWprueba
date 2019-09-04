<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Caja\SiafcaIntranetBundle\Entity\Domicilio;
use Caja\SiafcaIntranetBundle\Form\DomicilioType;
use DateTime;
use Caja\SiafcaIntranetBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Domicilio controller.
 *
 */
class DomicilioController extends Controller
{
    const TIPO_DOMICILIO_LEGAL = 2;

    /**
     * Lists all Domicilio entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $domicilios = $em->getRepository('SiafcaIntranetBundle:Domicilio')->findAll();

        return $this->render('domicilio/index.html.twig', array(
            'domicilios' => $domicilios,
        ));
    }

    /**
     * Creates a new Domicilio entity.
     *
     */
    public function newAction(Request $request, $id_entity)
    {
        $em = $this->getDoctrine()->getManager();
        $domicilio = new Domicilio();
        if ($request->query->get('persona'))
        {
            $entity = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($id_entity);
            $route = 'persona_show';
            $isOrganismo = false;
            $tipoDomicilio = $em->getRepository('SiafcaIntranetBundle:TipoDomicilio')->findOneById(self::TIPO_DOMICILIO_LEGAL);
            $domicilio->setTipoDomicilio($tipoDomicilio);
        }
        else
        {
            $entity = $em->getRepository('SiafcaIntranetBundle:Oficina')->findOneById($id_entity);
            $route = 'organismo_show';
            //$isOrganismo = true;
        }
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\DomicilioType', $domicilio,array(
            //'isOrganismo' => $isOrganismo,
            'action' => $this->generateUrl('domicilio_new',array(
                'id_entity' => $entity->getId(),
                //'persona' => !$isOrganismo
            )),
            //'isOrganismo' => $isOrganismo
        ));
        $form->handleRequest($request);

        // If is a ajax call, the function dies with the return data
        $ajax = $request->request->get('ajax');
        if ($form->isSubmitted() && $form->isValid()) {
            $domicilioAEliminar = $entity->setDomicilio($domicilio);
            $removedId = null;
            if ($domicilioAEliminar)
            {
                $removedId = $domicilioAEliminar->getId();
                $em->remove($domicilioAEliminar);
            }
            $em->persist($domicilio);
            $em->flush();

            if ($ajax) {
                if ($domicilio->getId()) {
                    $response = array(
                        'success' => true,
                        'address' => array(
                            'id' => $domicilio->getId(),
                            'calle' => $domicilio->getCalle(),
                            'numero' => $domicilio->getNumero(),
                            'localidad' => $domicilio->getLocalidad()->getNombre(),
                            'codPostal' => $domicilio->getLocalidad()->getCodPostal(),
                            'deleted' => $removedId
                        )
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => 'Error al dar de alta el domicilio, intente más tarde'
                    );
                }
                die(json_encode($response));
            } else {
                return $this->redirectToRoute($route, array('id' => $entity->getId()));
            }
        } else if ($ajax && $form->isSubmitted() && !$form->isValid()) {
            $Utils = new Util();
            $errors = $Utils->getErrorMessages($form);
            $response = array(
                'success' => false,
                'errors' => $errors,
                'msg' => 'Verifique los campos e intente nuevamente'
            );
            die(json_encode($response));
        }

        return $this->render('modal.html.twig', array(
            'form' => $form->createView(),
            'titulo' => 'Domicilio de '.$entity->__toString(),
            'modalId' => 'modalDomicilio'
        ));
    }

    /**
     * Finds and displays a Domicilio entity.
     *
     */
    public function showAction(Domicilio $domicilio)
    {
        $deleteForm = $this->createDeleteForm($domicilio);

        return $this->render('domicilio/show.html.twig', array(
            'domicilio' => $domicilio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Domicilio entity.
     *
     */
    public function editAction(Request $request, Domicilio $domicilio)
    {
        $deleteForm = $this->createDeleteForm($domicilio);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\DomicilioType', $domicilio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($domicilio);
            $em->flush();

            return $this->redirectToRoute('domicilio_edit', array('id' => $domicilio->getId()));
        }

        return $this->render('domicilio/edit.html.twig', array(
            'domicilio' => $domicilio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    public function editAjaxAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $domicilioData = new Domicilio();
            if (!$request->query->get('persona')) {
                $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\DomicilioType', $domicilioData, array(
                    'isOrganismo' => true,
                ));
                $tipoDomicilio = $em->getRepository('SiafcaIntranetBundle:TipoDomicilio')->findOneById(self::TIPO_DOMICILIO_LEGAL);
                $domicilioData->setTipoDomicilio($tipoDomicilio);
            } else {
                $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\DomicilioType', $domicilioData);
            }
            
        $form->handleRequest($request);
            $idDom = $form->get('id')->getData();
            if ($form->isSubmitted() && $form->isValid()) {
                $domicilio = $em->getRepository('SiafcaIntranetBundle:Domicilio')->find($idDom);

                if (!$domicilio) {
                    $response = array(
                        'success' => false,
                        'msg' => 'No existe un domicilio con id' . $idDom,
                    );
                }

                $domicilio->setCalle($domicilioData->getCalle());
                $domicilio->setNumero($domicilioData->getNumero());
                $domicilio->setPiso($domicilioData->getPiso());
                $domicilio->setManzana($domicilioData->getManzana());
                $domicilio->setLote($domicilioData->getLote());
                $domicilio->setMonoblock($domicilioData->getMonoblock());
                $domicilio->setLocalidad($domicilioData->getLocalidad());
                $domicilio->setTipoDomicilio($domicilioData->getTipoDomicilio());

                $em->flush();

                $response = array(
                    'success' => true,
                    'msg' => 'Domicilio editado',
                );
            } elseif ($form->isSubmitted() && !$form->isValid()) {
                $Utils = new Util();
                $errors = $Utils->getErrorMessages($form);
                $response = array(
                    'success' => false,
                    'errors' => $errors,
                    'msg' => 'Verifique los campos e intente nuevamente'
                );
            }

            return new JsonResponse($response);
        }
    }

    public function queryAjaxAction(Request $request, Domicilio $domicilio){
        if($request->isXmlHttpRequest()){
        
            return new JsonResponse(array(
                'id' => $domicilio->getId(),
                'calleNumero' => array(
                    'calle' => $domicilio->getCalle(),
                    'numero' =>$domicilio->getNumero()),
                'pisoDepto' => array(
                    'piso' => $domicilio->getPiso(),
                    'depto' => $domicilio->getDepto()),
                'manzanaLoteMono' => array(
                    'manzana' => $domicilio->getManzana(),
                    'lote' => $domicilio->getLote(),
                    'monoblock' => $domicilio->getMonoblock()),
                'tipoDomicilio' => $domicilio->getTipoDomicilio()->getId(),
                
                'Provincia' => $domicilio->getLocalidad()->getDepartamento()->getProvincia()->getId(),
                
                'Departamento' => $domicilio->getLocalidad()->getDepartamento()->getId(),
                'Localidad' => $domicilio->getLocalidad()->getId(),
                ));
            
        }
    }
    
    /**
     * Deletes a Domicilio entity.
     *
     */
    public function deleteAction(Request $request, Domicilio $domicilio)
    {
        $form = $this->createDeleteForm($domicilio);
        $form->handleRequest($request);
        $isAjaxCall = $request->request->get('ajax');

        if ($isAjaxCall == '1' || $form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($domicilio);
                $em->flush();
                if ($isAjaxCall == '1') { die(\json_encode(array('success' => true))); }
            } catch (\Exception $ex) {
                if ($isAjaxCall == '1') { die(\json_encode(array('success' => false))); }
            }
        }

        return $this->redirectToRoute('domicilio_index');
    }

    /**
     * Creates a form to delete a Domicilio entity.
     *
     * @param Domicilio $domicilio The Domicilio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Domicilio $domicilio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('domicilio_delete', array('id' => $domicilio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    
    public function renderAction(Domicilio $domicilio)
    {
        return $this->render('domicilio/render.html.twig', array(
            'domicilio' => $domicilio
        ));
    }
}
