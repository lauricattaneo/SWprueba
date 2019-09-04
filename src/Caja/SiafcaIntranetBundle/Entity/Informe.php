<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Informe
 *
 * @ORM\Table(name="informe")
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\InformeRepository")
 */

class Informe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupInforme"})
     */
    private $id;
    
    /**
    * @var int
    *
    * @ORM\Column(name="cantidadAportantes", type="integer", nullable=false)
    * @Groups({"groupInforme"})
    */
    private $cantidadAportantes;

       
    /**
    * @ORM\OneToMany(targetEntity="InformeItem", mappedBy="informe", cascade="persist")
    * @Groups({"groupInforme"})
    */
    private $informeItems;
    
    /**
     * @ORM\OneToOne(targetEntity="Liquidacion", inversedBy="informe")
     * @ORM\JoinColumn(name="liquidacion_id", referencedColumnName="id", nullable=false)
     * @Groups({"groupInforme"})
     */
    private $liquidacion;


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
     * Set cantidadAportantes
     *
     * @param integer $cantidadAportantes
     *
     * @return Informe
     */
    public function setCantidadAportantes($cantidadAportantes)
    {
        $this->cantidadAportantes = $cantidadAportantes;

        return $this;
    }

    /**
     * Get cantidadAportantes
     *
     * @return integer
     */
    public function getCantidadAportantes()
    {
        return $this->cantidadAportantes;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->informeItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add informeItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\InformeItem $informeItem
     *
     * @return Informe
     */
    public function addInformeItem(\Caja\SiafcaIntranetBundle\Entity\InformeItem $informeItem)
    {
        $this->informeItems[] = $informeItem;

        return $this;
    }

    /**
     * Remove informeItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\InformeItem $informeItem
     */
    public function removeInformeItem(\Caja\SiafcaIntranetBundle\Entity\InformeItem $informeItem)
    {
        $this->informeItems->removeElement($informeItem);
    }

    /**
     * Get informeItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInformeItems()
    {
        $iterator = $this->informeItems->getIterator();
        $iterator->uasort(function($a,$b){
        return ($a->getConcepto()->getOrden() < $b->getConcepto()->getOrden()) ? -1 : 1;});
        $collection = new \Doctrine\Common\Collections\ArrayCollection(iterator_to_array($iterator));
        return $collection;
    }

    /**
     * Set liquidacion
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion
     *
     * @return Informe
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
    
    public function getRemunerativo()
    {
        foreach ($this->informeItems as $item) {
            if ($item->getConcepto()->isRemunerativo()) {
                return $item;
            }
        }
        
        return null;
    }
    
    public function getNoRemunerativo()
    {
        foreach ($this->informeItems as $item) {
            if ($item->getConcepto()->isNoRemunerativo()) {
                return $item;
            }
        }
        
        return null;
    }
}
