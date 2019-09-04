<?php

namespace Caja\SiafcaIntranetBundle\Repository;

use Caja\SiafcaIntranetBundle\Entity\Item;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * 
     * @param array[] $itemArray
     * @return Item
     */
    public function crearItem($itemArray) {
        $em = $this->getEntityManager();
        $aportante = $em->getRepository('SiafcaIntranetBundle:Aportante')->find($itemArray['aportante']['id']);
        $item = new Item();
        $item->setAportante($aportante);
        foreach ($itemArray['conceptosItem'] as $conceptoItemArray) {
            $conceptoItem = $em->getRepository('SiafcaIntranetBundle:ConceptoItem')->crearConceptoItem($conceptoItemArray);
            $item->addConceptosItem($conceptoItem);
        }

        return $item;
    }

    /**
     * 
     * @param array[] $itemArray
     * @return Item
     */
    public function updateOrCreateItem($itemArray) {
        $em = $this->getEntityManager();
        if (!$itemArray['id']) {
            return $this->crearItem($itemArray);
        }
        $item = $em->getRepository('SiafcaIntranetBundle:Item')->find($itemArray['id']);
        $conceptoItemRepository = $em->getRepository('SiafcaIntranetBundle:ConceptoItem');
        $conceptosItem = new ArrayCollection();
        foreach ($itemArray['conceptosItem'] as $conceptoItemArray) {
            $conceptoItem = $conceptoItemRepository->updateOrCreateConceptoItem($conceptoItemArray);
            $conceptosItem->add($conceptoItem);
        }
        $item->updateConceptosItem($conceptosItem);

        return $item;
    }

    /**
     * 
     * @param array[] $itemArray
     * @return Item
     */
    public function getItemsByLiquidacion($liquidacionId) {
        $em = $this->getEntityManager();
        $dql = 'SELECT partial i.{id}'
                . 'FROM SiafcaIntranetBundle:Item i'
                . ' JOIN i.liquidacion l'
                . ' JOIN i.aportante a'
                . ' JOIN a.persona p'
                . ' WHERE l.id = :idLiq'
                . ' ORDER BY p.apellidoPat,p.apellidoMat, p.nombre';
        $query = $em->createQuery($dql)->setParameter('idLiq', $liquidacionId);
        return $query;
       
    }

}
