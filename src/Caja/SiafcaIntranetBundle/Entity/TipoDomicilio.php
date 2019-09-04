<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoDomicilio
 *
 * @ORM\Table(name="tipo_domicilio"
 * , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_tipo_domicilio_1", columns={"descripcion"}),
 *  })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\TipoDomicilioRepository")
 */

class TipoDomicilio
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
     * @ORM\Column(name="descripcion", type="string", length=100, unique=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="comentario", type="string", length=255, nullable=true)
     */
    private $comentario;


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
     * @return TipoDomicilio
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
     * Set comentario
     *
     * @param string $comentario
     *
     * @return TipoDomicilio
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
    }
    
    public function __toString() {
        return $this->descripcion;
    }
    
    public function isTipoDomicilioReal()
    {
        if ($this->id == 1)
        {
            return true;
        }
        
        return false;
    }
}
