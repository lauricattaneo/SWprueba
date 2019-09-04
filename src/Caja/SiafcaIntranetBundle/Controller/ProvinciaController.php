<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Provincia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provincia controller.
 *
 */

class ProvinciaController extends Controller
{
    public function ajaxAction(Request $request) {
        if (! $request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }
        
        $repo = $this->getDoctrine()->getManager()->getRepository('SiafcaIntranetBundle:Provincia');
        $provincias = $repo->findAll();
        $result = array();
        foreach ($provincias as $provincia) {
            $result[$provincia->getId()] = $provincia->__toString();
        }
        $response = new JsonResponse;
        $response->setData($result);
        return $response;
    }
}

