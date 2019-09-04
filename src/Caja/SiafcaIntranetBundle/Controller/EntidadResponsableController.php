<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\EntidadResponsable;

/**
 * Organismo controller.
 */
class EntidadResponsableController extends Controller
{
    /**
     * Creacion y edicion asincronica de instancia en db
     * @param Request $request
     * @param Organismo $organismo
     * @return JsonResponse
     */
    public function newAjaxAction(Request $request, Organismo $organismo)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $entidadResponsable = new EntidadResponsable();
            $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\EntidadResponsableType', $entidadResponsable);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entidadRId = $form->get("id")->getData();
                if ($entidadRId) {
                    $entidadResponsableExistente = $em->getRepository('SiafcaIntranetBundle:EntidadResponsable')->findOneById($entidadRId);
                    $entidadResponsableExistente->setNombre($entidadResponsable->getNombre());
                    $entidadResponsableExistente->setCuit($entidadResponsable->getCuit());
                    $entidadResponsableExistente->setTipoJuridico($entidadResponsable->getTipoJuridico());
                    $em->persist($entidadResponsableExistente);
                    $organismo->setEntidadResponsable($entidadResponsableExistente);
                    $id = $entidadResponsableExistente->getId();
                } else {
                    $em->persist($entidadResponsable);
                    $organismo->setEntidadResponsable($entidadResponsable);
                    $id = $entidadResponsable->getId();
                }
                $em->persist($organismo);

                try {
                    $em->flush();
                    $response = array(
                        'success' => true,
                        'entidadResponsable' => array('id' => $id, 'text' => $entidadResponsable->__toString()),
                        'edit' => true
                    );
                } catch (Exception $ex) {
                    $response = array(
                        'success' => false,
                        'msg' => 'Error al editar el organismo'
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

    public function ajaxCuilAction(Request $request) {
        if (! $request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $cuil = $request->query->get('cuil');
        $repoEntidadResponsable = $this->getDoctrine()->getManager()->getRepository('SiafcaIntranetBundle:EntidadResponsable');
        $entidadResponsables = $repoEntidadResponsable->findBy(array(
            'cuit' => $cuil
        ));
        $result = array();
        foreach ($entidadResponsables as $entidadResponsable) {
            $singleEntidadResponsables = array();
            $singleEntidadResponsables['id'] = $entidadResponsable->getId();
            $singleEntidadResponsables['cuit'] = $entidadResponsable->getCuit();
            $singleEntidadResponsables['nombre'] = $entidadResponsable->getNombre();
            $singleEntidadResponsables['tipoJuridico'] = $entidadResponsable->getTipoJuridico();
            $result[] = $singleEntidadResponsables;
        }
        return new JsonResponse($result);
    }

    public function deleteAjaxAction(Request $request, EntidadResponsable $entidadResponsable, Organismo $organismo = null)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $em = $this->getDoctrine()->getManager();
                if (!is_null($organismo)) {
                    // Anulacion de campo entidad responsable de organismo
                    $organismo->setEntidadResponsable(null);
                    $em->persist($organismo);
                }
                // Si la entidad responsable no esta vinculada a ninguna otra entidad se elimina.
                $query = $em->createQuery('SELECT COUNT(o) '
                    . 'FROM SiafcaIntranetBundle:Organismo o '
                    . 'JOIN o.entidadResponsable er '
                    . 'WHERE er.id = '.$entidadResponsable->getId());
                if ($query->getSingleScalarResult() <= 1) { $em->remove($entidadResponsable); }
                $em->flush();
                $response = array( 'success' => true );
            } catch (Exception $ex) {
                $response = array( 'success' => false );
            }
            return new JsonResponse($response);
        }
    }

    /**
     * Permite consultar asincronicamente por una entidad
     * @param Request $request
     * @param EntidadResponsable $er
     * @return JsonResponse
     */
    public function queryAjaxAction(Request $request, EntidadResponsable $er)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'id' => $er->getId(),
                'cuit' => $er->getCuit(),
                'nombre' => $er->getNombre(),
                'tipoJuridico' => $er->getTipoJuridico(),
            ));
        }
    }
}
