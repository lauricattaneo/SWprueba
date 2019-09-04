<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transicion
 *
 * @ORM\Table(name="transicion"
 * , indexes={
 *     @ORM\Index(name="idx_transicion_1", columns={"final_id"}),
 *     @ORM\Index(name="idx_transicion_2", columns={"inicial_id"}), 
 *  }
 * , uniqueConstraints={
 *    @ORM\UniqueConstraint(name="uk_transicion_1", columns={"clase","codigo"}),
 *
 * })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\TransicionRepository")
 */

class Transicion
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
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=15, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="clase", type="string", length=30, nullable=false)
     */
    private $clase;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;
    
    
      /**
     * @ORM\ManyToOne(targetEntity="Estado", inversedBy="transiciones")
     * @ORM\JoinColumn(name="inicial_id", referencedColumnName="id", nullable=false)
     */
    private $estado_inicial;
    
      /**
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumn(name="final_id", referencedColumnName="id", nullable=false)
     */
    private $estado_final;


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
     * @return Transicion
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
     * @return Transicion
     */
    public function setClase($nombre)
    {
        $this->clase = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getClase()
    {
        return $this->clase;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Transicion
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
     * Set estadoInicial
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Estado $estadoInicial
     *
     * @return Transicion
     */
    public function setEstadoInicial(\Caja\SiafcaIntranetBundle\Entity\Estado $estadoInicial = null)
    {
        $this->estado_inicial = $estadoInicial;

        return $this;
    }

    /**
     * Get estadoInicial
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Estado
     */
    public function getEstadoInicial()
    {
        return $this->estado_inicial;
    }

    /**
     * Set estadoFinal
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Estado $estadoFinal
     *
     * @return Transicion
     */
    public function setEstadoFinal(\Caja\SiafcaIntranetBundle\Entity\Estado $estadoFinal = null)
    {
        $this->estado_final = $estadoFinal;

        return $this;
    }

    /**
     * Get estadoFinal
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Estado
     */
    public function getEstadoFinal()
    {
        return $this->estado_final;
    }
    
    public function isTransicion($codigo,$clase)
    {
        return ($this->codigo == $codigo && $this->clase == $clase);
    }
}
