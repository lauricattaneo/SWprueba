<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\Cargo;
use Caja\SiafcaIntranetBundle\Form\CargoType;
use Symfony\Component\Form\Form;

/**
 * Otros controller.
 * Para uso de debug, etc.
 *
 */
class OtrosController extends Controller
{
    /**
     * Lists all Cargo entities.
     *
     */
    public function otrosAction($id)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        
//        $dql = 'SELECT 100, c.porcentaje
//                FROM SiafcaIntranetBundle:Aportante a
//                JOIN a.amparos e
//                JOIN e.itemConceptos c
//                WHERE a.id = 569885
//                AND c.concepto = 2';
//        
//        $result = $em->CreateQuery($dql)->getArrayResult();
        
        $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->showData($id);
        
        var_dump($liquidacion); die;
        
    }

}
