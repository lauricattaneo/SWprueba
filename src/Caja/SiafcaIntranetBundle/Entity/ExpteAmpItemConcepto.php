<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * ExpteAmpItemConcepto
 *
 * @ORM\Table(name="expte_amp_item_concepto",
 * indexes={
 *   @ORM\Index(name="idx_expte_amp_item_concepto_1", columns={"concepto_id"}),
 *   @ORM\Index(name="idx_expte_amp_item_concepto_2", columns={"expte_amparo_item_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\RevistaRepository")
 */

class ExpteAmpItemConcepto
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupExpteAmpItemConcepto"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupExpteAmpItemConcepto"})
     */
    private $descripcion;

    /**
     * @var oracledatetime
     *
     * @ORM\Column(name="fechaInicio", type="oracledatetime")
     * @Groups({"groupExpteAmpItemConcepto"})
     */
    private $fechaInicio;

    /**
     * @var oracledatetime
     *
     * @ORM\Column(name="fechaFin", type="oracledatetime", nullable=true)
     * @Groups({"groupExpteAmpItemConcepto"})
     */
    private $fechaFin;

    /**
     * @var float
     *
     * @ORM\Column(name="porcentaje", type="float")
     * @Groups({"groupExpteAmpItemConcepto"})
     */
    private $porcentaje;
    
    /**
     * @ORM\ManyToOne(targetEntity="ExpteAmpItem", inversedBy="itemConceptos")
     * @ORM\JoinColumn(name="expte_amparo_item_id", referencedColumnName="id")
     */
    private $amparoItem;
    
    /**
     * @ORM\ManyToOne(targetEntity="Concepto", inversedBy="itemConceptos")
     * @ORM\JoinColumn(name="concepto_id", referencedColumnName="id")
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return ExpteAmpItemConcepto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechaInicio
     *
     * @param oracledatetime $fechaInicio
     *
     * @return ExpteAmpItemConcepto
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return oracledatetime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param oracledatetime $fechaFin
     *
     * @return ExpteAmpItemConcepto
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return oracledatetime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set porcentaje
     *
     * @param float $porcentaje
     *
     * @return ExpteAmpItemConcepto
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return float
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set amparoItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparoItem
     *
     * @return ExpteAmpItemConcepto
     */
    public function setAmparoItem(\Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparoItem = null)
    {
        $this->amparoItem = $amparoItem;

        return $this;
    }

    /**
     * Get amparoItem
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem
     */
    public function getAmparoItem()
    {
        return $this->amparoItem;
    }

    /**
     * Set concepto
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Concepto $concepto
     *
     * @return ExpteAmpItemConcepto
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
    }
