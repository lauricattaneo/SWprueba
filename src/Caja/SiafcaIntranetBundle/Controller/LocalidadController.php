<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Localidad controller.
 *
 */

class LocalidadController extends Controller
{
    
    /*
     * Función que recibe el Request con el ID del Departamento
     * seleccionada en el formulario, y devuelve un array JSON
     * con las Localidades que pertenecen a ese Departamento
     */
    public function ajaxAction(Request $request) {
        if (! $request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }
        
       //ID del departamento seleccionada
        $departamentoID = $request->query->get('departamento');
        
        $repoLocalidad = $this->getDoctrine()->getManager()->getRepository('SiafcaIntranetBundle:Localidad');
        $localidades = $repoLocalidad->findByDepartamento($departamentoID);
        $result = array();
        foreach ($localidades as $localidad) {
            $result[$localidad->getId()] = $localidad->__toString();
        }
        return new JsonResponse($result);
    }
}
