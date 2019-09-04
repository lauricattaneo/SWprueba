<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Familiar
 *
 * @ORM\Table(name="familiar"
 * , indexes={
 *      @ORM\Index(name="idx_familiar_1", columns={"familiar_id"}),
 *      @ORM\Index(name="idx_familiar_2", columns={"parametrizacion_id"}),
 *      @ORM\Index(name="idx_familiar_3", columns={"titular_id"}),
 *
 * }
 * , uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_familiar_1", columns={"titular_id","familiar_id"})
 * })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\FamiliarRepository")
 */
class Familiar
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
     * @var \DateTime
     *
     * @ORM\Column(name="fechaCarga", type="datetime")
     */
    private $fechaCarga;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaValidacion", type="oracledatetime",  nullable=true)
     */
    private $fechaValidacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaVencimiento", type="oracledatetime",  nullable=true)
     */
    private $fechaVencimiento;
    
    /**
     * @ORM\ManyToOne(targetEntity="Persona", inversedBy="familiares")
     * @ORM\JoinColumn(name="titular_id", referencedColumnName="id")
     */
    private $titular;
    
    /**
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumn(name="familiar_id", referencedColumnName="id")
     */
    private $familiar;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="parametrizacion_id", referencedColumnName="id")
     */
    private $parentezco;


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
     * Set fechaCarga
     *
     * @param \DateTime $fechaCarga
     *
     * @return Familiar
     */
    public function setFechaCarga($fechaCarga)
    {
        $this->fechaCarga = $fechaCarga;

        return $this;
    }

    /**
     * Get fechaCarga
     *
     * @return \DateTime
     */
    public function getFechaCarga()
    {
        return $this->fechaCarga;
    }

    /**
     * Set fechaValidacion
     *
     * @param \DateTime $fechaValidacion
     *
     * @return Familiar
     */
    public function setFechaValidacion($fechaValidacion)
    {
        $this->fechaValidacion = $fechaValidacion;

        return $this;
    }

    /**
     * Get fechaValidacion
     *
     * @return \DateTime
     */
    public function getFechaValidacion()
    {
        return $this->fechaValidacion;
    }

    /**
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     *
     * @return Familiar
     */
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }

    /**
     * Set titular
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Persona $titular
     *
     * @return Familiar
     */
    public function setTitular(\Caja\SiafcaIntranetBundle\Entity\Persona $titular = null)
    {
        $this->titular = $titular;

        return $this;
    }

    /**
     * Get titular
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Persona
     */
    public function getTitular()
    {
        return $this->titular;
    }

    /**
     * Set familiar
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Persona $familiar
     *
     * @return Familiar
     */
    public function setFamiliar(\Caja\SiafcaIntranetBundle\Entity\Persona $familiar = null)
    {
        $this->familiar = $familiar;

        return $this;
    }

    /**
     * Get familiar
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Persona
     */
    public function getFamiliar()
    {
        return $this->familiar;
    }

    /**
     * Set parentezco
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $parentezco
     *
     * @return Familiar
     */
    public function setParentezco(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $parentezco = null)
    {
        $this->parentezco = $parentezco;

        return $this;
    }

    /**
     * Get parentezco
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getParentezco()
    {
        return $this->parentezco;
    }
}
