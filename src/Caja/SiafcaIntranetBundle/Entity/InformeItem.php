<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * InformeItem
 *
 * @ORM\Table(name="informe_item",
 * indexes={
 *    @ORM\Index(name="idx_informe_item_1", columns={"informe_id"}),
 *    @ORM\Index(name="idx_informe_item_2", columns={"concepto_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\InformeItemRepository")
 */

class InformeItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupInformeItem"})
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="decimal", precision=12, scale=2, nullable=false)
     * @Groups({"groupInformeItem"})
     */
    private $importeTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=false)
     * @Groups({"groupInformeItem"})
     */
    private $cantidad;
    
    /**
     * @ORM\ManyToOne(targetEntity="Informe", inversedBy="informeItems")
     * @ORM\JoinColumn(name="informe_id", referencedColumnName="id")
     */
    private $informe;
    
    /**
     * @ORM\ManyToOne(targetEntity="Concepto")
     * @ORM\JoinColumn(name="concepto_id", referencedColumnName="id")
     * @Groups({"groupInformeItem"})
     */
    private $concepto;


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
     * Set total
     *
     * @param string $total
     *
     * @return InformeItem
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return InformeItem
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set informe
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Informe $informe
     *
     * @return InformeItem
     */
    public function setInforme(\Caja\SiafcaIntranetBundle\Entity\Informe $informe = null)
    {
        $this->informe = $informe;

        return $this;
    }

    /**
     * Get informe
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Informe
     */
    public function getInforme()
    {
        return $this->informe;
    }

    /**
     * Set importeTotal
     *
     * @param string $importeTotal
     *
     * @return InformeItem
     */
    public function setImporteTotal($importeTotal)
    {
        $this->importeTotal = $importeTotal;

        return $this;
    }

    /**
     * Get importeTotal
     *
     * @return string
     */
    public function getImporteTotal()
    {
        return $this->importeTotal;
    }

    /**
     * Set concepto
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Concepto $concepto
     *
     * @return InformeItem
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

    public function getOrden(){
        return $this->concepto->getOrden();
    }
}
