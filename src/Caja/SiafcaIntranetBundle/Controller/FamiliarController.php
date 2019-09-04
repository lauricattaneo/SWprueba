<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Familiar;
use Caja\SiafcaIntranetBundle\Entity\Persona;
use Caja\SiafcaIntranetBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

/**
 * Familiar controller.
 *
 */
class FamiliarController extends Controller {

    /**
     * Creacion asincronica de familiar (y persona)
     * @param Request $request
     * @param $id_persona
     * @return JsonResponse
     */
    public function newAjaxAction(Request $request, $id_persona) {
        /*
        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $familiar = new Familiar();
            $familiarTitular = new Familiar();
            $titular = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($id_persona);
            
            $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\FamiliarType', $familiar);
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){
                $familiarId = $form->get("familiarId")->getData();
            $familiar->setTitular($titular);
            $familiarTitular->setFamiliar($titular);
                if($familiarId){
                    $personaExistente = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($familiarId);
                    if($personaExistente){
                        $familiar->setFamiliar($personaExistente);
                        $
                    }
                }
            } 
        */
        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $familiar = new Familiar();
            $titular = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($id_persona);

            $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\FamiliarType', $familiar);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $personaId = $form->get("familiarId")->getData();
                if ($personaId) {
                    $personaExistente = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($personaId);
                    if ($personaExistente) {
                        $familiar->setFamiliar($personaExistente);
                    }
                }
                $titular->addFamiliare($familiar);
                $em->persist($titular);
                $familiar->setTitular($titular);
                $fecha = new DateTime();
                $familiar->setFechaCarga($fecha);
                $em->persist($familiar);
           
                $em->flush();

                if ($familiar->getId()) {
                    $response = array(
                        'success' => true,
                        'familiar' => array('id' => $familiar->getId(), 'text' => $familiar->getFamiliar()->__toString())
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => '1. Error al dar de alta el familiar'
                    );
                }
            }
        }
        return new JsonResponse($response);
    }
    
    public function editAjaxAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $familiarData = new Familiar();


            $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\FamiliarType', $familiarData);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $personaId = $form->get("id")->getData();
                $familiar = $em->getRepository('SiafcaIntranetBundle:Familiar')->findOneById($form->get("familiarId")->getData());
                if ($personaId) {
                    $personaExistente = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($personaId);
                    if ($personaExistente) {
                        $familiar->setFamiliar($personaExistente);
                    }
                }

                $familiar->setParentezco($familiarData->getParentezco());
                $familiar->setFechaValidacion($familiarData->getFechaValidacion());
                $familiar->setFechaVencimiento($familiarData->getFechaVencimiento());
                
                
                $em->persist($familiar);

                $em->flush();

                if ($familiar->getId()) {
                    $response = array(
                        'success' => true,
                        'familiar' => array('id' => $familiar->getId(), 'text' => $familiar->getFamiliar()->__toString())
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => '1. Error al editar el familiar'
                    );
                }
            }
            return new JsonResponse($response);
        }
    }

    public function deleteAjaxAction(Request $request, Familiar $id)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            
            $em->remove($id);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
            ));
        }
    }
    
            public function queryAjaxAction(Request $request, Familiar $id)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $familiar = $em->getRepository('SiafcaIntranetBundle:Familiar')->findOneById($id);
            $persona = $familiar->getFamiliar();
            $em->flush();

            return new JsonResponse(array(
                'familiar' => array(
                    'id' => $familiar->getId(),
                    'fechaValidacion' => ($familiar->getFechaValidacion() ? str_replace('-', '/', $familiar->getFechaValidacion()->format('d/m/Y')) : ""),
                    'fechaVencimiento' =>($familiar->getFechaVencimiento() ? str_replace('-', '/', $familiar->getFechaVencimiento()->format('d/m/Y')) : ""),
                    'Parentezco' => $familiar->getParentezco()->getId(),
                ),
                'persona' => array(
                    'id' => $persona->getId(),
                    'apellidoMat' => $persona->getApellidoMat(),
                    'apellidoPat' => $persona->getApellidoPat(),
                    'cuil' => $persona->getCuil(),
                    'documento' => $persona->getDocumento(),
                    'estadoCivil' => $persona->getEstadoCivil()->getNombre(),
                    'fechaNac' => $persona->getFechaNac()->format('d/m/Y'),
                    'nacionalidad' => $persona->getNacionalidad()->getDescripcion(),
                    'nombre' => $persona->getNombre(),
                    'sexo' => $persona->getSexo()->getNombre(),
                    'tipoDocumento' => $persona->getTipoDocumento()->getNombre(),
                ),
            ));
        }
    }
    
}
