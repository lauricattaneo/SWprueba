<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Departamento controller.
 *
 */

class DepartamentoController extends Controller
{
    
    /*
     * Función que recibe el Request con el ID de la provincia
     * seleccionada en el formulario, y devuelve un array JSON
     * con los Departamentos que pertenecen a esa Provincia
     */
    public function ajaxAction(Request $request) {
        if (! $request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }
        
       //ID de la provincia seleccionada
        $provinciaID = $request->query->get('provincia');
        
        $repoDepartamento = $this->getDoctrine()->getManager()->getRepository('SiafcaIntranetBundle:Departamento');
        $departamentos = $repoDepartamento->findByProvincia($provinciaID);
        $result = array();
        foreach ($departamentos as $departamento) {
            $result[$departamento->getId()] = $departamento->__toString();
        }
        return new JsonResponse($result);
    }
}
