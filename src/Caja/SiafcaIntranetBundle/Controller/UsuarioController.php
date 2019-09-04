<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Caja\SiafcaIntranetBundle\Entity\Estado;
use Caja\SiafcaIntranetBundle\Entity\Usuario;
use Caja\SiafcaIntranetBundle\Form\UsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo;
use \DateTime;
use Caja\SiafcaIntranetBundle\Entity\Oficina;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Usuario controller.
 *
 */
class UsuarioController extends Controller
{
    /**
     * Retorna 10 usuarios filtrados por nombre.
     * Para ser utilizado en autocompletado de campos y dar sugerencias a los usuarios.
     * @param Request $request
     * @return JsonResponse
     */
    public function AutocompleteAction(Request $request)
    {
        $input = $request->get('filter');

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                'SELECT DISTINCT u.username ' .
                'FROM SiafcaIntranetBundle:Usuario u ' .
                'WHERE UPPER(u.username) LIKE :filter ' .
                'ORDER BY u.username ASC'
            )
            ->setParameter('filter', '%'.strtoupper($input).'%')
            ->setMaxResults(10);
        $results = $query->getArrayResult();

        $matchResults = array();
        foreach ($results as $result) {
            $matchResults[] = $result['username'];
        }

        $response = new JsonResponse();
        $response->setData(array('matchResults' => $matchResults));
        return $response;
    }

    public function checkJavaScriptAction() {

        return $this->render('detectaJavaScript.html.twig');

    }

    /**
     * Permite consultar asincronicamente por la existencia de un nombre de usuario.
     * @param Request $request
     * @return JsonResponse
     */
    public function queryAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $username = $request->request->get('username');

            $em = $this->getDoctrine()->getManager();
            $existingUser = $em->getRepository('SiafcaIntranetBundle:Usuario')->findOneBy(array(
                'username' => $username,
            ));

            if ($existingUser) {
                $response = array(
                    'id' => $existingUser->getId(),
                    'username' => $existingUser->getUsername(),
                );
            } else {
                $response = array(
                    'id' => null,
                );
            }

            return new JsonResponse($response);
        }
    }

    /**
     * Lists all Usuario entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuarios = $em->getRepository('SiafcaIntranetBundle:Usuario')->findAll();

        return $this->render('usuario/index.html.twig', array(
            'usuarios' => $usuarios,
        ));
    }

    /**
     * Lists all organism users.
     */
    public function organismoAction(Request $request, Oficina $organismo)
    {
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraUsrOrg'));
        $dql= "SELECT "
                . "partial uo.{id, fechaDesde, fechaHasta}, "
                . "partial u.{id, username}, "
                . "partial e.{id, nombre}, "
                . "partial r.{id, nombre} "
            . "FROM "
                . "SiafcaIntranetBundle:UsuarioOrganismo uo "
                . "JOIN uo.estado e "
                . "JOIN uo.usuario u "
                . "JOIN uo.organismo o "
                . "JOIN uo.rol r "
            . "WHERE "
                . "o.id = ?1";

        // Si existe la variable actualOnly en el query string, se recuperaran solo los usuaros actuales
        if ($request->query->get('actualOnly')) {
            $today = new \DateTime('now');
            $dql .= " AND uo.fechaDesde <= '".$today->format("Y-m-d")." 23:59:59'";
            $dql .= " AND (uo.fechaHasta >= '".$today->format("Y-m-d")." 23:59:59' OR uo.fechaHasta IS NULL)";
        }

        $query = $em->createQuery($dql)->setParameters(array(1 => $organismo->getId()));

        $paginator  = $this->get('knp_paginator');
        $usuarios = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20,
            array('defaultSortFieldName' => 'uo.fechaDesde', 'defaultSortDirection' => 'desc')
        );

        return $this->render('usuario/index.html.twig', array(
                        'ayuda' => $ayuda,
            'usuarios' => $usuarios,
            'subtitle' => $organismo->getNombre(),
            'orgId' => $organismo->getId(),
            'actualOnly' => ((is_null($request->query->get('actualOnly')))? false : true),

        ));
    }

    /**
     * Creates a new Usuario entity.
     */
    public function newAction(Request $request, Oficina $organismo)
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(
            'Caja\SiafcaIntranetBundle\Form\UsuarioType',
            $usuario,
            array('action' => $this->generateUrl('usuario_new_manual', array('id' => $organismo->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rol = $form['rol']->getData();
            $estado = $em->getRepository('SiafcaIntranetBundle:Estado')->find(2);
            $usuarioOrganismo = new UsuarioOrganismo();
            $usuarioOrganismo->setOrganismo($organismo);
            $usuarioOrganismo->setFechaDesde(new DateTime('now'));
            $usuarioOrganismo->setRol($rol);
            $usuarioOrganismo->setEstado($estado);

            $usuario->addUsuarioOrganismo($usuarioOrganismo);
            $usuario->resetPassword();
            $usuario->setEstado($estado);
            $usuario->setSalt(bin2hex(random_bytes(50)));
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($usuario, $usuario->getPassword());
            $usuario->setPassword($encoded);
            dump($usuario);die();
            $em->persist($usuario);
            $em->flush();

            $next = $this->redirectToRoute('organismo_show', array('id' => $organismo->getId()));
        }

        if (!isset($next)) {
            $next = $this->render(':usuario:new.html.twig', array(
                'form' => $form->createView(),
                'oficina' => $organismo
            ));
        }

        return $next;
    }

    /**
     * Finds and displays a Usuario entity.
     *
     */
    public function showAction(Usuario $usuario)
    {
        $deleteForm = $this->createDeleteForm($usuario);

        return $this->render('usuario/show.html.twig', array(
            'usuario' => $usuario,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     */
    public function editAction(Request $request, Usuario $usuario)
    {
        $deleteForm = $this->createDeleteForm($usuario);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\UsuarioType', $usuario);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute('usuario_edit', array('id' => $usuario->getId()));
        }

        return $this->render('usuario/edit.html.twig', array(
            'usuario' => $usuario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Usuario entity.
     *
     */
    public function deleteAction(Request $request, Usuario $usuario)
    {
        $form = $this->createDeleteForm($usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($usuario);
            $em->flush();
        }

        return $this->redirectToRoute('usuario_index');
    }

    /**
     * Creates a form to delete a Usuario entity.
     *
     * @param Usuario $usuario The Usuario entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Usuario $usuario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', array('id' => $usuario->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function showOrganismosAction($id)
    {
        $em = $this->getDoctrine()->getManager();


        $user = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($id);
        $organismos = $user->getOrganismos('array');


//        $usurOrg = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->findByUsuarios($id);
//        $organismos = array();
//        $repo = $em->getRepository('SiafcaIntranetBundle:Organismo');
//         foreach ($usurOrg as $uo )
//         {
//            $org = $repo->find($uo->getOrganismo()->getId());
//            if ($org->isValid() && $uo->isValid())
//            {
//                $org->getEstado();
//                $organismos[] = $org;
//            }
//         }
//         $estado = $em->refresh($organismos[0]->getEstado());
//         $estado = $organismos[0]->getEstado();
//         $clase = get_class($estado);
//         $estado2 = new Estado($estado);
//         $estado2 = new Estado();
        dump($organismos);die();

        return $this->render('organismo/index.html.twig', array(
            'organismos' => $organismos,
        ));
    }

    /**
     * Edits password an existing Usuario entity.
     *
     */
    public function changePasswordAction(Request $request, $id)
    {

        var_dump( $request->request );

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $form = $this->createForm(new UsuarioPasswordType());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $pass_actual = $encoder->encodePassword( $data['password_act'], $entity->getSalt() ) ;

//            echo '<br>' . $pass_actual . '<br>';
//            echo $entity->getPassword(). '<br>';

            // ¿es password1 igual a password2?
            $bool = StringUtils::equals($entity->getPassword(), $pass_actual);

            //if ($entity->getPassword() == $pass_actual) {
            if ($bool) {
                //$entity->setSaltUsr(md5(uniqid(null, true)));
                $pass_nueva = $encoder->encodePassword( $data['password'], $entity->getSalt() ) ;
                $entity->setPassword($pass_nueva);

                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->set('aviso', 'La contraseña se ha cambiado...');

                return $this->redirect($this->generateUrl('Usuario_show', array('id' => $entity->getId())));

            }
            else {

                $this->get('session')->getFlashBag()->set('aviso', 'Contraseña incorrecta...');

                 return $this->render('SiafcaIntranetBundle:Usuario:usuarioPassword.html.twig', array(
                    'id' => $id,
                    'form' => $form->createView()
                    ));

            }
        }

        return $this->render('SiafcaIntranetBundle:Usuario:usuarioPassword.html.twig', array(
            'id' => $id,
            'form' => $form->createView()
        ));
    }

    /**
     * Clear password an existing Usuario entity.
     *
     */
    public function reset_passwordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->request->get('id');

        $entity = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UsuarioOrganismo entity.');
        }

        $idUser = $entity->getUsuario()->getId();

        $entity = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($idUser);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($entity);
        $entity->setPassword( $encoder->encodePassword( $entity->getUsername(), $entity->getSalt() ) );

        $em->persist($entity);
        $em->flush();

        //$this->get('session')->getFlashBag()->set('aviso', 'La contraseña se ha reestablecido...');

        //return $this->redirect($this->generateUrl('usuario_organismo.show', array('id' => $id)));

        $response = array(
            'success' => true,
            'msg' => 'Se reseteó la contraseña exitosamente. Para poder ingresar nuevamente el Usuario deberá cambiar su contraseña.'
        );

        return new JsonResponse($response);

    }
}
