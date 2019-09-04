<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Caja\SiafcaIntranetBundle\Entity\ConceptoItem;

/**
 * Item
 *
 * @ORM\Table(name="item",
 * indexes={
 *    @ORM\Index(name="idx_item_1", columns={"liquidacion_id"}),
 *    @ORM\Index(name="idx_item_2", columns={"aportante_id"}),
 * } 
 * , uniqueConstraints={
 *    @ORM\UniqueConstraint(name="uk_item_1", columns={"liquidacion_id", "aportante_id"})
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ItemRepository")
 */

class Item
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupItem", "groupItem1"})
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Aportante", inversedBy="items")
     * @ORM\JoinColumn(name="aportante_id", referencedColumnName="id")
     * @Groups({"groupItem", "groupItem1"})
     */
    private $aportante;
    
    /**
     * @ORM\ManyToOne(targetEntity="Liquidacion", inversedBy="items")
     * @ORM\JoinColumn(name="liquidacion_id", referencedColumnName="id")
     */
    private $liquidacion;
    
    /**
    * @ORM\OneToMany(targetEntity="ConceptoItem", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
    * @Groups({"groupItem"})
    */
    private $conceptosItem;
    
    /**
     * @var string
     *
     * @ORM\Column(name="antiguedad", type="string", length=4, nullable=true)
     * @Groups({"groupItem"})
     */
    private $antiguedad;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conceptosItem = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set aportante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Aportante $aportante
     *
     * @return Item
     */
    public function setAportante(\Caja\SiafcaIntranetBundle\Entity\Aportante $aportante = null)
    {
        $this->aportante = $aportante;

        return $this;
    }

    /**
     * Get aportante
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Aportante
     */
    public function getAportante()
    {
        return $this->aportante;
    }

    /**
     * Set liquidacion
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion
     *
     * @return Item
     */
    public function setLiquidacion(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion = null)
    {
        $this->liquidacion = $liquidacion;

        return $this;
    }

    /**
     * Get liquidacion
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Liquidacion
     */
    public function getLiquidacion()
    {
        return $this->liquidacion;
    }

    /**
     * Add conceptosItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ConceptoItem $conceptosItem
     *
     * @return Item
     */
    public function addConceptosItem(\Caja\SiafcaIntranetBundle\Entity\ConceptoItem $conceptosItem)
    {
        $conceptosItem->setItem($this);
        $this->conceptosItem[] = $conceptosItem;

        return $this;
    }

    /**
     * Remove conceptosItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ConceptoItem $conceptosItem
     */
    public function removeConceptosItem(\Caja\SiafcaIntranetBundle\Entity\ConceptoItem $conceptosItem)
    {
        $this->conceptosItem->removeElement($conceptosItem);
    }

    /**
     * Get conceptosItem
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConceptosItem()
    {
        $iterator = $this->conceptosItem->getIterator();
        $iterator->uasort(function($a,$b){
        return ($a->getConcepto()->getOrden() < $b->getConcepto()->getOrden()) ? -1 : 1;});
        $collection = new \Doctrine\Common\Collections\ArrayCollection(iterator_to_array($iterator));
        return $collection;
    }
    
    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $conceptosItem
     * @return \Caja\SiafcaIntranetBundle\Entity\Item
     */
    public function updateConceptosItem($conceptosItem)
    {
        $this->conceptosItem->clear();
        foreach ($conceptosItem as $conceptoItem) {
            $this->addConceptosItem($conceptoItem);
        }
        
        return $this;
    }
    
    /**
     * 
     * @param \Caja\SiafcaIntranetBundle\Entity\Item $item
     * @return \Caja\SiafcaIntranetBundle\Entity\Item
     */
    public function copyData(Item $item)
    {
        $this->aportante = $item->getAportante();
        foreach ($item->getConceptosItem() as $conceptoItem) {
            $newConceptoItem = new ConceptoItem();
            $newConceptoItem->copyData($conceptoItem);
            $this->addConceptosItem($newConceptoItem);
        }
        
        return $this;
    }

    /**
     * Set antiguedad
     *
     * @param string $antiguedad
     *
     * @return Item
     */
    public function setAntiguedad($antiguedad)
    {
        $this->antiguedad = $antiguedad;

        return $this;
    }

    /**
     * Get antiguedad
     *
     * @return string
     */
    public function getAntiguedad()
    {
        return $this->antiguedad;
    }
}
