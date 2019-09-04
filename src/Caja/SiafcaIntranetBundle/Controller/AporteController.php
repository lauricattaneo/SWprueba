<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Caja\SiafcaIntranetBundle\Entity\Persona;
use Caja\SiafcaIntranetBundle\Form\PersonaType;
use Symfony\Component\Form\Form;
use Caja\SiafcaIntranetBundle\Entity\Domicilio;
use Caja\SiafcaIntranetBundle\Entity\Familiar;

/**
 * Description of AporteController
 *
 * @author administrador
 */
class AporteController extends Controller {

    public function showAction(Request $request, $persona) {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()
                ->select('count(i.id)')
                ->from('SiafcaIntranetBundle:Item', 'i')
                ->from('SiafcaIntranetBundle:Aportante', 'a')
                ->where('i.aportante = a')
                ->andWhere('a.persona = :persona')
                ->setParameter('persona', $persona);
        $count = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->createQueryBuilder()
                ->select(array('L.mes MES', 'L.anio ANIO', 'O.nombre ORG', 'TL.nombre NTIPO', 'R.nombre REV',
                    'C1.importe IMP1', 'C2.importe IMP2', 'C3.importe IMP3'))
                ->from('SiafcaIntranetBundle:Liquidacion', 'L')
                ->from('SiafcaIntranetBundle:Oficina', 'O')
                ->from('SiafcaIntranetBundle:Parametrizacion', 'TL')
                ->from('SiafcaIntranetBundle:Revista', 'R')
                ->from('SiafcaIntranetBundle:ConceptoItem', 'C1')
                ->from('SiafcaIntranetBundle:ConceptoItem', 'C2')
                ->from('SiafcaIntranetBundle:ConceptoItem', 'C3')
                ->from('SiafcaIntranetBundle:Aportante', 'AP')
                ->from('SiafcaIntranetBundle:Item','ITEM')
                ->where('ITEM.aportante = AP')
                ->andWhere('ITEM.liquidacion = L')
                ->andWhere('ITEM = C1.item')
                ->andWhere('ITEM = C2.item')
                ->andWhere('ITEM = C3.item')
                ->andWhere('C1.concepto = 61')
                ->andWhere('C2.concepto = 2')
                ->andWhere('C3.concepto = 3')
                ->andWhere('L.organismo = O')
                ->andWhere('AP.organismo = O')
                ->andWhere('AP.persona = :id')
                ->andWhere('R = AP.revista')
                ->andWhere('TL.clase = \'TIPO_LIQ\'')
                ->andWhere('TL = L.tipoLiq')
                ->addOrderBy('ANIO', 'DESC')
                ->addOrderBy('MES','DESC')
                ->setParameter('id', $persona);
        $query = $qb->getQuery();
        $query = $query->setHint('knp_paginator.count',$count);

        $personaEnt = $em->getRepository('SiafcaIntranetBundle:Persona')->find($persona);
        //$aportes = $query->getResult();
        $paginator  = $this->get('knp_paginator');

        $aportes = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20, array('distinct' => false));

        return $this->render('aporte/show.html.twig', array(
            'aportes' => $aportes,
            'persona' => $personaEnt
        ));

    }

}

