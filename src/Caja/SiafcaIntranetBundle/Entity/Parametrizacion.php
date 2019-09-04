<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Parametrizacion
 *
 * @ORM\Table(name="parametrizacion")
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ParametrizacionRepository")
 */

class Parametrizacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupParametrizacion"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="clase", type="string", length=50, nullable=true)
     * @Groups({"groupParametrizacion"})
     */
    private $clase;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=10, nullable=true)
     * @Groups({"groupParametrizacion"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupParametrizacion"})
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=true)
     * @Groups({"groupParametrizacion"})
     */
    private $nombre;

    /**
     * @var string
     * @ORM\Column(name="valido", type="string", nullable=true, length=1)
     * @Groups({"groupParametrizacion"})
     */
    private $valido;


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
     * Set clase
     *
     * @param string $clase
     *
     * @return Parametrizacion
     */
    public function setClase($clase)
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * Get clase
     *
     * @return string
     */
    public function getClase()
    {
        return $this->clase;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Parametrizacion
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Parametrizacion
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Parametrizacion
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
    
    public function __toString() {
        return $this->getCodigo().': '.$this->nombre;
    }
    
    function getValido() {
        return $this->valido;
    }

    function setValido($valido) {
        $this->valido = $valido;
    }


}
