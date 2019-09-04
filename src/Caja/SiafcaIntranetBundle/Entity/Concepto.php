<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Concepto
 *
 * @ORM\Table(name="concepto"
 * 
 * , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_concepto_1", columns={"codigo"}),
 *     @ORM\UniqueConstraint(name="uk_concepto_2", columns={"orden"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ConceptoRepository")
 */

class Concepto
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupConcepto"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, unique=true)
     * @Groups({"groupConcepto"})
     */
    private $codigo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="orden", type="string", length=4, unique=true, nullable=true)
     */
    private $orden;


    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=true)
     * @Groups({"groupConcepto"})
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="importe", type="decimal", precision=5, scale=2, nullable=true)
     * @Groups({"groupConcepto"})
     */
    private $importe;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje", type="decimal", precision=5, scale=2, nullable=true)
     * @Groups({"groupConcepto"})
     */
    private $porcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupConcepto"})
     */
    private $descripcion;
    
    /**
     * @var boolean
     * @ORM\Column(name="obligatorio", type="boolean", nullable=true, options = {"default": false})
     * @Groups({"groupConcepto"})
     */
    private $obligatorio;
    
    /**
    * @ORM\OneToMany(targetEntity="ExpteAmpItemConcepto", mappedBy="concepto")
    * @Groups({"groupConcepto"})
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
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Concepto
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Concepto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set importe
     *
     * @param string $importe
     *
     * @return Concepto
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
     * @return Concepto
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Concepto
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
     * Set obligatorio
     *
     * @param boolean $obligatorio
     *
     * @return Concepto
     */
    public function setObligatorio($obligatorio)
    {
        $this->obligatorio = $obligatorio;

        return $this;
    }

    /**
     * Get obligatorio
     *
     * @return boolean
     */
    public function getObligatorio()
    {
        return $this->obligatorio;
    }
    
    public function __toString() {
        return $this->nombre;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->itemConceptos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add itemConcepto
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItemConcepto $itemConcepto
     *
     * @return Concepto
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
    
    public function isRemunerativo()
    {
        return $this->codigo == '50';
    }
    
    public function isNoRemunerativo()
    {
        return $this->codigo == '51';
    }
    
    public function isAportePersonal()
    {
        return $this->codigo == '01';
    }
    
    public function isAportePatronal()
    {
        return $this->codigo == '02';
    }

    /**
     * Set orden
     *
     * @param string $orden
     *
     * @return Concepto
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return string
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
