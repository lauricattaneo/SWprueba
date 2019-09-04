<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\Oficina;
use Caja\SiafcaIntranetBundle\Entity\Usuario;
use Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 
 
 * IntraUsrOrgShow - showAction

 */

class UsuarioOrganismoController extends Controller
{
    /** @var integer Toma el valor del id del rol utilizado por los usuarios responsables de los organismos */
    const ROLE_ORGADMIN = 3;

    /** @var integer Toma el valor del id del rol utilizado por los usuarios de organismos */
    const ROLE_ORGUSER = 5;

    /** @var integer Toma el valor del id del estado inicial de los usuarios de organismo/oficina */
    const EST_USR_INIC = 30;
    
        /** @var integer Toma el valor del id del estado habilitado de los usuarios de organismo/oficina */
    const EST_USR_HABIL = 31;

        /** @var integer Toma el valor del id del estado bloqueado de los usuarios de organismo/oficina */
    const EST_USR_BLOQ = 32;

        /** @var integer Toma el valor del id del estado inhabilitado de los usuarios de organismo/oficina*/
    const EST_USR_INHAB = 33;

    public function validarAction(Request $request){
        if($request->isXmlHttpRequest()){
            $uorganismoId = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $uorganismo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($uorganismoId);
            $uorganismo->validar();
            $em->persist($uorganismo);
            $em->flush();
            
            $response = array(
                'success' => true,
                'msg' => 'Se valido el usuario ' . $uorganismoId,
                'usuario' => array( 
                    'id' => $uorganismo->getId(),
                    'usuario' => $uorganismo->__toString(),
                ),
                );
            
        }
                return new JsonResponse($response);
    }

    public function bloquearAction(Request $request) {
        if($request->isXmlHttpRequest()){
            $uorganismoId = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $uorganismo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($uorganismoId);
            $uorganismo->bloquear();
            $em->persist($uorganismo);
            $em->flush();
            
            $response = array(
                'success' => true,
                'msg' => 'Se bloqueo el usuario ' . $uorganismoId,
                'usuario' => array( 
                    'id' => $uorganismo->getId(),
                    'usuario' => $uorganismo->__toString(),
                ),
                );
            
        }
                return new JsonResponse($response);
    }

    public function desbloquearAction(Request $request) {
        if($request->isXmlHttpRequest()){
            $uorganismoId = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $uorganismo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($uorganismoId);
            $uorganismo->desbloquear();
            $em->persist($uorganismo);
            $em->flush();
            
            $response = array(
                'success' => true,
                'msg' => 'Se desbloqueo el usuario ' . $uorganismoId,
                'usuario' => array( 
                    'id' => $uorganismo->getId(),
                    'usuario' => $uorganismo->__toString(),
                ),
                );
            
        }
                return new JsonResponse($response);
    }

    public function inhabilitarHAction(Request $request) {
        if($request->isXmlHttpRequest()){
            $uorganismoId = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $uorganismo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($uorganismoId);
            $uorganismo->inhabilitarHab();
            $em->persist($uorganismo);
            $em->flush();
            
            $response = array(
                'success' => true,
                'msg' => 'Se inhabilito el usuario ' . $uorganismoId,
                'usuario' => array( 
                    'id' => $uorganismo->getId(),
                    'usuario' => $uorganismo->__toString(),
                ),
                );
            
        }
                return new JsonResponse($response);
    }

    public function inhabilitarBAction(Request $request) {
        if($request->isXmlHttpRequest()){
            $uorganismoId = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $uorganismo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($uorganismoId);
            $uorganismo->inhabilitarBloq();
            $em->persist($uorganismo);
            $em->flush();
            
            $response = array(
                'success' => true,
                'msg' => 'Se inhabilito el usuario' . $uorganismoId,
                'usuario' => array( 
                    'id' => $uorganismo->getId(),
                    'usuario' => $uorganismo->__toString(),
                ),
                );
            
        }
        return new JsonResponse($response);
    }

    /**
     * Creates a new UsuarioOrganismo entity, and the user if it does not exist.
     */
    public function newAction(Request $request, Oficina $organismo)
    {
    
        $rolAct = '';
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            $rolAct = 'ROLE_ADMIN';
        else if ($this->get('security.authorization_checker')->isGranted ('ROLE_CONTRALOR_ADMIN'))
            $rolAct = 'ROLE_CONTRALOR_ADMIN';
        else $rolAct = 'NONE';

        $usuario = $this->getUser();
    
        $oUsuarioOrganismo = new UsuarioOrganismo();
        $form = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
            $oUsuarioOrganismo,
            array(
                'action' => $this->generateUrl('usuario_organismo.new_usuario', array('id' => $organismo->getId(), 'type' => ($organismo instanceof Organismo)? 'organismo' : 'oficina')),
                'type' => $organismo->getDiscr(), // Se usa la definición del tipo de oficina para condicionar los roles que se dan a elegir para el nuevo usuario
                'discr' => $organismo->getDiscr(),
                'type' => $this->getUser()->orgAdm()['adm'],
                'roles'=> $this->getUser()->orgAdm()['roles'],
                //'attr' => $usuario->orgAdm(),
            )
        );
      
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            // Se completa la instancia con datos que no vienen del formulario
            $username = $request->request->get('usuario_organismo')['username'];
                       
            $estado = $em->getRepository('SiafcaIntranetBundle:Estado')->find(self::EST_USR_INIC); // TODO: Estado = Organismo creado?
            $usuario = $em->getRepository('SiafcaIntranetBundle:Usuario')->findOneBy(array('username' => $username));
            if (!$usuario) {
                // Si el usuario es nuevo, establecer contraseña, salt y estado (2)
                $usuario = new Usuario();
                $usuario->setUsername($request->request->get('usuario_organismo')['username']);
                $usuario->resetPassword();
                $usuario->setEstado($estado);
                $usuario->setSalt(bin2hex(random_bytes(50)));
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($usuario, $usuario->getPassword());
                $usuario->setPassword($encoded);
            }
            $oUsuarioOrganismo->setOrganismo($organismo);
            $oUsuarioOrganismo->setEstado($estado);
            $oUsuarioOrganismo->setUsuario($usuario);
            
            // Detecta si se requieren acciones previas a realizar un nuevo cambio de rol
            $conflicts = $this->checkRoleChange($em, $oUsuarioOrganismo);

            if ($conflicts['overlapingAdmin'] || $conflicts['unclosed'] || $conflicts['overlaping'] || $conflicts['noChange'] || $conflicts['admin'] || $conflicts['wrongDates']) {
                if ($conflicts['admin']) { $this->addFlash('error', 'El usuario seleccionado es administrador del organismo, asigne un nuevo administrador antes de cabiar su rol. No se realizaron cambios.'); } // Tiene prioridad
                else if ($conflicts['noChange']) { $this->addFlash('error', 'El usuario ya tiene el rol asignado, no se realizaron cambios'); } // Tiene prioridad
                else if ($conflicts['unclosed']) { $this->addFlash('error', 'Es necesario cerrar el periodo anterior del usuario en el organismo'); } // Tiene prioridad
                else if ($conflicts['overlapingAdmin']) { $this->addFlash('error', 'Los periodos de los administradores no pueden solaparse'); }
                else if ($conflicts['wrongDates']) { $this->addFlash('error', 'La fecha de inicio debe ser menor que la fecha de finalización del periodo'); }
                else if ($conflicts['overlaping']) { $this->addFlash('error', 'El nuevo periodo no puede solaparse con anteriores del mismo usuario'); }
                $next = $this->redirectToRoute('usuario_organismo', array('id' => $organismo->getId()));
            } else {
                if ($oUsuarioOrganismo->getRol()->getId() === self::ROLE_ORGADMIN) { 
                    $this->closeLastAdmin($organismo->getId(), $em);

                }

                $usuario->addUsuarioOrganismo($oUsuarioOrganismo);
                $em->persist($usuario);
                $em->persist($oUsuarioOrganismo);
                $em->flush();
                
                //envio de correos al organismo para notificar el usuario y contraseña
                $this->enviarMail($organismo,$usuario->getUsername());
                
                if($oUsuarioOrganismo->getId()){
                    $response = array(
                        'success' => true,
                        'usuario' => array(
                        'id' => $oUsuarioOrganismo->getId(),
                        'username' => $oUsuarioOrganismo->__toString(),
                        'fechaDesde' => $oUsuarioOrganismo->getFechaDesde(),
                        'fechaHasta' => $oUsuarioOrganismo->getFechaHasta(),
                        'rol' => $oUsuarioOrganismo->getRol()->getNombre(),
                        'estado' => $oUsuarioOrganismo->getEstado()->getNombre(),
                        )
                    );


                }else{
                    $response = array(
                        'success' => false,
                        'msg' => "Error al agregar usuario al organismo",
                    );
                }

                return new JsonResponse($response);


                //$this->addFlash('success', 'Es usuario fue añadido al organismo');
                //$next = $this->redirectToRoute('usuario_organismo', array('id' => $organismo->getId()));
            }
        } elseif($form->isSubmitted() && !$form->isValid()) {
            $Utils = new Util();
            $errors = $Utils->getErrorMessages($form);
            $response = array(
                'success' => false,
                'errors' => $errors,
                'msg' => 'Verifique los campos e intente nuevamente'
            );
            die(json_encode($response));
        }
    }
    
    /**
     * Función que envia un mail a una organización. Los datos del usuario emisor se utilizan para
     * proporcionar información de contacto en la firma del mensaje. (nombre, apellido, telefono)
     * @param String $customEmails Emails de los destinatarios. Si son varios estan separados por ';' (Insertados por el usuario en un formulario)
     * @param String $subject Asunto del mensaje
     * @param String $message Mensaje a entregar
     * @param String $contactFirstName Nombre (Opcional) del emisor del mensaje (Se agrega a firma)
     * @param String $contactLastName Apellido (Opcional) del emisor del mensaje (Se agrega a firma)
     * @param String $contactPhone Telefono (Opcional) de contacto al emisor del mensaje (Se agrega a firma)
     * @param String $contactMail Mail (Opcional) de contacto al emisor del mensaje (Se agrega a firma)
     * @return boolean Retorna true si el mail fue enviado sin errores, de lo contrario false.
     */
    public function enviarMail($organismo,$nombreUsuario)
    {
        // DESCOMENTAR la siguiente linea !!!!!
//        $to = $organismo-getCorreos();
        $to = 'lcattaneo@santafe.gob.ar';
        
        $subject = 'Notificación de usuario creado';
        
        /*
         * NOTA!!!!!!
         * Revisar los datos de contacto de contralor
         */
        
        $body = 'Estimado <br />'
                . 'Se informa la creacion de nuevo usuario para el organismo cuyo nombre es: <b>' . $organismo->getNombre() . '</b><br />'
                . 'Para ingresar al sistema, debe dirigirse al siguiente <i><a href="https://www.santafe.gob.ar">enlace</a></i><br />' 
                . 'Usando el siguiente usuario: <b>'. $nombreUsuario . '</b> y la ' . ' contraseña de su Id Ciudadana correspondiente'
                . '<br /><br />'
                . 'Sin otro particular, lo saludo atte<br />'
                . '---------------------------<br />'
                . 'Caja de Jubilaciones y Pensiones de la Provincia'
                . '<br /><br />'
                . 'Para consultas comunicarse a ejemplo@contralor.com o (0342) - 4xxyyzz'
                ;

        $from = 'lcattaneo@santafe.gov.ar';

        try {
            $mail = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($to)
                ->setReadReceiptTo($from)
                ->setBody($body, 'text/html');

            $success = $this->get('mailer')->send( $mail );
        } catch (\Exception $e) {
//            throw new \Exception('Error al enviar mail');
            $success = false;
        }

        return $success;
    }

    /**
     * Edits an existing relation between usuario y organismo
     */
    public function editAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

                    $rolAct = '';
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            $rolAct = 'ROLE_ADMIN';
        else if ($this->get('security.authorization_checker')->isGranted ('ROLE_CONTRALOR_ADMIN'))
            $rolAct = 'ROLE_CONTRALOR_ADMIN';
        else $rolAct = 'NONE';
            $em = $this->getDoctrine()->getManager();
            $uOrganismoData = new UsuarioOrganismo();
            
            if($request->query->get('tipo') == "oficina"){
            $form = $this->createForm(
                    'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
                    $uOrganismoData,
                    array(
                'attr' => array('role' => $rolAct),
                    )
                    );
            }else{
                           $form = $this->createForm(
                    'Caja\SiafcaIntranetBundle\Form\UsuarioOrganismoType',
                    $uOrganismoData,
                    array(
                        'attr' => array('role' => $rolAct),
                    )
                    );
            }
            $form->handleRequest($request);
            $idUsOrg = $form->get('uid')->getData();

            if ($form->isSubmitted() && $form->isValid()) {
                $oUsuarioOrganismo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($idUsOrg);

                if (!$oUsuarioOrganismo) {
                    $response = array(
                        'success' => false,
                        'msg' => "No existe un usuario con id " . $idUsOrg,
                    );
                }

                $oUsuarioOrganismo->setRol($uOrganismoData->getRol());
                $oUsuarioOrganismo->setFechaDesde($uOrganismoData->getFechaDesde());
                $oUsuarioOrganismo->setFechaHasta($uOrganismoData->getFechaHasta());
                $oUsuarioOrganismo->setCorreo($uOrganismoData->getCorreo());

                // Detecta si se requieren acciones previas a realizar un nuevo cambio de rol
                $conflicts = $this->checkRoleChange($em, $oUsuarioOrganismo);

                
                if ($conflicts['overlapingAdmin'] || $conflicts['overlaping'] || $conflicts['wrongDates']) {
                    if ($conflicts['wrongDates']) {
                        $this->addFlash('error', 'La fecha de inicio debe ser menor que la fecha de finalización del periodo');
                    } else if ($conflicts['overlaping']) {
                        $this->addFlash('error', 'El nuevo periodo no puede solaparse con otros del mismo usuario');
                    } else if ($conflicts['overlapingAdmin']) {
                        $this->addFlash('error', 'Los periodos de los administradores no pueden solaparse');
                    }
                } else {
                    if ($oUsuarioOrganismo->getRol()->getId() === self::ROLE_ORGADMIN) {
                        $this->closeLastAdmin($oUsuarioOrganismo->getOrganismo()->getId(), $em);
                    }
                    $estado = $em->getRepository('SiafcaIntranetBundle:Estado')->find(2); // TODO: Estado = Organismo creado?
                    // Solo se podrá actualizar desde el formulario los campos tipo fecha y el correo,
                    // Para cualquier otro cambio se debe cerrar el periodo actual y crear un UsuarioOrganismo nuevo
                    $em->persist($oUsuarioOrganismo);
                    $em->flush();

                    $response = array(
                        'success' => true,
                        'msg' => "Usuario editado con exito",
                        'usuario' => array(
                            'id' => $oUsuarioOrganismo->getId(),
                            'usuario' => $oUsuarioOrganismo->__toString()
                        )
                    );
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
     * Retorna los conflictos que hay que solucionar antes de cambiar el rol del usuario o administrador en un organismo.
     * Contempla:
     *  - Superposición de periodos nuevos con anteriores
     *  - Periodos anteriores del usuario sin fecha de finalización
     *  - Cambio de rol a un administrador a otro rol con menor jerarquia (dejando el organismo sin administrador)
     *  - Rol de origen y destino iguales.
     *  - Superposicion de periodos entre dos roles administrador
     * @param type $em
     * @param UsuarioOrganismo $oUsuarioOrganismo
     * @return Array Con banderas que indican si se encontro o no cada problema contemplado
     */
    private function checkRoleChange($em, $oUsuarioOrganismo) {
        // Check para ver si el usuario ya tiene actualmente el rol
        $sameRole = $em->createQuery(
            'SELECT '.
                'COUNT(uo) as existente '.
            'FROM '.
                'SiafcaIntranetBundle:UsuarioOrganismo uo '.
                'JOIN uo.usuario u '.
                'JOIN uo.organismo o '.
                'JOIN uo.rol r '.
            'WHERE '.
                'o.id = :orgId '.
                'AND u.id = :usuId '.
                'AND uo.fechaHasta IS NULL '.
                'AND r.id = :rolId')
            ->setParameters(array(
                'orgId' => $oUsuarioOrganismo->getOrganismo()->getId(),
                'rolId' => $oUsuarioOrganismo->getRol()->getId(),
                'usuId' => $oUsuarioOrganismo->getUsuario()->getId()))
            ->getResult();

        // Check de control de superposicion entre periodos con roles responsables (overlapAdmin)
        // Check para ver si el periodo definido para la nueva instancia de usuario_organismo se superpone con otro (overlapPeriod)
        // Check para ver si el usuario tiene un periodo abierto que debe cerrarse antes de agregarle uno nuevo. (openPeriod)
        $query = $em->createQuery(
            'SELECT '.
                'SUM (CASE WHEN uo.fechaHasta IS NULL AND u.id = :usuId THEN 1 ELSE 0 END) as openPeriod, '.
                'SUM (CASE WHEN uo.fechaHasta IS NOT NULL AND u.id = :usuId THEN 1 ELSE 0 END) as overlapPeriod '.
                (($oUsuarioOrganismo->getRol()->getId() == self::ROLE_ORGADMIN)? ', SUM (CASE WHEN r.id = '.self::ROLE_ORGADMIN.' AND uo.estado <> 33'.
                ' AND ((uo.fechaDesde < :fd AND uo.fechaHasta > :fd)'.
                  ' OR (uo.fechaDesde < :fh AND uo.fechaHasta > :fh) ) THEN 1 ELSE 0 END) as overlapAdmin ' : '').
                //(($oUsuarioOrganismo->getRol()->getId() == self::ROLE_ORGADMIN)? ', SUM (CASE WHEN r.id = '.self::ROLE_ORGADMIN.' AND ((uo.fechaDesde > :fd AND uo.fechaDesde < :fh OR uo.fechaHasta > :fd AND uo.fechaHasta < :fh) OR '.
                //    '(uo.fechaDesde < :fd AND uo.fechaDesde < :fh AND uo.fechaHasta > :fd AND uo.fechaHasta > :fh)) THEN 1 ELSE 0 END) as overlapAdmin ' : '').
            'FROM '.
                'SiafcaIntranetBundle:UsuarioOrganismo uo '.
                'JOIN uo.usuario u '.
                'JOIN uo.rol r '.
                'JOIN uo.organismo o '.
            'WHERE '.
                'o.id = :orgId '.
                'AND (u.id = :usuId'.(($oUsuarioOrganismo->getRol()->getId() == self::ROLE_ORGADMIN)? ' OR r.id = '.self::ROLE_ORGADMIN : '').') '.
                'AND ((uo.fechaHasta IS NULL) OR '. // Fecha fin es nulo
                    '(uo.fechaDesde > :fd AND uo.fechaDesde < :fh OR uo.fechaHasta > :fd AND uo.fechaHasta < :fh) OR '. // Periodo superpuesto
                    '(uo.fechaDesde < :fd AND uo.fechaDesde < :fh AND uo.fechaHasta > :fd AND uo.fechaHasta > :fh)) '.
                (is_null($oUsuarioOrganismo->getId())? '' : 'AND uo.id != :usuarioOrgId')) // Para que al editar no se compare con la misma instancia
            ->setParameters(array(
                'orgId' => $oUsuarioOrganismo->getOrganismo()->getId(),
                'usuId' => $oUsuarioOrganismo->getUsuario()->getId(),
                'fd' => $oUsuarioOrganismo->getFechaDesde(), //->format("j-M-y")." 12.00.00.000000000 AM",
                'fh' => (!is_null($oUsuarioOrganismo->getFechaHasta()))? $oUsuarioOrganismo->getFechaHasta() /*->format("j-M-y")." 11.59.59.000000000 PM" */ : (new \DateTime('now')) /*->format("j-M-y")." 11.59.59.000000000 PM" */));

        if (!is_null($oUsuarioOrganismo->getId())) { $query->setParameter('usuarioOrgId', $oUsuarioOrganismo->getId()); }
        
        $periodConflict = $query->getResult();
        
        return array(
            'overlapingAdmin' => isset($periodConflict[0]['overlapAdmin']) && $periodConflict[0]['overlapAdmin'] > 0,
            'overlaping' => isset($periodConflict[0]['overlapPeriod']) && $periodConflict[0]['overlapPeriod'] > 0,
            'unclosed' => isset($periodConflict[0]['openPeriod']) && $periodConflict[0]['openPeriod'] > 0,
            'noChange' => isset($sameRole[0]['existente']) && (bool) $sameRole[0]['existente'] > 0,
            'admin' => (!is_null($oUsuarioOrganismo->getOrganismo()->getUsuarioResponsable()))? $oUsuarioOrganismo->getOrganismo()->getUsuarioResponsable()->getId() == $oUsuarioOrganismo->getUsuario()->getId() : false,
            'wrongDates' => (is_null($oUsuarioOrganismo->getFechaHasta()))? false : $oUsuarioOrganismo->getFechaHasta() <= $oUsuarioOrganismo->getFechaDesde());
    }


    public function queryAjaxAction(Request $request)
    {
        
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('uid');
            $em = $this->getDoctrine()->getManager();

            $existingUserResp = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($id);

           if ($existingUserResp) {
                $response = array(
                    'success' => true,
                    'username' => $existingUserResp->getUsuario()->getUsername(),
                    'rol' => $existingUserResp->getRol()->getId(),
                    'fechaDesde' => $existingUserResp->getFechaDesde()->format('d/m/Y'),
                    //'fechaHasta' => $existingUserResp->getFechaHasta()->format('d/m/Y'),
                    'correo' => $existingUserResp->getCorreo(),
                    'uid' => $existingUserResp->getId(),
                );
            } else {
                $response = array(
                    'success' => false,
                    'id' => null,
                );
            }
            return new JsonResponse($response);
        }else return new JsonResponse(array('success'=> "false"));
    }


    /**
     * Cierra el periodo del administrador (responsable de organismo) anterior
     * @param int $uid
     * @param int $orgid
     * @param int $em
     * @return String Cadena que indica si hay un problema al cambiar el administrador
     */
    private function closeLastAdmin($orgid, $em) {
        $prevAdmin = $em->createQuery(
            'SELECT '.
                'uo '.
            'FROM '.
                'SiafcaIntranetBundle:UsuarioOrganismo uo '.
                'JOIN uo.usuario u '.
                'JOIN uo.organismo o '.
                'JOIN uo.rol r '.
            'WHERE '.
                'o.id = :orgId '.
                'AND uo.fechaHasta IS NULL '.
                'AND r.id = :admin')
            ->setParameters(array(
                'orgId' => $orgid,
                'admin' => self::ROLE_ORGADMIN))
            ->getOneOrNullResult();

        if ($prevAdmin) {
            // Cierre del periodo del administrador (responsable) anterior:
            $prevAdmin->setFechaHasta((new \DateTime('now')));
            $em->persist($prevAdmin);
            $em->flush();
        }
    }

    /**
     * Finds and displays a UsuarioOrganismo entity.
     */
    public function showAction(UsuarioOrganismo $uOrganismo)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraUsrOrgShow'));
        
        return $this->render('usuarioOrganismo/show.html.twig', array(
            'usuarioOrganismo' => $uOrganismo,
            'ayuda' => $ayuda,
        ));
    }
}
