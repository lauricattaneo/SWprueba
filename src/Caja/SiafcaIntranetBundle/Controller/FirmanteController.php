<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Firmante;
use Caja\SiafcaIntranetBundle\Entity\Persona;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;
use DateInterval;
/**
 * Firmante controller.
 *
 */
class FirmanteController extends Controller
{
    /**
     * Creacion asincronica de firmante (y persona)
     * @param Request $request
     * @param Organismo $organismo
     * @return JsonResponse
     */
    public function newAjaxAction(Request $request, Organismo $organismo)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $firmante = new Firmante();

            $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\FirmanteType', $firmante);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try{
                    $personaId = $form->get("personaId")->getData();
                    if ($personaId) {
                        $personaExistente = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($personaId);
                        if ($personaExistente) {
                            $firmante->setPersona($personaExistente);
                        }
                    }
                    $fechaHasta = $firmante->getFechaDesde();

                    $firmanteActivo = $em->getRepository('SiafcaIntranetBundle:Firmante')->getActive($organismo->getId());
                    if (is_a($firmanteActivo, 'SiafcaIntranetBundle:Firmante')) {
                        $firmanteActivo = $firmanteActivo->setFechaHasta($fechaHasta);
                        $em->persist($firmanteActivo);
                    }
                    $organismo->setFirmante($firmante);
                    $em->persist($organismo);

                    $firmante->setOrganismo($organismo);

                    $dql = "SELECT e FROM "
                            . "SiafcaIntranetBundle:Estado e "
                            . "WHERE e.id = 35";
                    $query = $em->createQuery($dql);
                    $estado = $query->getSingleResult();
                    $firmante->setEstado($estado);


                    $em->persist($firmante);
                    $em->flush();

                    if ($firmante->getId()) {
                        $response = array(
                            'success' => true,
                            'firmante' => array('id' => $firmante->getId(), 'text' => $firmante->getPersona()->__toString() . ' / (' . $firmante->getFechaDesde()->format('d/m/Y') . ' - ' . $firmante->getFechaHasta() . ' )'),
                            'edit' => !((bool) $firmante->getFechaHasta())
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'msg' => 'Error al dar de alta el firmante'
                        );
                    }
                } catch (Exception $ex) {
                    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("ERROR");
                }
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

    /**
     * Actualizacion asincrona de firmante, no afecta persona o organismo
     * @param Request $request
     * @param Organismo $organismo
     * @return JsonResponse
     */
    public function editAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $firmanteData = new Firmante();
            $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\FirmanteType', $firmanteData);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $firmante = $em->getRepository('SiafcaIntranetBundle:Firmante')
                        ->find($form->get("firmanteId")->getData());

                if (!$firmante) {
                    $response = array(
                        'success' => false,
                        'msg' => 'No existe un firmante con Id '.$request->request->get('firmanteId'),
                    );
                }

                $firmante->setFechaDesde($firmanteData->getFechaDesde());
                $firmante->setFechaHasta($firmanteData->getFechaHasta());
                $firmante->setComentario($firmanteData->getComentario());
                $em->flush();

                $response = array(
                    'success' => true,
                    'edit' => !((bool) $firmante->getFechaHasta()),
                    'firmante' => array(
                        'persona' => $firmante->getPersona()->__toString(),
                        'fechaDesde' => $firmante->getFechaDesde()->format('d/m/Y'),
                        'fechaHasta' => $firmante->getFechaHasta()->format('d/m/Y'),
                    ),
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

    /**
     * Permite consultar asincronicamente por un firmante-persona de una organizacion.
     * @param Request $request
     * @param Firmante $firmante
     * @return JsonResponse
     */
    public function queryAjaxAction(Request $request, Firmante $firmante)
    {
        if ($request->isXmlHttpRequest()) {
            $persona = $firmante->getPersona();
            return new JsonResponse(array(
                'firmante' => array(
                    'id' => $firmante->getId(),
                    'fechaDesde' => $firmante->getFechaDesde()->format('d/m/Y'),
                    'fechaHasta' => $firmante->getFechaHasta()->format('d/m/Y'),
                    'comentario' => $firmante->getComentario(),
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

    public function deleteAjaxAction(Request $request, Firmante $firmante)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($firmante);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
            ));
        }
    }

    
    public function newAction(Request $request, Organismo $organismo)
    {
        $em = $this->getDoctrine()->getManager();
        $firmante = new Firmante();
        $personaId = $request->query->get('persona');
        if (!$personaId)
        {
            $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\FirmanteType', $firmante,array(
                'action' => $this->generateUrl('firmante_new', array(
                    'id' => $organismo->getId()
                ))
            ));
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $organismo->setFirmante($firmante);
                $em->persist($firmante);
                $em->flush();
                
                return $this->redirectToRoute('organismo_show', array('id' => $organismo->getId()));
            }
            
            return $this->render('firmante/newmodal.html.twig', array(
                'form' => $form->createView(),
                'titulo' => 'Agregar Firmante:',
                'modalId' => 'modalFirmante'
            ));

        }
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneById($personaId);
        $desde = new DateTime('now');
        $firmante->setFechaDesde($desde);
        $firmante->setPersona($persona);
        $organismo->setFirmante($firmante);
        $em->persist($organismo);
        $em->flush();
        return $this->redirectToRoute('organismo_show', array('id' => $organismo->getId()));
    }
    
    
    public function showAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $firmante = $em->getRepository('SiafcaIntranetBundle:Firmante')->findOneById($id);
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraFirPenShow'));
        
        return $this->render('firmante/show.html.twig', array(
                'firmante' => $firmante,
                'ayuda' => $ayuda,
       ));
    }
    
    public function ajaxActivarAction(Request $request, $id) {
       
        $em = $this->getDoctrine()->getManager();
        $firmante = $em->getRepository('SiafcaIntranetBundle:firmante')->find($id);
        $organismo = $firmante->getOrganismo();
        $query = $em->createQuery(
                'SELECT count(f) FROM SiafcaIntranetBundle:Firmante f WHERE f.organismo = ' . $organismo->getId() . ' AND f.estado = 35 '
        );
        $cant_activos = $query->getSingleScalarResult();
        if ($firmante->getFechaHasta() != null){
            $firmante->setFechaHasta(null);
        }
        //$estado = $em->getRepository('SiafcaIntranetBundle:estado')->find(35);
        $activo = $firmante->activar();

        $correo = $organismo->getCorreos();
        $orgName = $organismo->getNombre();

        $persona = $em->getRepository('SiafcaIntranetBundle:persona')
                ->findOneByid($firmante->getPersona()->getId());

        if ($cant_activos == 0) {
            if ($activo && $correo) {

                $em->persist($firmante);
                $em->flush();

                $asunto = 'Activación de Firmante';

                $descripcion = 'Estimado,' . '<br />'
                        . 'Se informa que el siguiente firmante ha sido activado:'
                        . '<br /><br />Apellido y Nombre: ' . $persona->getApellidoPat() . ', ' . $persona->getNombre()
                        . '<br />Cuil: ' . $persona->getCuilToShow()
                        . '<br /><br /> Sin otro particular, saluda atte' . '<br />';

                // Aca envio los datos para enviar el mail de solicitud de alta firmante
                $envioFirmante = $this->enviarMail($orgName, $asunto, $descripcion, $correo);
                if ($envioFirmante) {
                   $this->addFlash(
                        'info', 
                        'Firmante activado correctamente.');
   
                   return $this->redirectToRoute('organismo_show', array('id' => $organismo->getId()));
   
                }else{
                    $this->addFlash(
                        'error', 
                        'Firmante no se pudo activar.');
                }
            }else {
                $this->addFlash(
                    'error', 
                    'Error, verifique si ya existe firmante activo o si tiene email asociado al organismo');
            }
        }
         
        return $this->redirectToRoute('organismo_show', array('id' => $organismo->getId()));
    }
    
    public function rechazarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $idFirmante = $request->request->get('id');
        $texto = $request->request->get('texto');

        $firmante = $em->getRepository('SiafcaIntranetBundle:firmante')->find($idFirmante);

        $organismo = $em->getRepository('SiafcaIntranetBundle:organismo')
                ->findOneByid($firmante->getOrganismo()->getId());

        $rechazado = $firmante->rechazar();
        
        $correo = $organismo->getCorreos();
        $orgName = $organismo->getNombre();

        $persona = $em->getRepository('SiafcaIntranetBundle:persona')
                ->findOneByid($firmante->getPersona()->getId());


        if ($rechazado && $correo) {
//            { throw new \Exception('No hay emails asociados a esta organización'); }
            $firmante->setFechaHasta(new DateTime('now'));
            
            $em->persist($firmante);
            $em->flush();
            $asunto = 'Rechazo de Firmante';

            $descripcion = 'Estimado,' . '<br />'
                    . 'Se informa que el siguiente firmante ha sido rechazado:'
                    . '<br /><br />Apellido y Nombre: ' . $persona->getApellidoPat() . ', ' . $persona->getNombre()
                    . '<br />Cuil: ' . $persona->getCuilToShow()
                    . '<br />Motivo: ' . $texto
                    . '<br /><br /> Sin otro particular, saluda atte' . '<br />';

            // Aca envio los datos para enviar el mail de solicitud de alta firmante
            $envioFirmante = $this->enviarMail($orgName, $asunto, $descripcion, $correo);
            if ($envioFirmante) {
                return new JsonResponse(array(
                    'success' => true,
                    'mensaje' => 'Se envio correctamente email a organismo informando el rechazo de firmante',
                    
                ));
            } else {
                return new JsonResponse(array(
                    'success' => false,
                    'mensaje' => 'Ha ocurrido un problema. Verifique los datos o intente nuevamente más tarde',
                ));
            }
        } else {
            return new JsonResponse(array(
                'success' => false,
                'mensaje' => 'No hay emails asociados a esta organización',
            ));
        }
    }

    /**
     * Función que envia un mail a una organización. Los datos del usuario emisor se utilizan para
     * proporcionar información de contacto en la firma del mensaje. (nombre, apellido, telefono)
     * @param String $orgName Nombre de la organizacion destinataria del mensaje
   
     * @param String $asunto Asunto del mensaje
     * @param String $descripcion

     * @param String $correo 
     * @return boolean Retorna true si el mail fue enviado sin errores, de lo contrario false.
     */
    public function enviarMail($orgName, $asunto, $descripcion, $correo)
    { 
        $to = 'lcattaneo@santafe.gov.ar'; //o ver algun nombre
        //$contactMailArray = array_map('trim', array_filter(explode(';', trim($defaultEmails))));
                
        $body = $this->paragraphsWrap($descripcion);

        // If there is contact info, add it to bottom of body
        if ($orgName) {
            $signature = '<div>---------------------------<br />Caja de Jubilaciones y Pensiones de la Provincia de Santa Fe'
                        .'<br /';
            
            $signature .= '</div>';
            $body .= $signature;
        }

        try 
        {
            $mail = \Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom($to)
                //->setTo('lcattaneo@santafe.gov.ar')
                ->setTo($correo)
                ->setBody($body, 'text/html');

            $success = $this->get('mailer')->send($mail);
        } catch (\Exception $ex) {
            //throw new \Exception('Error al enviar mail');
            $success = false;
        }
        return $success;
    }
      
   /**
     * Transforma el contenido de textarea para mostrarlo correctamente en emails
     * @param String $body Cuerpo de email
     * @return string Cuerpo de email separado en parrafos
     */
    function paragraphsWrap($body)
    {
        $array = explode("\n", $body);
        $paragraphs = "";
        foreach($array as $paragraph) {
            $paragraphs .= '<p>'.$paragraph.'</p>';
        }
        return $paragraphs;
    }
    
    public function editAction(Request $request, Firmante $firmante)
    {
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\FirmanteType', $firmante,array(
                'action' => $this->generateUrl('firmante_edit', array(
                    'id' => $firmante->getId()
                ))
            ));
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($firmante);
            $em->flush();

            return $this->redirectToRoute('organismo_show', array('id' => $firmante->getOrganismo()->getId()));
        }

        return $this->render('firmante/newmodal.html.twig', array(
            'form' => $editForm->createView(),
            'titulo' => 'Editar Datos del Firmante:',
            'modalId' => 'modalFirmante'
        ));
    }
    
    public function bloquearAction(Request $request, Firmante $firmante)
    {
        
        $em = $this->getDoctrine()->getManager();
        $firmante->inhabilitar();
        $em->flush();

        return $this->redirectToRoute('organismo_show', array('id' => $firmante->getOrganismo()->getId()));
    }
    
    private function getFormErrors($form)
    {
        $errors = array();
        array_push($errors, $form['fechaDesde']->getErrors());
        $personaErrors = iterator_to_array($form['persona']->getErrors(true,false), false);
        foreach ($personaErrors as $personaError){
            array_push($errors, $personaError);
        }
        
        return $errors;
    }
    
    public function renderAction(Firmante $firmante)
    {
        return $this->render('firmante/render.html.twig', array(
            'firmante' => $firmante
        ));
    }
    
    public function indexFirmantesPendientesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraFirmPendi'));
        
        //obtengo el index 
        $resultado = $em->getRepository('SiafcaIntranetBundle:Firmante')
                ->IndexFirmantesPendientes();
   
        //pagino los resultados
        $paginator = $this->get('knp_paginator');
        $organismos = $paginator->paginate(
            $resultado,
            $request->query->getInt('page', 1),
                20
        );
        
        return $this->render('firmante/index.html.twig', array(
            'ayuda'      =>  $ayuda,
            'organismos' =>  $organismos
        ));
    }
}
