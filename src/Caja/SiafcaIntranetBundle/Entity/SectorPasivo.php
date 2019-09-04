<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * SectorPasivo
 *
 * @ORM\Table(name="sector_pasivo"
 * , uniqueConstraints={
 *    @ORM\UniqueConstraint(name="uk_sector_pasivo_1", columns={"codigo"}),
 *    @ORM\UniqueConstraint(name="uk_sector_pasivo_2", columns={"nombre"}),
 *
 * })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\SectorPasivoRepository")
 */

class SectorPasivo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupSectorPasivo","groupCargo"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, unique=true)
     * @Groups({"groupSectorPasivo","groupCargo"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     * @Groups({"groupSectorPasivo","groupCargo"})
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="comentarios", type="string", length=255, nullable=true)
     * @Groups({"groupSectorPasivo","groupCargo"})
     */
    private $comentarios;


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
     * @return SectorPasivo
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
     * @return SectorPasivo
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
     * Set comentarios
     *
     * @param string $comentarios
     *
     * @return SectorPasivo
     */
    public function setComentarios($comentarios)
    {
        $this->comentarios = $comentarios;

        return $this;
    }

    /**
     * Get comentarios
     *
     * @return string
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }
    
    public function __toString() {
        return $this->nombre;
    }
}
