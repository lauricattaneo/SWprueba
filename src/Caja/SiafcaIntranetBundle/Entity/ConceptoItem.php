<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

// Se quito el indice idx_concepto_item_1, por recomendacion de la sgt:
// Es redundante con el uk_concepto_item_1
// Pero Doctrine lo necesita. Si lo saco, crea otro automáticamente
/**
 * ConceptoItem
 *
 * @ORM\Table(name="concepto_item",
 * indexes={
 *    @ORM\Index(name="idx_concepto_item_2", columns={"concepto_id"}), 
 *    @ORM\Index(name="idx_concepto_item_1", columns={"item_id"}),
 * }
 * , uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_concepto_item_1", columns={"item_id", "concepto_id"}) 
 * })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ConceptoItemRepository")
 */

class ConceptoItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupConceptoItem"})
     */
    private $id;
    
     /**
     * @ORM\ManyToOne(targetEntity="Concepto")
     * @ORM\JoinColumn(name="concepto_id", referencedColumnName="id")
     * @Groups({"groupConceptoItem"})
     */
    private $concepto;
    
    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="conceptosItem")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * @Groups({"groupConceptoItem1"})
     */
    private $item;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="decimal", precision=12, scale=2, nullable=false)
     * @Groups({"groupConceptoItem"})
     * 
     */
    private $importe;
    
    /**
     * @var float
     * @ORM\Column(name="porcentaje", type="decimal", precision=6, scale=2, nullable=true)
     * @Groups({"groupConceptoItem"})
     */
    private $porcentaje;
    
    /**
    * @ORM\OneToMany(targetEntity="Control", mappedBy="conceptoItem", cascade= {"persist","remove"})
    */
    private $controles;
    
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
     * Set concepto
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Concepto $concepto
     *
     * @return ConceptoItem
     */
    public function setConcepto(\Caja\SiafcaIntranetBundle\Entity\Concepto $concepto = null)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Concepto
     */
    public function getConcepto()
    {
        return $this->concepto;
    }


    /**
     * Set item
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Item $item
     *
     * @return ConceptoItem
     */
    public function setItem(\Caja\SiafcaIntranetBundle\Entity\Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set importe
     *
     * @param string $importe
     *
     * @return ConceptoItem
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set porcentaje
     *
     * @param string $porcentaje
     *
     * @return ConceptoItem
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return string
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->controles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add controle
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Control $controle
     *
     * @return ConceptoItem
     */
    public function addControle(\Caja\SiafcaIntranetBundle\Entity\Control $controle)
    {
        $this->controles[] = $controle;

        return $this;
    }

    /**
     * Remove controle
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Control $controle
     */
    public function removeControle(\Caja\SiafcaIntranetBundle\Entity\Control $controle)
    {
        $this->controles->removeElement($controle);
    }

    /**
     * Get controles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControles()
    {
        return $this->controles;
    }
    
    /**
     * Actualiza los valores de importe y porcentaje
     * @param array[] $values
     */
    public function updateValues($values) 
    {
        if ($this->importe != $values['importe']) {
            $this->importe =  (float)$values['importe'];
        }
        if($this->porcentaje != $values['porcentaje']) {
            $this->porcentaje = (float)$values['porcentaje'];
        }
    }
    
    /**
     * 
     * @param \Caja\SiafcaIntranetBundle\Entity\ConceptoItem $conceptoItem
     * @return \Caja\SiafcaIntranetBundle\Entity\ConceptoItem
     */
    public function copyData(ConceptoItem $conceptoItem)
    {
        $this->concepto = $conceptoItem->getConcepto();
        $this->importe = $conceptoItem->getImporte();
        $this->porcentaje = $conceptoItem->getPorcentaje();
        
        return $this;
    }
}
