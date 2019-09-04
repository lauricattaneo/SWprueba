<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presentacion
 *
 * @ORM\Table(name="presentacion",
 * indexes={
 *         @ORM\Index(name="idx_presentacion_1", columns={"usuario_id"}),
 *          })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\PresentacionRepository")
 */

class Presentacion
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
     * @ORM\Column(name="fechaPresentacion", type="oracledatetime")
     */
    private $fechaPresentacion;
    
    /**
     * @ORM\OneToOne(targetEntity="Liquidacion", inversedBy="presentacion")
     * @ORM\JoinColumn(name="liquidacion_id", referencedColumnName="id", nullable=false)
     */
    private $liquidacion;
    
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="presentaciones")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

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
     * Set fechaPresentacion
     *
     * @param \DateTime $fechaPresentacion
     *
     * @return Presentacion
     */
    public function setFechaPresentacion($fechaPresentacion)
    {
        $this->fechaPresentacion = $fechaPresentacion;

        return $this;
    }

    /**
     * Get fechaPresentacion
     *
     * @return \DateTime
     */
    public function getFechaPresentacion()
    {
        return $this->fechaPresentacion;
    }

    /**
     * Set liquidacion
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion
     *
     * @return Presentacion
     */
    public function setLiquidacion(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion)
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
     * Set usuario
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Usuario $usuario
     *
     * @return Presentacion
     */
    public function setUsuario(\Caja\SiafcaIntranetBundle\Entity\Usuario $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
