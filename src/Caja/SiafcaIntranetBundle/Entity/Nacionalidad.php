<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Nacionalidad
 *
 * @ORM\Table(name="nacionalidad",
 * uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_nacionalidad_1", columns={"codigo"}),
 *      @ORM\UniqueConstraint(name="uk_nacionalidad_2", columns={"descripcion"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\NacionalidadRepository")
 */

class Nacionalidad
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Groups({"groupNacionalidad"})
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=3, unique=true)
     * @Groups({"groupNacionalidad"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, unique=true)
     * @Groups({"groupNacionalidad"})
     */
    private $descripcion;


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
     * @return Nacionalidad
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
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Nacionalidad
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
    
    public function __toString() {
        return $this->descripcion;
    }
}
