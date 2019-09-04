<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExpteAmpItem
 *
 * @ORM\Table(name="expte_amp_item",
 * indexes={
*     @ORM\Index(name="idx_expte_amp_item_1", columns={"aportante_id"}),
 *    @ORM\Index(name="idx_expte_amp_item_2", columns={"expte_amparo_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ExpteAmpItemRepository")
 */

class ExpteAmpItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Aportante", inversedBy="amparos")
     * @ORM\JoinColumn(name="aportante_id", referencedColumnName="id")
     */
    private $aportante;
    
    /**
     * @ORM\ManyToOne(targetEntity="ExpteAmparo", inversedBy="amparoItems")
     * @ORM\JoinColumn(name="expte_amparo_id", referencedColumnName="id")
     */
    private $expteAmparo;
    
    /**
    * @ORM\OneToMany(targetEntity="ExpteAmpItemConcepto", mappedBy="amparoItem")
    */
    private $itemConceptos;

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
        $this->itemConceptos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set aportante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Aportante $aportante
     *
     * @return ExpteAmpItem
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
     * Set expteAmparo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmparo $expteAmparo
     *
     * @return ExpteAmpItem
     */
    public function setExpteAmparo(\Caja\SiafcaIntranetBundle\Entity\ExpteAmparo $expteAmparo = null)
    {
        $this->expteAmparo = $expteAmparo;

        return $this;
    }

    /**
     * Get expteAmparo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\ExpteAmparo
     */
    public function getExpteAmparo()
    {
        return $this->expteAmparo;
    }

    /**
     * Add itemConcepto
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItemConcepto $itemConcepto
     *
     * @return ExpteAmpItem
     */
    public function addItemConcepto(\Caja\SiafcaIntranetBundle\Entity\ExpteAmpItemConcepto $itemConcepto)
    {
        $this->itemConceptos[] = $itemConcepto;

        return $this;
    }

    /**
     * Remove itemConcepto
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItemConcepto $itemConcepto
     */
    public function removeItemConcepto(\Caja\SiafcaIntranetBundle\Entity\ExpteAmpItemConcepto $itemConcepto)
    {
        $this->itemConceptos->removeElement($itemConcepto);
    }

    /**
     * Get itemConceptos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItemConceptos()
    {
        return $this->itemConceptos;
    }
}
